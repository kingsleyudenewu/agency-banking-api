<?php

namespace App\Http\Controllers\Api\Agent;

use App\Events\AgentAccountCreated;
use App\Http\Controllers\APIBaseController;
use App\Http\Requests\CreateAgentRequest;
use App\Koloo\User;
use App\Http\Resources\User as UserTransformer;
use App\Traits\LogTrait;


/**
 * Class CreateAgentController
 *
 * @package \App\Http\Controllers\Api\Agent
 */
class CreateAgentController extends APIBaseController
{

    use LogTrait;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
         $this->logChannel = 'CreateAgent';

        $this->middleware(function ($request, $next) {
            $user = User::find($request->user()->id);

            if($user->canManageAgent())
            {
                return $next($request);
            }

            return $this->errorResponse('Access denied');

        })->only(['store']);

    }

    public function store(CreateAgentRequest $request)
    {
        $this->logInfo('Creating account ..');

        $data = $request->validated();

        $authUser = User::find($request->user()->id);

        $parent = $authUser->isAdmin() ? User::rootUser()->getModel() : $request->user();
        $user =  User::createWithProfile($data, $parent);

        if(!$user)
        {
            return $this->errorResponse('Unable to create account. Try again.');
        }

        $method = ($authUser->isAdmin() && request('type') === 'super') ? 'setAsSuperAgent' : 'setAsAgent';
        $user->getModel()->$method();

        if($authUser->isAdmin())
            $user->approve($authUser->getId(), 'auto approved');

        event(new AgentAccountCreated($user));

        $this->logInfo('Done creating account ..');

        return $this->successResponse('OK', new UserTransformer($user->getModel()));

    }

}

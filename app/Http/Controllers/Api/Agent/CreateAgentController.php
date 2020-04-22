<?php

namespace App\Http\Controllers\Api\Agent;

use App\Events\AgentAccountCreated;
use App\Http\Controllers\APIBaseController;
use App\Http\Requests\CreateAgentRequest;
use App\Koloo\User;
use App\Http\Resources\User as UserTransformer;
use Illuminate\Support\Str;


/**
 * Class CreateAgentController
 *
 * @package \App\Http\Controllers\Api\Agent
 */
class CreateAgentController extends APIBaseController
{

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

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

        $data = $request->validated();
        $data['password'] = Str::random(30);

        $authUser = User::find($request->user()->id);
        $user =  User::createWithProfile($data, $request->user());

        $method = ($authUser->isAdmin() && request('type') === 'super') ? 'setAsSuperAgent' : 'setAsAgent';

        $user->$method();

        event(new AgentAccountCreated($user));

        return $this->successResponse('OK', new UserTransformer($user));

    }

}

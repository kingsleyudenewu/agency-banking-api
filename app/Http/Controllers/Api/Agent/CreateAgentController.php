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

    public function store(CreateAgentRequest $request)
    {

        $data = $request->validated();
        $data['password'] = Str::random(30);

        $user =  User::createWithProfile($data);

        event(new AgentAccountCreated($user));

        return $this->successResponse('OK', new UserTransformer($user));

    }

}

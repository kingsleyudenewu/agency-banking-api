<?php

namespace App\Http\Controllers\Api\Customer;

use App\Events\AgentAccountCreated;
use App\Http\Controllers\APIBaseController;
use App\Http\Requests\CreateAgentRequest;
use App\Http\Resources\User as UserTransformer;
use App\Koloo\User;
use App\Traits\LogTrait;


/**
 * Class CustomerController
 *
 * @package \App\Http\Controllers\Api\Customer
 */
class CustomerController extends APIBaseController
{
    use LogTrait;

    public function __construct()
    {
        $this->logChannel = 'CustomerController';
    }

    public function store(CreateAgentRequest $request)
    {

        $this->logInfo('Creating customer account ..');

        $path = settings('document_storage_path');
        $disk = settings('document_storage_driver');

        $data = $request->validated();

        $idKey = 'means_of_identification';

        if($request->hasFile($idKey))
        {
            $storedLocation = $request->file($idKey)->store($path, $disk);
            $data[$idKey] = [
                'disk' => $disk,
                'path' => $storedLocation
            ];

        }

        $user =  User::createWithProfile($data, $request->user());

        if(!$user)
        {
            return $this->errorResponse('Unable to create account. Try again.');
        }

        $user->getModel()->setAsCustomer();

        event(new AgentAccountCreated($user));

        $this->logInfo('Done customer creating account ..');

        return $this->successResponse('OK', new UserTransformer($user->getModel()));
    }
}

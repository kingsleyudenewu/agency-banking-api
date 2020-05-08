<?php

namespace App\Http\Controllers\Api\Customer;

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

        $storedLocation = $request->file('passport_photo')->store($path, $disk);
        $data['passport_photo'] = [
            'disk' => $disk,
            'path' => $storedLocation
        ];

        $user =  User::createWithProfile($data, $request->user());

        if(!$user)
        {
            return $this->errorResponse('Unable to create account. Try again.');
        }

        $user->getModel()->setAsCustomer();

        $this->logInfo('Done customer creating account ..');

        return $this->successResponse('OK', new UserTransformer($user->getModel()));
    }
}

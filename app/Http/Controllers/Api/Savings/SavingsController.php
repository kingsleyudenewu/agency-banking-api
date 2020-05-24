<?php

namespace App\Http\Controllers\Api\Savings;

use App\Events\PreSavingCreated;
use App\Http\Controllers\APIBaseController;
use App\Http\Requests\CreateSavingsRequest;
use App\Koloo\User;

/**
 * Class SavingsController
 *
 * @package \App\Http\Controllers\Api\Saving
 */
class SavingsController extends APIBaseController
{

    public function store(CreateSavingsRequest $request)
    {
        $data = $request->validated();
        $data['creator_id'] = $request->user()->id;

        event(new PreSavingCreated($data, request()->user()));

        try {

            $customer = User::find($data['owner_id']);
            User::checkExistence($customer);

            $saving = $customer->newSaving($data,  request()->user());

            return $this->successResponse('Success', $saving->money());
        } catch (\Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }

    }
}

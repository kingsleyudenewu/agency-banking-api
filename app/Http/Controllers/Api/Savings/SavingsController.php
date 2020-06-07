<?php

namespace App\Http\Controllers\Api\Savings;

use App\Events\PreSavingCreated;
use App\Http\Controllers\APIBaseController;
use App\Http\Requests\ContributeRequest;
use App\Http\Requests\CreateSavingsRequest;
use App\Http\Resources\Saving;
use App\Koloo\User;
use App\Http\Resources\User as UserTransformer;
use Illuminate\Support\Facades\Log;

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

            return $this->successResponse('Success', new Saving($saving));

        } catch (\Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }

    }

    public function show()
    {

        try {
            $customer = User::search(request('q'), request('country_code'));
            User::checkExistence($customer);

            $res = [
                'customer' => new UserTransformer($customer->getModel()),
                'savings' => Saving::collection($customer->getSavings())
            ];


             return $this->successResponse('Savings', $res);
        }catch (\Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function contribute(ContributeRequest $request, $id)
    {
        try {
            $saving = \App\Saving::find($id);

            if(!$saving) throw new \Exception('Saving not found');

            if(!$saving->maturity || $saving->maturity->isPast())
            {
                throw new \Exception('Saving closed for new contribution');
            }


            $authUser = User::findByInstance(auth()->user());
            User::checkExistence($authUser);

            $amount =  intval(request('amount')) * 100;

            $res = $authUser->contributeToSaving($saving, $amount);

            return $this->successResponse('Savings', ['contribution' => $res, 'savingStat' => $res->savingPlan->stats()]);

        }catch (\Exception $e)
        {
            Log::error('FAILED_REQUEST: '  .  $e->getMessage() . ' File:  ' . $e->getFile()  . ' on line ' . $e->getLine());
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getContributions($id)
    {
        try {
            $saving = \App\Saving::find($id);

            if(!$saving) throw new \Exception('Saving not found');

            return $this->successResponse('contributions', $saving->contributions);

        }catch (\Exception $e)
        {
            Log::error('FAILED_REQUEST: '  .  $e->getMessage() . ' File:  ' . $e->getFile()  . ' on line ' . $e->getLine());
            return $this->errorResponse($e->getMessage());
        }
    }
}

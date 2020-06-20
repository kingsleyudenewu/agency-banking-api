<?php

namespace App\Http\Controllers\Api\Savings;

use App\Events\PreSavingCreated;
use App\Http\Controllers\APIBaseController;
use App\Http\Requests\ContributeRequest;
use App\Http\Requests\CreateSavingsRequest;
use App\Http\Resources\Saving;
use App\Koloo\User;
use App\Http\Resources\User as UserTransformer;
use App\Transaction;
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

            $customer->writeCreditTransaction($saving->amount, 'New contribution', Transaction::LABEL_CONTRIBUTION);

            return $this->successResponse('Success', new Saving($saving));

        } catch (\Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }

    }

    public function show()
    {

        $authUser = new User(request()->user());
        try {
            $customer = User::search(request('q'), request('country_code'));
            User::checkExistence($customer);

            if(!$authUser->isAdmin() && !$customer->isApproved()) throw new \Exception('Account has not been approved.');

            $res = [
                'customer' => new UserTransformer($customer->getModel()),
                'savings' => Saving::collection($customer->getSavings()),
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

            if($saving->hasContributedOn())
            {
                throw new \Exception('You recently have contributed to this saving, wait till tomorrow to add more. ');
            }


            $authUser = User::findByInstance(auth()->user());
            User::checkExistence($authUser);

            $amount =  request('amount');

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

            return $this->successResponse('contributions', $saving->contributions()->latest()->get());

        }catch (\Exception $e)
        {
            Log::error('FAILED_REQUEST: '  .  $e->getMessage() . ' File:  ' . $e->getFile()  . ' on line ' . $e->getLine());
            return $this->errorResponse($e->getMessage());
        }
    }
}

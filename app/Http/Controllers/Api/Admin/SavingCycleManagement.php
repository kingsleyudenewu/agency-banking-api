<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\APIBaseController;
use App\Http\Requests\CreateSavingCyclerRequest;
use App\Http\Requests\UpdateSavingCyclerRequest;
use App\SavingCycle;
use App\Http\Resources\SavingCycle as SavingCycleTransformer;

/**
 * Class SavingCycleManagement
 *
 * @package \App\Http\Controllers\Api\Admin
 */
class SavingCycleManagement extends APIBaseController
{

    public function index()
    {
        return $this->successResponse('SavingCycle', SavingCycleTransformer::collection(SavingCycle::get()));
    }


    public function store(CreateSavingCyclerRequest $request)
    {
        $data = $request->validated();
        $data['charge_type'] = 'percent';

        if(SavingCycle::isFlatCharge($data['charge_type']))
            $data['percentage_to_charge'] = null;
        else
            $data['percentage_to_charge'] = number_format($data['percentage_to_charge'],2);

        $result = SavingCycle::create($data);

        return $this->successResponse('Created', new SavingCycleTransformer($result));
    }

    public function update(UpdateSavingCyclerRequest $request)
    {
        $data = $request->validated();
        if(SavingCycle::isFlatCharge($data['charge_type']))
            $data['percentage_to_charge'] = null;
        else
            $data['percentage_to_charge'] = number_format($data['percentage_to_charge'],2);

        $savingCycle = SavingCycle::find($data['id']);

        $savingCycle->update($data);

        return $this->successResponse('Updated', new SavingCycleTransformer($savingCycle));
    }
}

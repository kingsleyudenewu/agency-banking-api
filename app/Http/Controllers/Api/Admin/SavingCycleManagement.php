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

        if(SavingCycle::isFlatCharge($data['charge_type']))
            $data['percentage_to_charge'] = null;

        $result = SavingCycle::create($data);

        return $this->successResponse('Created', new SavingCycleTransformer($result));
    }

    public function update(UpdateSavingCyclerRequest $request)
    {
        $data = $request->validated();
        if(SavingCycle::isFlatCharge($data['charge_type']))
            $data['percentage_to_charge'] = null;

        $savingCycle = SavingCycle::find($data['id']);

        $savingCycle->update($data);

        return $this->successResponse('Updated', new SavingCycleTransformer($savingCycle));
    }
}

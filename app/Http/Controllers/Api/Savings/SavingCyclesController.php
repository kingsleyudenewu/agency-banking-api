<?php

namespace App\Http\Controllers\Api\Savings;

use App\Http\Controllers\APIBaseController;
use App\Http\Resources\SavingCycle;

/**
 * Class SavingCyclesController
 *
 * @package \App\Http\Controllers\Api\Savings
 */
class SavingCyclesController extends APIBaseController
{

    public function index()
    {
        return $this->successResponse('saving cycles', SavingCycle::collection(\App\SavingCycle::get()));
    }
}

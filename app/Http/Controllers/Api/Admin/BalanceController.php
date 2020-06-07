<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\BalanceUpdated;
use App\Http\Controllers\APIBaseController;
use App\Http\Requests\BalanceCreditAndDebitRequest;
use App\Koloo\User;
use Illuminate\Support\Facades\Log;

/**
 * Class BalanceController
 *
 * @package \App\Http\Controllers\Api\Admin
 */
class BalanceController extends APIBaseController
{

    public function store(BalanceCreditAndDebitRequest $request)
    {



    }

}

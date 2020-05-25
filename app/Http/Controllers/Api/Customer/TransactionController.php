<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\APIBaseController;
use App\Transaction;
use App\User;
use \EloquentBuilder;

/**
 * Class TransactionController
 *
 * @package \App\Http\Controllers\Api\Customer
 */
class TransactionController extends APIBaseController
{

    public function index()
    {
        $query = Transaction::query();

        if(!request()->user()->hasRole(User::ROLE_ADMIN))
            $query = request()->user()->transactions();

        $perPage = $this->perginationPerPage();

        return $this->successResponse('transactions',
            EloquentBuilder::to($query->with('owner:id,first_name,last_name'), request()->filter)->paginate($perPage)
        );
    }
}

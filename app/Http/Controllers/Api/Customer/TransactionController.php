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

        $user = request()->user();
        if(!$user->hasRole(User::ROLE_ADMIN))
        {
            $ids = $user->children->map(function($user){
                if(!$user->hasRole(User::ROLE_CUSTOMER))
                {
                    return $user->id;
                }
            });

            $ids[] = $user->id;
            $query = Transaction::query()
                ->whereIn('user_id', $ids);
        }
        else
        {
            $query = Transaction::query();
        }


        $perPage = $this->perginationPerPage();
        return $this->successResponse('transactions',
            EloquentBuilder::to($query->latest()->with('owner:id,name,phone'), request()->filter)->paginate($perPage)
        );
    }
}

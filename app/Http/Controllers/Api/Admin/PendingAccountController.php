<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\APIBaseController;
use App\User;
use EloquentBuilder;

/**
 * Class PendingAccountController
 *
 * @package \App\Http\Controllers\Api\Admin
 */
class PendingAccountController extends APIBaseController
{

    public function index()
    {
        $perPage = $this->perginationPerPage();

        $query =  User::pending()->latest()
            ->select(explode(',',User::SELECT_BASIC_INFO))
            ->whereHas('parent')
            ->with('parent:id,name,phone');

        return $this->successResponse('pending accounts',
            EloquentBuilder::to($query, request()->filter)->paginate($perPage)
        );
    }
}

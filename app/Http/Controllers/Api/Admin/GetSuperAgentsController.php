<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\APIBaseController;
use App\User;

/**
 * Class GetSuperAgentsController
 *
 * @package \App\Http\Controllers\Api\Admin
 */
class GetSuperAgentsController extends APIBaseController
{

    public function index()
    {
        $result = User::select('id', 'name')->whereHas('roles', function($q){
            $q->where('name', User::ROLE_SUPER_AGENT);
        })->get();

        return $this->successResponse('Super agents', $result);
    }

}

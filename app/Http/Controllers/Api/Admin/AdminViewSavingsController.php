<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\APIBaseController;
use App\Saving;
use EloquentBuilder;

/**
 * Class AdminViewSavingsController
 *
 * @package \App\Http\Controllers\Api\Admin
 */
class AdminViewSavingsController extends APIBaseController
{

    public function index()
    {
        $query = Saving::query();
        $query->with(['owner:id,name','creator:id,name', 'cycle:id,title'])->orderBy('maturity', 'desc');


        $perPage = $this->perginationPerPage();

        $result = EloquentBuilder::to($query, request()->filter)->paginate($perPage);

        return $this->successResponse('savings', $result);

    }

}

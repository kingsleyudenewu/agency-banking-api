<?php

namespace App\Http\Controllers\Api\Bank;

use App\Bank;
use App\Http\Controllers\APIBaseController;
use App\Http\Requests\CreateBankRequest;


/**
 * Class BanksController
 *
 * @package \App\Http\Controllers\Api\Bank
 */
class BanksController extends APIBaseController
{

    public function index()
    {
        return $this->successResponse('banks', Bank::orderByName()->get());
    }

    public function store(CreateBankRequest $request)
    {

        return $this->successResponse('bank', Bank::create($request->only('name', 'code')));
    }

}

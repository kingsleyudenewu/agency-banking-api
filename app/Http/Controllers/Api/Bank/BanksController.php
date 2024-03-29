<?php

namespace App\Http\Controllers\Api\Bank;

use App\Bank;
use App\Http\Controllers\APIBaseController;
use App\Http\Requests\CreateBankRequest;
use Illuminate\Http\Request;


/**
 * Class BanksController
 *
 * @package \App\Http\Controllers\Api\Bank
 */
class BanksController extends APIBaseController
{

    public function index()
    {
        $banks = \request('enabled') ?  Bank::enabled()->orderByName()->get() :  Bank::orderByName()->get();
        return $this->successResponse('banks', $banks);
    }

    public function store(CreateBankRequest $request)
    {

        return $this->successResponse('bank', Bank::create($request->only('name', 'code')));
    }

    public function update(Request $request, $id)
    {
        $bank = Bank::find($id);
        if(!$bank) return $this->errorResponse('Bank not found.', null, 404);


        $request->validate(['name' => 'required', 'code' => 'required']);

        $bank->name = $request->input('name');
        $bank->code = $request->input('code');
        $bank->save();

        return $this->successResponse('bank', $bank);
    }


    public function enableOrDisable(Request $request)
    {

        $request->validate([
            'id' => 'required|uuid|exists:banks,id',
            'action' => 'required:in:enable,disable'
        ]);


        $bank = Bank::find($request->input('id'));

        $bank->enabled = $request->input('action') === 'enable' ? true :  null;

        $bank->save();

        return $this->successResponse('bank', $bank);
    }


}

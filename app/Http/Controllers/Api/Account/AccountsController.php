<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\APIBaseController;
use App\Http\Resources\AccountCollection;
use App\Profile;
use App\User;
use \EloquentBuilder;
use Illuminate\Http\Request;


/**
 * Class AccountsController
 *
 * @package \App\Http\Controllers\Api\Account
 */
class AccountsController extends APIBaseController
{

    public function index()
    {
        $query  = Profile::query();

        if(!auth()->user()->hasRole(User::ROLE_ADMIN))
        {
            $query->whereHas('user', function ($query){
                $query->where('parent_id', auth()->user()->id);
            });
        }

        $query->with(['user', 'user.roles:id,name', 'user.country']);

        $perPage = $this->perginationPerPage();

        $result = EloquentBuilder::to($query, request()->filter)->paginate($perPage);

        return $this->successResponse('Accounts', $this->getPagingData($result, function ($items){
            return AccountCollection::collection($items);
        }));


    }

    public function wallet(Request $request)
    {
        $user = new \App\Koloo\User($request->user());

        return $this->successResponse('wallet', [
            'wallet' => $user->mainWallet()->getModel(),
            'purse' => $user->purse()->getModel()
        ]);

    }


    public function show(Request $request, $id)
    {
        return ;
    }
}

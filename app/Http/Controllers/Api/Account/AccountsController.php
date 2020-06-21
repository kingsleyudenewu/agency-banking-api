<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\APIBaseController;
use App\Http\Resources\AccountCollection;
use App\Http\Resources\AccountView;
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


        $authUser = new \App\Koloo\User(auth()->user());
        $query  = User::query();
        $query->with(['country'])->whereHas('profile', function ($q){
            $q->whereNotNull('user_id');
        });

        if(!$authUser->isAdmin())
        {
            $query = User::query();
            $query->where('parent_id', $authUser->getId());
        }


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
        $user = User::with('profile', 'roles:id,name')->find($id);

        if(!$user) return $this->errorResponse('User not found', null, 404);

        $authUser = new \App\Koloo\User($request->user());

        if(!$authUser->isAdmin() && ($user->parent_id !== $authUser->getId() || $user->id !== $authUser->getId()))
        {
            return $this->errorResponse('Not found', null, 404);
        }

        return $this->successResponse('account', new AccountView($user));
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\AccountApproved;
use App\Events\AccountDisapproved;
use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class AgentApprovalController
 *
 * @package \App\Http\Controllers\Api\Admin
 */
class AgentApprovalController extends APIBaseController
{

    public function store(Request $request)
    {
        $request->validate(['approval_remark' => 'nullable|max:255']);

        try {
            $user = User::find(request('id'));
            User::checkExistence($user);

            switch (request('action'))
            {
                case 'approve':
                    if($user->isApproved()) throw new \Exception('Account is already approved.');
                    $user->approve($request->user()->id, request('approval_remark'));
                    event(new AccountApproved($user));
                    break;
                case 'disapprove':
                    $user->disapprove($request->user()->id, request('approval_remark'));
                    event(new AccountDisapproved($user, request('approval_remark')));
                    break;
                default:
                    throw new \Exception('Invalid action');
            }

            return $this->successResponse('Successful');



        } catch (\Exception $e)
        {
            Log::error('AgentApprovalController :: ' . $e->getMessage());

            return $this->errorResponse($e->getMessage());
        }

    }

}

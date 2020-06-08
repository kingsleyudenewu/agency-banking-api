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
        $request->validate(['remark' => 'nullable|max:255']);

        $remark = $request->input('remark') ?: '';

        try {
            $user = User::find(request('id'));
            User::checkExistence($user);

            switch (request('action'))
            {
                case 'approve':
                    if($user->isApproved()) throw new \Exception('Account is already approved.');
                    $user->approve($request->user()->id, $remark);
                    break;
                case 'disapprove':
                    if(!$user->isApproved()) throw new \Exception('Account is already disapproved.');
                    $user->disapprove($request->user()->id, $remark);
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

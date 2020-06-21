<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use Illuminate\Http\Request;

/**
 * Class AccountSuspensionController
 *
 * @package \App\Http\Controllers\Api\Admin
 */
class AccountSuspensionController extends APIBaseController
{
    const ACTION_SUSPEND = 'suspend';
    const ACTION_UNSUSPEND = 'unsuspend';

    public function update(Request $request, $id, $action)
    {
       try {
             $user = User::find($id);
             User::checkExistence($user);

             if($user->getId() === $request->user()->id) throw new \Exception('You cannot perform this action on your account account');

             switch ($action)
             {
                 case self::ACTION_SUSPEND:
                     $user->suspend();
                     break;
                 case self::ACTION_UNSUSPEND:
                     $user->unsuspend();
                     break;
                 default:
                     throw new \Exception('Invalid action. Send suspend/unsuspend as an action.');
             }

             return $this->successResponse('Operation successful.');

       }
       catch (\Exception $e)
       {
           return $this->errorResponse($e->getMessage());
       }
    }

}

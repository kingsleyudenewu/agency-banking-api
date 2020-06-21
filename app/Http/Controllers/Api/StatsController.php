<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use Illuminate\Http\Request;

/**
 * Class StatsController
 *
 * @package \App\Http\Controllers\Api
 */
class StatsController extends APIBaseController
{

    public function index(Request $request)
    {

       try {
           $authUser = new User($request->user());

           if($request->input('id') && !$authUser->isAdmin())
           {
               $user = $authUser->getModel()->children()->find($request->input('id'));
               if(!$user) throw new \Exception('You can not view this information');

               $authUser = new User($user);

           }else if($request->input('id') && $authUser->isAdmin())
           {
               $authUser = User::find($request->input('id'));
               User::checkExistence($authUser);
           }

           return $this->successResponse('stats', $authUser->stats($request));
       } catch (\Exception $e)
       {
            return $this->errorResponse($e->getMessage());
       }

    }

}

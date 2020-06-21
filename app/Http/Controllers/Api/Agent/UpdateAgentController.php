<?php

namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\APIBaseController;
use App\Http\Requests\UpdateAgentRequest;
use App\Koloo\User;
use App\Http\Resources\Profile;
use Illuminate\Http\Request;

/**
 * Class UpdateAgentController
 *
 * @package \App\Http\Controllers\Api\Agent
 */
class UpdateAgentController extends APIBaseController
{

    public function store(UpdateAgentRequest $request)
    {

        try {
            $user = User::find($request->input('id'));
            User::checkExistence($user);

            $authUser = new User($request->user());
            $userModel = $user->getModel();
            $profile = $userModel->profile;

            if($profile->setup_completed && !$authUser->isAdmin())
            {
                throw new \Exception('You can not edit this profile.');
            }

            if(!$authUser->isAdmin() && $user->getParentID() !== $authUser->getId())
            {
                throw new \Exception('You can not edit this profile.');
            }

            $data = $request->validated() ;
            if($authUser->isAdmin() && $request->input('type'))
                unset($data['type']);



            $userModel->fill($data)->save();
            $profile->fill($data)->save();

            if($authUser->isAdmin() && !$user->isAdmin())
            {
                if( $request->input('type') !== 'super')
                {
                    $userModel->roles()->sync([]);

                    $userModel->setAsAgent();

                    $maxCommission = settings('max_commission') - $authUser->getCommission();

                    $user->setCommission($maxCommission );
                    $user->setCommissionForAgent(0);
                }
                else {
                    $userModel->roles()->sync([]);
                    $userModel->setAsSuperAgent();
                }
            }


            return $this->successResponse('Successful', new Profile($userModel));


        } catch (\Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }



    }

}

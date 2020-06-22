<?php

namespace App\Http\Controllers\Api\Agent;

use App\Events\AgentDocumentUploaded;
use App\Http\Controllers\APIBaseController;
use App\Http\Requests\AgentDocumentUploadRequest;
use App\Koloo\User;
use App\Profile;


/**
 * Class DocumentManagement
 *
 * @package \App\Http\Controllers\Api\Agent
 */
class DocumentManagement extends APIBaseController
{

    public function upload(AgentDocumentUploadRequest $request)
    {
        $path = settings('document_storage_path');
        $disk = settings('document_storage_driver');
        $docType = request('document_type');

        // We already check for existence via the request middle where
        $agent = User::find(request('id'));

        $profile = $agent->getModel()->profile;


        if(!$profile)
            return $this->errorResponse('Profile not set for this user.');

        if($profile->setup_completed && !$request->user()->hasRole(\App\User::ROLE_ADMIN))
            return  $this->errorResponse('You can not add additional information to this application.');

        $storedLocation = $request->file('doc')->store($path, $disk);

        $fileData = [
            'disk' => $disk,
            'path' => $storedLocation
        ];

        $agent->updateDocument($fileData, $docType);

        $profile = Profile::find($profile->id);

        if($profile->hasUploadedAllDocuments())
        {
            $profile->setupCompleted();
        }

        event(new AgentDocumentUploaded($docType));

        return $this->successResponse('Uploaded', [
            'id' => $profile->user_id,
            'setup_completed' => $profile->setup_completed
        ]);
    }

}

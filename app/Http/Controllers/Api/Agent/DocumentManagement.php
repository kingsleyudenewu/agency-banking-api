<?php

namespace App\Http\Controllers\Api\Agent;

use App\Events\AgentDocumentUploaded;
use App\Http\Controllers\APIBaseController;
use App\Http\Requests\AgentDocumentUploadRequest;
use App\Koloo\User;


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

        $storedLocation = $request->file('doc')->store($path, $disk);

        $fileData = [
            'disk' => $disk,
            'path' => $storedLocation
        ];

        $agent->updateDocument($fileData, $docType);

        event(new AgentDocumentUploaded($docType));

        return $this->successResponse('Uploaded');
    }

}

<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\APIBaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class SMSReportController extends APIBaseController
{

    const INFOBIP_DND_ERROR_CODE_ID = 10;
    const INFOBIP_DND_ERROR_CODE_GID = 5;

    public function processReport(Request $request) 
    {
        $this->logInfo(json_encode($request->input('results')));

        if( ! is_array($request->input('results')) )
            return;

        $result = $request->input('results')[0];
        $to = array_get($result, 'to'); 
        $messageId = array_get($result, 'messageId');

        //Verify webhook hash
        if(!$result || !$this->verifyHookHash($to, $messageId, array_get($result, 'callbackData')))
            return;
            
        //check if status code is not DND_RESTRICTION
        if( !$result['status'] || 
            $result['status']['groupId'] != static::INFOBIP_DND_ERROR_CODE_GID ||
            $result['status']['id'] != static::INFOBIP_DND_ERROR_CODE_ID ) {

            return;
        }

        new event(FoundDndSubscriberMessage($to, $messageId));

        return 'ok';
    }


    private function verifyHookHash($to, $messageId, $callbackHash) 
    {
        return $callbackHash == hash('sha512', $to . $messageId . env('INFOBIP_INTL_API_KEY'));
    }



    private function logInfo($message, array $context = []): self
    {
        return $this->log('info', $message, $context);
    }

    private function logError(string $message = '', array $context = []): self
    {
        $message = "Response: {$message}";

        return $this->log('error', $message, $context);
    }

    private function log(string $type, string $message, array $context = []): self
    {
        $message = "[DEBUG MODE] " . $message;

        Log::channel('koloo')->{$type}($message, $context);

        return $this;
    }

}
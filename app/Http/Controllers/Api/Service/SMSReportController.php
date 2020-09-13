<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\APIBaseController;
use Illuminate\Http\Request;

class SMSReportController extends APIBaseController
{

    const INFOBIP_DND_ERROR_CODE_ID = 10;
    const INFOBIP_DND_ERROR_CODE_GID = 5;

    public function processReport(Request $request) 
    {
        $result = is_array($request->input('results')) ? $request->input('results')[0] : null;

        //Verify webhook hash
        if( !$result || ! $this->verifyHookHash( 
            array_get($result, 'to'), 
            array_get($result, 'messageId'), 
            array_get($result, 'callbackData') )) {
                
            return '111';
        }
            
        //check if status code is DND_RESTRICTION
        if( !$result['status'] || 
            $result['status']['groupId'] != static::INFOBIP_DND_ERROR_CODE_GID ||
            $result['status']['id'] != static::INFOBIP_DND_ERROR_CODE_ID ) {

            return '222';
        }
        
        $to = array_get($result, 'to'); 
        $messageId = array_get($result, 'messageId');

        new event(FoundDndSubscriberMessage($to, $messageId));

        return 'ok';
    }


    private function verifyHookHash($to, $messageId, $callbackHash) 
    {
        return $callbackHash == hash('sha512', $to . $messageId . env('INFOBIP_INTL_API_KEY'));
    }
}
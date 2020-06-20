<?php

namespace App\Listeners;

use App\Services\Monnify\Api as MonnifyApi;
use App\Traits\LogTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleAgentAccountCreation implements ShouldQueue
{
    protected $monnifyApi;
    use LogTrait;

    /**
     * Create the event listener.
     *
     * @param \App\Services\Monnify\Api $monnifyApi
     */
    public function __construct(MonnifyApi $monnifyApi)
    {
        $this->monnifyApi = $monnifyApi;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = $event->agent;

        try {
            $res = $this->monnifyApi->reserveAccountNumber($user->getName(), $user->getEmail(), $user->getId());
            $user->setProvidusBankDetail($res->accountNumber, $res->accountReference);

        } catch (\Exception $e)
        {
           $this->logError('HandleAgentAccountCreation::monnifyApi ::' . $e->getMessage());
        }

        try {
            if($user->isCustomer())
                $user->sendWelcomeSMS();

        } catch (\Exception $e)
        {
            $this->logError('HandleAgentAccountCreation::sendWelcomeSMS ::' . $e->getMessage());
        }

    }
}

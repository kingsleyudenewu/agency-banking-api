<?php

namespace App\Listeners;

use App\Services\Monnify\Api;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleAgentAccountCreation implements ShouldQueue
{
    protected $monnifyApi;

    /**
     * Create the event listener.
     *
     * @param \App\Services\Monnify\Api $monnifyApi
     */
    public function __construct(Api $monnifyApi)
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
            Log::error('HandleAgentAccountCreation::monnifyApi ::' . $e->getMessage());
        }

    }
}

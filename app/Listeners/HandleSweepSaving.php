<?php

namespace App\Listeners;

use App\Koloo\SavingManagement;
use App\Saving;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleSweepSaving
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $saving = $event->saving;
        if(!$saving->sweep_status)
        {
            SavingManagement::charge($saving);
        }

    }
}

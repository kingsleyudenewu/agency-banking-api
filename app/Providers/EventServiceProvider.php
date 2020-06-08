<?php

namespace App\Providers;

use App\Events\AccountApproved;
use App\Events\AccountDisapproved;
use App\Events\AgentAccountCreated;
use App\Events\BalanceUpdated;
use App\Events\FundTransfer;
use App\Events\SendMessage;
use App\Events\SendNewOTP;
use App\Events\WalletBilled;
use App\Listeners\HandleAgentAccountCreation;
use App\Listeners\HandleFundTransfer;
use App\Listeners\HandleNewOTP;
use App\Listeners\HandleWalletBilled;
use App\Listeners\NotifySuperAgentOnAccountApproval;
use App\Listeners\NotifySuperAgentOnAccountDisapproval;
use App\Listeners\OnSendMessage;
use App\Listeners\WriteUpdateUpdatedTransaction;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        AgentAccountCreated::class => [
            HandleAgentAccountCreation::class
        ],

        SendMessage::class => [
            OnSendMessage::class
        ],

        SendNewOTP::class => [
            HandleNewOTP::class
        ],

        WalletBilled::class => [
            HandleWalletBilled::class
        ],

        BalanceUpdated::class => [
            WriteUpdateUpdatedTransaction::class
        ],

        FundTransfer::class => [
            HandleFundTransfer::class
        ],

        AccountApproved::class => [
            NotifySuperAgentOnAccountApproval::class
        ],

        AccountDisapproved::class => [
            NotifySuperAgentOnAccountDisapproval::class
        ]

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

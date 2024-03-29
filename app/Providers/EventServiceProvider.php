<?php

namespace App\Providers;

use App\Events\AccountApproved;
use App\Events\AccountDisapproved;
use App\Events\AgentAccountCreated;
use App\Events\BalanceUpdated;
use App\Events\CommissionPayoutStatusChanged;
use App\Events\CustomerFundWithdrawal;
use App\Events\FundTransfer;
use App\Events\NewPasswordRequested;
use App\Events\SavingSwept;
use App\Events\SendMessage;
use App\Events\SendNewOTP;
use App\Events\SweepSaving;
use App\Events\WalletBilled;
use App\Events\WalletCredited;
use App\Events\FoundDndSubscriberMessage;
use App\Listeners\HandleAgentAccountCreation;
use App\Listeners\HandleCommissionPayoutStatusChanged;
use App\Listeners\HandleFundTransfer;
use App\Listeners\HandleNewOTP;
use App\Listeners\HandleSweepSaving;
use App\Listeners\HandleWalletBilled;
use App\Listeners\HandleWalletCredited;
use App\Listeners\NotifySuperAgentOnAccountApproval;
use App\Listeners\NotifySuperAgentOnAccountDisapproval;
use App\Listeners\OnSendMessage;
use App\Listeners\SendFundWithdrawalNotification;
use App\Listeners\SendPasswordResetNotification;
use App\Listeners\SendSavingSweptNotification;
use App\Listeners\WriteUpdateUpdatedTransaction;
use App\Listeners\ProcessDndSubscriberMessage;
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

        WalletCredited::class => [
          HandleWalletCredited::class
        ],

        CustomerFundWithdrawal::class => [
          SendFundWithdrawalNotification::class
        ],

        BalanceUpdated::class => [
            WriteUpdateUpdatedTransaction::class
        ],

        FundTransfer::class => [
            HandleFundTransfer::class
        ],

        AccountApproved::class => [
           // NotifySuperAgentOnAccountApproval::class,
            SendPasswordResetNotification::class
        ],

        NewPasswordRequested::class => [
            SendPasswordResetNotification::class
        ],
        AccountDisapproved::class => [
           // NotifySuperAgentOnAccountDisapproval::class
        ],

        CommissionPayoutStatusChanged::class => [
            HandleCommissionPayoutStatusChanged::class
        ],

        SweepSaving::class => [
            HandleSweepSaving::class
        ],

        SavingSwept::class => [
          SendSavingSweptNotification::class
        ],

        FoundDndSubscriberMessage::class => [
            ProcessDndSubscriberMessage::class
        ],

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

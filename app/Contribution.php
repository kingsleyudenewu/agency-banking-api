<?php

namespace App;



use App\Events\SendMessage;
use App\Koloo\User as KolooUser;

class Contribution extends BaseModel
{

    protected $fillable = ['amount', 'saving_id', 'created_by'];

    public function savingPlan()
    {
        return $this->belongsTo(Saving::class, 'saving_id');
    }

    /**
     * The agent that created the saving
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    public function commissionComputed() : bool
    {
        return boolval($this->commission_computed);
    }

    public function updateCommissionComputed()
    {
        $this->commission_computed = true;
        $this->save();
    }


    public function sendContributionMessageToUser(KolooUser $user)
    {

        $amount = 'N' . number_format($this->amount,2);
        $saving = $this->savingPlan;
        $channel = 'sms';
        $amountSaved = 'N' . number_format($saving->amount_saved,2);
        $walletBalance = 'N' . number_format($user->mainWallet()->getAmount(),2);


        //%s credited to your Koloó safe. Safe balance is now %s. You also have %s in you wallet.
        $message = sprintf(config('koloo.contribution_message_to_customer'), $amount, $amountSaved, $walletBalance);

        $message = Message::create([
            'message' => $message,
            'message_type' => $channel,
            'user_id' => $user->getId(),
            'sender' => KolooUser::rootUser()->getId(),
            'subject' => 'Contribution Notification'
        ]);

        event(new SendMessage($message, $channel));
    }
}

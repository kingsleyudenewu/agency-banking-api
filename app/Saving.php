<?php

namespace App;
use Cknow\Money\Money;

class Saving extends BaseModel
{
    protected $fillable = [
        'amount',
        'saving_cycle_id',
        'completed',
        'owner_id',
        'creator_id',
        'meta'
    ];

    public function cycle()
    {
        return $this->belongsTo(SavingCycle::class, 'saving_cycle_id');
    }

    /**
     * The agent that created the saving
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * The person saving the fund
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function money()
    {
        $currency = strtoupper($this->owner->country->currency);

        if(!$currency) $currency = 'NGN';
        return Money::$currency($this->amount);
    }

}

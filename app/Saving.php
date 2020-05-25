<?php

namespace App;


class Saving extends BaseModel
{
    protected $fillable = [
        'amount',
        'saving_cycle_id',
        'completed',
        'owner_id',
        'creator_id',
        'meta',
        'target'
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

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class, 'saving_id');
    }

}

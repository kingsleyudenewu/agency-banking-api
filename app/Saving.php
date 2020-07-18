<?php

namespace App;


use Carbon\Carbon;

class Saving extends BaseModel
{
    protected $fillable = [
        'amount',
        'saving_cycle_id',
        'completed',
        'owner_id',
        'creator_id',
        'meta',
        'target',
        'maturity'
    ];

    protected $dates = ['created_at', 'maturity', 'updated_at'];

    protected $appends = ['amount_saved', 'matured', 'total_contributions', 'saving_frequent_count'];

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

    public function getTargetAttribute($value)
    {
        return $value / 100;
    }

    public function getTotalContributionsAttribute($value)
    {
        return $this->contributions()->count();
    }

    public function getSavingFrequentCountAttribute($value)
    {
        return percentOf($this->cycle->duration, $this->total_contributions);
    }

    public function getAmountSavedAttribute($value)
    {
        return $this->contributions()->sum('amount') / 100;
    }

    public function getMaturedAttribute($value)
    {
        return $this->maturity && $this->maturity->isPast();
    }

    public function setTargetAttribute($value)
    {
        $this->attributes['target'] = $value * 100;
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class, 'saving_id');
    }

    public function canAcceptNewContribution()
    {
        if(!$this->maturity || $this->maturity->isPast())
        {
            throw new \Exception('Saving closed for new contribution');
        }
    }

    public function stats() : array
    {
        return [
            'amountSaved' => $this->contributions()->sum('amount') / 100,
            'totalSavings' => $this->contributions()->count(),
        ];
    }


    public function lastContribution()
    {
        return $this->contributions()->latest()->first();
    }

    public function hasContributedOn(Carbon $at=null) : bool
    {

        if(!$at) $at = Carbon::today();

        return $this->contributions()
            ->whereDate('created_at', $at->toDateString())
            ->exists();

    }

    public function scopeSweepables($query)
    {
        return $query->whereNull('sweep_status')
                ->whereDate('maturity', '<', now()); //
    }

    public function swept($comment='')
    {
        $this->sweep_status = 'swept';
        $this->swept_at = now();
        $this->sweep_comment = $comment;
        $this->completed = now();
        $this->save();

    }


}

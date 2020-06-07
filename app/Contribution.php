<?php

namespace App;



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

}

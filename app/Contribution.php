<?php

namespace App;



class Contribution extends BaseModel
{

    protected $fillable = ['amount', 'saving_id', 'created_by'];

    public function savingPlan()
    {
        return $this->belongsTo(Saving::class, 'saving_id');
    }

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

}

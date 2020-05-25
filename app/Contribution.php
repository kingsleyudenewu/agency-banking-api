<?php

namespace App;



class Contribution extends BaseModel
{

    protected $fillable = ['amount', 'saving_id', 'created_by'];

    public function savingPlan()
    {
        return $this->belongsTo(Saving::class, 'saving_id');
    }

}

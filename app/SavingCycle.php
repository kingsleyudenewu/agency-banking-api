<?php

namespace App;


class SavingCycle extends BaseModel
{
    const CHARGE_TYPE_FLAT = 'flat';

    const CHARGE_TYPE_PERCENT = 'percent';

    protected $fillable = [
        'title', 'duration',
        'rule', 'description',
        'min_saving_frequent',
        'charge_type',
        'percentage_to_charge',
        'min_saving_amount'
    ];

    public static function isFlatCharge($charge): bool {
        return  static::CHARGE_TYPE_FLAT === $charge;
    }

    public function savings()
    {
        return $this->hasMany(Saving::class, 'saving_cycle_id');
    }

    public function minSavingAmount() : int
    {
        return $this->min_saving_amount;
    }

}

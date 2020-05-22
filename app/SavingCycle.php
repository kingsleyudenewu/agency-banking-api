<?php

namespace App;


class SavingCycle extends BaseModel
{
    const CHARGE_TYPE_FLAT = 'flat';
    const CHARGE_TYPE_PERCENT = 'percent';
    protected $fillable = ['title', 'duration', 'rule', 'description', 'min_saving_frequent', 'charge_type', 'percentage_to_charge'];

    public static function isFlatCharge($charge): bool {
        return  static::CHARGE_TYPE_FLAT === $charge;
    }

}

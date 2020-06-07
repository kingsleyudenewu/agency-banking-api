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

    public function getMinSavingAmountAttribute($value)
    {
        return $value / 100;
    }

    public function setMinSavingAmountAttribute($value)
    {
        $this->attributes['min_saving_amount'] = $value * 100;
    }

    public function getPercentageToChargeAttribute($value)
    {
        return $value / 100;
    }

    public function getDurationAttribute($value)
    {
        return  intval($value);
    }

    public function setPercentageToChargeAttribute($value)
    {
        $this->attributes['percentage_to_charge'] = $value * 100;
    }

    public function getMinSavingFrequentAttribute($value)
    {
        return $value / 100;
    }

    public function setMinSavingFrequentAttribute($value)
    {
        $this->attributes['min_saving_frequent'] = $value * 100;
    }


}

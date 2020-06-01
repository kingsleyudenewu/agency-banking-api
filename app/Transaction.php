<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    const TRANSACTION_TYPE_CREDIT = 'credit';
    const TRANSACTION_TYPE_DEBIT = 'debit';

    protected $fillable = [
        'type',
        'amount',
        'trans_ref',
        'transactionable_id',
        'transactionable_type',
        'remark'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactionable()
    {
        return $this->morphTo();
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

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    const TRANSACTION_TYPE_CREDIT = 'credit';
    const TRANSACTION_TYPE_DEBIT = 'debit';


    // Some labels
    const LABEL_NORMAL = 'normal';
    const LABEL_COMMISSION = 'commission';
    const LABEL_TRANSFER = 'transfer';
    const LABEL_CONTRIBUTION = 'contribution';
    const LABEL_PAYOUT = 'payout';
    const LABEL_WITHDRAWAL =  'withdrawal';
    const LABEL_MONNIFY =  'Monnify';
    const LABEL_SWEEP = 'sweep';
    const LABEL_MANUAL = 'manual';

    protected $appends = ['amount_formatted'];

    protected $fillable = [
        'type',
        'amount',
        'trans_ref',
        'transactionable_id',
        'transactionable_type',
        'remark',
        'label',
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

    public function getAmountFormattedAttribute($value)
    {
        return round($value, 2);
    }


    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = round($value,2) * 100;
    }

    public function scopeCredit($query)
    {
        return $query->where('type', self::TRANSACTION_TYPE_CREDIT);
    }

    public function scopeDebit($query)
    {
        return $query->where('type', self::TRANSACTION_TYPE_DEBIT);
    }
}

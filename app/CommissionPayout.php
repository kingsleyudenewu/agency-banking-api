<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionPayout extends Model
{

    use SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_WAITING_PAYMENT = 'Approved awaiting payment';
    const STATUS_PAID = 'commission paid';

    protected $fillable = ['status', 'amount', 'wallet_id', 'user_id', 'bank_id', 'bank_account_number', 'bank_account_name'];

    protected $hidden = ['deleted_at'];

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    /**
     *
     * @param string|null $userId the user ID that completed the transaction
     */
    public function markAsPaid(string $userId=null)
    {
        $this->paid = now();
        $this->completed_by = $userId;
        $this->status = static::STATUS_PAID;
        $this->save();
    }

    public function updateStatus($newStatus)
    {
        $this->status = $newStatus;
        $this->save();
    }
}

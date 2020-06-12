<?php

namespace App;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionPayout extends Model
{

    use LogTrait, SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_WAITING_PAYMENT = 'Approved awaiting payment';
    const STATUS_PAID = 'commission paid';

    protected $fillable = ['status', 'amount', 'wallet_id', 'user_id'];

    protected $hidden = ['deleted_at'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->logInfo( 'CommissionPayout');
    }


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
}

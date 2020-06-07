<?php

namespace App;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Wallet extends Model
{
    use LogTrait;

    const WALLET_TYPE_MAIN = 'wallet';
    const WALLET_TYPE_COMMISSION = 'commission';


    protected $fillable = ['user_id', 'currency', 'type'];

    protected $hidden  = ['hash', 'created_at', 'updated_at', 'touched', 'type', 'user_id', 'id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->logChannel = 'KOLOO_WALLET';
    }


    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }

    /**
     * Every user starts with two wallets
     *
     * 1. A commission and regular wallet
     *
     * @param \App\User $user
     * @param null      $type
     *
     * @return mixed
     */
    public static function start(User $user)
    {
        Log::info('Starting new wallets for ' . __($user->email));

        try {

            DB::beginTransaction();

            if(!$user->hasWallet(static::WALLET_TYPE_MAIN))
            {
                static::create([
                    'user_id' => $user->id,
                    'currency' => $user->country->currency,
                    'type' => static::WALLET_TYPE_MAIN,
                    'hash' => null
                ]);
            }

            if(!$user->hasWallet(static::WALLET_TYPE_COMMISSION))
            {
                static::create([
                    'user_id' => $user->id,
                    'currency' => $user->country->currency,
                    'type' => static::WALLET_TYPE_COMMISSION,
                    'hash' => null
                ]);
            }

            DB::commit();

            Log::info('Wallets started  for ' . __( $user->email));

            return $user->wallets;

        } catch (\Exception $e)
        {
            DB::rollBack();

            Log::error($e->getMessage() . __( $user->email));
        }

        return null;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}

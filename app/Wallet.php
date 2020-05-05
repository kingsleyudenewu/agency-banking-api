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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->logChannel = 'KOLOO_WALLET';
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
                    'type' => static::WALLET_TYPE_MAIN
                ]);
            }

            if(!$user->hasWallet(static::WALLET_TYPE_COMMISSION))
            {
                static::create([
                    'user_id' => $user->id,
                    'currency' => $user->country->currency,
                    'type' => static::WALLET_TYPE_COMMISSION
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

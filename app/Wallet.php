<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'currency'];

    public static function start(User $user)
    {

        if(!$user->wallet)
        {
            return static::create([
                'user_id' => $user->id,
                'currency' => $user->country->currency
            ]);
        }

        return $user->wallet;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}

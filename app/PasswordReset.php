<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $fillable = [
        'hash',
        'expires_at',
        'email'
    ];
    protected $dates = ['created_at', 'updated_at', 'expires_at'];
}

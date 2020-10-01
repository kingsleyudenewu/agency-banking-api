<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
/**
 * Class AuthBaseModel
 *
 * @package \App
 */
class AuthBaseModel extends Authenticatable{

    use Notifiable, Uuids, SoftDeletes;

    public $incrementing = false;

    protected $dates = ['deleted_at'];

    protected $keyType = 'string';
}

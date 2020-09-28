<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * Class BaseModel
 *
 * @package \App
 */
class BaseModel extends Model
{
    use Uuids, SoftDeletes;

    public $incrementing = false;

    protected $dates = ['deleted_at'];

    protected $keyType = 'string';
}

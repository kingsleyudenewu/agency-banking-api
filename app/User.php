<?php

namespace App;


use Illuminate\Notifications\Notifiable;

use App\Http\Resources\User as UserTransformer;
use Illuminate\Support\Facades\Hash;
use Shanmuga\LaravelEntrust\Traits\LaravelEntrustUserTrait;

class User extends AuthBaseModel
{
    use Notifiable, LaravelEntrustUserTrait;

    const ROLE_AGENT = 'agent';
    const ROLE_SUPER_AGENT = 'super-agent';
    const ROLE_ADMIN = 'admin';

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_OTHERS = 'others';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'other_name',
        'country_code',
        'email',
        'password',
        'phone',
        'parent_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    public function transform(): UserTransformer
    {
        return new UserTransformer($this);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id', 'id');
    }

    public function setAsAgent()
    {
        $agentRole = Role::where('name', static::ROLE_AGENT)->first();
        $this->attachRole($agentRole);
    }

    public function setAsSuperAgent()
    {
        $superAgentRole = Role::where('name', static::ROLE_SUPER_AGENT)->first();
        $this->attachRole($superAgentRole);
    }

    public function updatePassword(string $password) : bool
    {
        $this->password = Hash::make($password);
        $this->save();
        return true;
    }
}

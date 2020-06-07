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
    const ROLE_CUSTOMER = 'customer';

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_OTHERS = 'others';

    const STATUS_APPROVED = 'approved';
    const STATUS_PENDING_APPROVAL = 'pending approval';
    const STATUS_DRAFT = 'draft';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'country_code',
        'email',
        'password',
        'phone',
        'parent_id',
        'account_number',
        'providus_account_number',
        'providus_account_ref',
        'is_root',
        'status',
        'approved_by',
        'approved_at',
        'approval_remark'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token', 'email_verified_at',
        'phone_verified', 'deleted_at', 'is_root'
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

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
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

    public function setAsCustomer()
    {
        $customer = Role::where('name', static::ROLE_CUSTOMER)->first();
        $this->attachRole($customer);
    }

    public function updatePassword(string $password) : bool
    {
        $this->password = Hash::make($password);
        $this->save();
        return true;
    }


    /**
     * TODO: Work on the account number to be unique
     * @return string
     */
    public static function makeAccountNumber() : string
    {
        return makeRandomInt(settings('account_number_length', 10));

    }

    public function hasWallet($type): bool
    {
        return $this->wallets()->where('type', $type)->exists();
    }

    public function getRoles()
    {
       return $this->roles->pluck('name');
    }

    /**
     * The savings this person has made - like saving money
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function savings()
    {
        return $this->hasMany(Saving::class, 'owner_id');
    }

    /**
     * The saving this agent has created
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function savingsCreated()
    {
        return $this->hasMany(Saving::class, 'creator_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

}

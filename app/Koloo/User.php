<?php

namespace App\Koloo;


use App\Events\SendNewOTP;
use App\OTP;
use App\User as Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class User
 *
 * @package \App\Koloo
 */
class User
{
    /**
     * User
     */
    private $model;

    /**
     * @var  $plainToken plain user api token
     */
    private $plainToken;


    /**
     * This is what we return when a user successfully login
     * @var
     */
    private $loginResponse = [];


    /**
     * Find User by id
     *
     * @param string|null $id
     *
     * @return static|null
     */
    public static function find(string $id = null): ?self
    {
        if (! $model = Model::find($id)) {
            return null;
        }

        return new static($model);
    }

    public static function findByPhone(string $phone = null): ?self
    {
        if (! $model = Model::where('phone', $phone)->first()) {
            return null;
        }

        return new static($model);
    }


    public static function findByEmail(string $email = null): ?self
    {
        if (! $model = Model::where('email', $email)->first()) {
            return null;
        }

        return new static($model);
    }

    public static function findOneByRole($role): ?self
    {
        if (! $model = Model::withRole($role)->first()) {
            return null;
        }

        return new static($model);
    }


    public function isAdmin() :bool
    {
        return $this->model->hasRole(Model::ROLE_ADMIN);
    }

    public function isAgent() :bool
    {
        return $this->model->hasRole(Model::ROLE_AGENT);
    }

    public function isSuperAgent() :bool
    {
        return $this->model->hasRole(Model::ROLE_SUPER_AGENT);
    }

    public function getParent() : ?self
    {
        return $this->model->parent_id ?  new static($this->model->parent) : null;
    }

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getHashedPassword() : string
    {
        return $this->model->password;
    }

    public function getId(): string
    {
        return $this->model->id;
    }

    public function getEmail(): string
    {
        return $this->model->email;
    }

    public function getPhone() : string
    {
        return $this->model->phone;
    }

    public function isPhoneVerified(): bool
    {
        return !! $this->model->phone_verified;
    }

    public function getName(): string
    {
        return $this->model->first_name . ' ' . $this->model->last_name;
    }

    public function getAPIToken(): ?string
    {
        return $this->model->api_token;
    }

    public function getPlainToken() : string
    {
        return $this->plainToken;
    }

    public function newAPIToken($len=80) : self
    {
        $token = Str::random($len);

        $this->model->forceFill(
            [
                'api_token' => hash('sha256', $token),
            ]
        )->save();

        $this->plainToken = $token;

        return $this;
    }


    public static function createWithProfile(array $data, Model $parent  = null) : Model
    {

        if($parent)
            $data['parent_id'] = $parent->id;

        $user =  Model::create($data);

        \App\Wallet::start($user);

        $user->profile()->create($data);

        return $user;
    }

    public function settings()
    {
        return settings()->group($this->getId());

    }

    public function canManageAgent()
    {
        return ($this->isSuperAgent() || $this->isAdmin()) ? true : false;
    }

    /**
     * Check if the current instance belongs to $parent
     *
     * @param \App\Koloo\User $parent
     *
     * @return bool
     */
    public function belongsTo(self  $parent): bool
    {
        return $this->getParent() && $this->getParentID() === $parent->getId() ? true  : false;
    }

    public function getParentID(): ?string
    {
        return $this->getParent() ? $this->getModel()->parent_id : null;
    }

    public function getProfile()
    {
        return $this->getModel()->profile();
    }

    private function isValidDocumentType($documentType): bool
    {
        $validDocumentFields = trim(settings('valid_document_fields'));

        return in_array($documentType, explode(',', $validDocumentFields));
    }

    public function updateDocument(array $data, string $documentType) : bool
    {

            return $this->isValidDocumentType($documentType) ?
                    $this->getProfile()->update([$documentType => $data]) :
                    false;


    }

    public function getDocumentPath($documentType):string
    {
        if($this->isValidDocumentType($documentType))
        {
            $document = json_decode($this->getModel()->profile->$documentType, false);

            return $document ? str_replace('//', '/', $document->path) : '';
        }

        return '';
    }


    public function setNewPassword(string $password):bool
    {
        return $this->getModel()->updatePassword($password);
    }

    public function sendOTP(string $messageType = 'sms')
    {
        if (! in_array($messageType, ['sms', 'email', 'both'])) {
            $error = "Messages does not support type [$messageType].";
            Log::info($error);
            throw new \Exception($error);
        }

        $otp =  makeRandomInt(settings('otp_length', 4));

        $otpModel = OTP::create([
            'expire_at' => now()->addHours(24),
            'phone' => $this->getPhone(),
            'code' => $otp
        ]);

        event(new SendNewOTP($otpModel, $this));

        return $otpModel;
    }

    public  function getLoginResponse(): array
    {
        return $this->loginResponse;
    }

    public function determineLoginOTP()
    {
        $otpRequired = boolval(settings('enable_otp_for_login'));
        $this->loginResponse['otp_required'] = $otpRequired;

        if($otpRequired)
        {
            $otp = new OtpVerification($this);
            $otp->send();
        }
        else
        {
            $this->loginResponse['access_token'] = $this->getPlainToken();
        }

    }
}

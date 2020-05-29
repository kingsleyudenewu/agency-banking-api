<?php

namespace App\Koloo;


use App\Events\BalanceUpdated;
use App\Events\NewContributionCreated;
use App\Events\NewSavingCreated;
use App\Events\PreWalletBilled;
use App\Events\SendNewOTP;
use App\Events\WalletBilled;
use App\Koloo\Exceptions\BilingException;
use App\Koloo\Exceptions\UserNotFoundException;
use App\OTP;
use App\Saving;
use App\SavingCycle;
use App\Traits\LogTrait;
use App\Transaction;
use App\User as Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Webpatser\Uuid\Uuid;

/**
 * Class User
 *
 * @package \App\Koloo
 */
class User
{
    use LogTrait;
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

    public static function search(string $query, $countryCode=''): ?self
    {
        if($countryCode)
        {
            $phone = PhoneNumber::format($query, $countryCode);
            if($phone)
            {
                $model = Model::where('phone', $phone)->first();
                if(!$model) return null;

                return new static($model);
            }

        }

        $model = Model::where('email', $query)
                    ->orWhere('phone', $query)
                    ->orWhere('account_number', $query)
                    ->orWhere('id', $query)->first();
        if (! $model) {
            return null;
        }

        return new static($model);
    }

    public static function findByInstance(?Model $user): ?self
    {
        if(!$user) return null;
        return static::find($user->id);
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

    public static function checkExistence(?User $user)
    {
       if(!$user) throw new UserNotFoundException('User not found.');
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
        $this->logChannel  = 'KOLOO_USER';
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

    public function getAccountNumber(): ?string
    {
        return $this->model->account_number;
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


    public static function createWithProfile(array $data, Model $parent  = null) : ?self
    {
       try {
           DB::beginTransaction();

           if($parent)
               $data['parent_id'] = $parent->id;

           $data['password'] = Hash::make($data['password']);
           $data['account_number'] = Model::makeAccountNumber();
           $user =  Model::create($data);

           $wallet = \App\Wallet::start($user);
           if(!$wallet) throw new \Exception('Unable to start wallet');

           $user->profile()->create($data);

           DB::commit();

           return static::find($user->id);
       } catch (\Exception $e) {
           throw $e;
           Log::channel('KOLOO_USER')->error($e->getMessage());
           DB::rollBack();
       }

       return null;
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
            'code' => $otp,
        ]);

        event(new SendNewOTP($otpModel, $this, $messageType));

        return $otpModel;
    }

    public  function getLoginResponse($otp = null)
    {
        return $otp ? json_decode($otp->response, true) : $this->loginResponse;

    }

    public function determineLoginOTP()
    {
        settings()->flushCache();

        $otpRequired = boolval(settings('enable_otp_for_login'));

        $this->loginResponse['otp_required'] = $otpRequired;

        $accessToken = $this->getPlainToken();

        if($otpRequired)
        {
            $otp = new OtpVerification($this);
            $otp->send();

            $this->loginResponse['expires_at'] = $otp->getExpiresAt();

            // The send has to happen first else getLastOtp will be null
            if($otp->getLastOtp())
                $otp->getLastOtp()->update(['response' => json_encode(['access_token' => $accessToken])]);
        }
        else
        {
            $this->loginResponse['access_token'] = $accessToken;
        }

    }

    public function getPassportPath(): ?string
    {
        $passport = $this->getModel()->profile ? $this->getModel()->profile->passport_photo : '';
        if(isJson($passport))
        {

            $passport = json_decode($passport, false, JSON_UNESCAPED_SLASHES);
            return str_replace("//", "/", $passport->path);
        }

        return null;
    }

    public function wallets()
    {
        return $this->model->wallets;
    }

    public function mainWallet()
    {
        $wallet = $this->model->wallets()->where('type', \App\Wallet::WALLET_TYPE_MAIN)->first();

        if(!$wallet) return null;
        return new Wallet($wallet);
    }


    public function checkWalletIsValid() : self
    {
        $wallet = $this->mainWallet();

        if(!$wallet || !$wallet->isValid()) throw new BilingException('Wallet is not in a valid state');

        return $this;
    }

    private function canChargeWallet(int $amount)
    {
        $this->checkWalletIsValid();

        $wallet = $this->mainWallet();

        if($wallet->getAmount() * 100 < $amount) throw new BilingException('Insufficient funds');

        return $this;
    }

    /**
     * Start a new savings for this user instance
     * if $user is set, this method will try to debit the user the
     * same amount the customer want to save
     *
     *
     * @param $data     array of data to use for the savings
     * @param $authUser - the account performing the operation
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public function newSaving(array $data , ?Model $authUser)
    {
        try {
            DB::beginTransaction();

            // Open the user savings
            $user = static::find($data['owner_id']);

            $authUser = static::findByInstance($authUser);
            $contribData = ['amount' => $data['amount']];

            // If we have the user, try to debit the user
            if($authUser)
            {
                $authUser->chargeWallet($data['amount'], 'New saving created for ' . e($user->getName()));
                $contribData['created_by'] = $authUser->getId();
            }

            $data = $user->validateForTransaction($data);

            $data['amount'] = $data['amount'] / 100;
            $saving = $user->makeNewSaving($data);

            $saving->contributions()->create($contribData);

            event(new NewSavingCreated($saving));

            DB::commit();

            return $saving;
        } catch (\Exception $e) {
            Log::channel('KOLOO_USER')->error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }


    private function chargeWallet($amount, $reason='Charged')
    {

        $this->canChargeWallet($amount);

        $wallet = $this->mainWallet();

        event(new PreWalletBilled($wallet));

        $wallet->debit($amount);

        event(new WalletBilled($wallet, $amount, $reason));

    }

    /**
     * TODO: Add some fraud check here
     *
     * @param array $data
     *
     * @return array
     * @throws \App\Koloo\Exceptions\BilingException
     */
    public function validateForTransaction(array $data) : array
    {
        $savingCycle = SavingCycle::find($data['saving_cycle_id']);
        if($data['amount'] < $savingCycle->minSavingAmount())
        {
            throw new BilingException('Amount too small for this package.');
        }

        $data['target'] = $savingCycle->duration * $data['amount'];
        $data['maturity'] = now()->addDays($savingCycle->duration);

        return $data;
    }

    private function makeNewSaving(array $data)
    {
        $old = $this->getModel()
            ->savings()
            ->where('owner_id', $this->getId())
            ->where('saving_cycle_id', $data['saving_cycle_id'])
            ->whereNull('completed')->first();

        if($old) {
            throw new \Exception('You have an active saving plan.');
        }


        return $this->getModel()->savings()->create($data);
    }

    public function transactions()
    {
        return $this->model->transactions;
    }

    public function writeTransaction(int $amount, string $type, string $remark = '')
    {
        return $this->model->transactions()
                ->create([
                    'type' => $type,
                    'amount' => $amount,
                    'trans_ref' => $this->makeTransactionRef(),
                    'remark' => $remark
                ]);

    }

    public function writeCreditTransaction(int $amount, string $remark = '')
    {
        return $this->writeTransaction($amount, Transaction::TRANSACTION_TYPE_CREDIT, $remark);
    }

    public function writeDebitTransaction(int $amount, string $remark = '')
    {
        return $this->writeTransaction($amount, Transaction::TRANSACTION_TYPE_DEBIT, $remark);
    }

    private function makeTransactionRef()
    {
        return Uuid::generate()->string;
    }

    public static function creditOrDebit($data, $performedBy) : self
    {
        $authUser = static::findByInstance($performedBy);
        static::checkExistence($authUser);

        $amount = $data['amount'];

        $customer = User::find($data['user_id']);
        static::checkExistence($customer);

        $method = $data['action'];
        $remark = isset($data['remark']) ? $data['remark'] : '';

        $customer->checkWalletIsValid()
            ->mainWallet()
            ->$method($amount);

        event(new BalanceUpdated($amount, $method, $customer, $authUser, $remark));

        return $customer;
    }

    public function getSavings()
    {
        $savings = $this->getModel()->savings();

        return $savings ? $savings->with('cycle:id,title,description')->get() : [];
    }

    public function contributeToSaving(Saving $saving, $amount)
    {
        try {
            DB::beginTransaction();

             $saving->canAcceptNewContribution();

             $customer = User::findByInstance($saving->owner);
             User::checkExistence($customer);

            $this->chargeWallet($amount, 'New contribution for ' . e($customer->getName()));

            $contribData = ['amount' => $amount / 100, 'created_by' => $this->getId()];

            $contribution = $saving->contributions()->create($contribData);

            event(new NewContributionCreated($contribution));

            DB::commit();

            return $contribution;

        } catch (\Exception $e) {
            Log::channel('KOLOO_USER')->error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }
}

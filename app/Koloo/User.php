<?php

namespace App\Koloo;


use App\CommissionPayout;
use App\Contribution;
use App\Events\AccountApproved;
use App\Events\AccountDisapproved;
use App\Events\BalanceUpdated;
use App\Events\CommissionEarned;
use App\Events\NewContributionCreated;
use App\Events\NewSavingCreated;
use App\Events\PreWalletBilled;
use App\Events\SendMessage;
use App\Events\SendNewOTP;
use App\Events\WalletBilled;
use App\Events\WalletCredited;
use App\Exceptions\OTPRequiredException;
use App\Koloo\Exceptions\BilingException;
use App\Koloo\Exceptions\UserNotFoundException;
use App\OTP;
use App\PasswordReset;
use App\Saving;
use App\SavingCycle;
use App\Traits\LogTrait;
use App\Transaction;
use App\User as Model;
use Illuminate\Http\Request;
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

    public static function findByProvidusReference(string $reference): ?self
    {
        if (! $model = Model::where('providus_account_ref', $reference)->first()) {
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

    public static function findByAccountNumber(string $accountNumber = null): ?self
    {
        if (! $model = Model::where('account_number', $accountNumber)->first()) {
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

    public function isCustomer() :bool
    {
        return $this->model->hasRole(Model::ROLE_CUSTOMER);
    }

    public function isAgent() :bool
    {
        return $this->model->hasRole(Model::ROLE_AGENT);
    }

    public function isSuperAgent() :bool
    {
        return $this->model->hasRole(Model::ROLE_SUPER_AGENT);
    }

    public function isApproved()
    {
        return $this->model->status === Model::STATUS_APPROVED;
    }

    /**
     * @param string|null $by
     * @param string      $remark
     *
     * @return bool
     */
    public function approve(string $by=null, $remark='')
    {
        $approval = $this->setStatus(Model::STATUS_APPROVED, $by, $remark);

        event(new AccountApproved($this, $remark));

        $this->getModel()->profile->setupCompleted();

        return $approval;
    }


    public function disapprove($by=null, $remark='')
    {
        $disapproval = $this->setStatus(Model::STATUS_DRAFT, $by, $remark);
        event(new AccountDisapproved($this, $remark));

        return $disapproval;
    }

    private function setStatus($newStatus, $by=null, $remark='')
    {
        $this->model->status = $newStatus;
        $this->model->approved_by = $by;
        $this->model->approved_at = now();
        $this->model->approval_remark = $remark;
        return $this->model->save();
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
        return $this->model->name;
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
           {
               $parent = new static($parent);
               static::checkExistence($parent);

               $data['parent_id'] = $parent->getId();

               if(!isset($data['type']) || $data['type'] !== 'super')
               {
                   $data['commission'] = $parent->getCommissionForAgent() ;
               }

           }

           $data['password'] = Hash::make($data['password']);
           $data['account_number'] = Model::makeAccountNumber();



           $user =  Model::create($data);

           $wallet = \App\Wallet::start($user);
           if(!$wallet) throw new \Exception('Unable to start wallet');

           if(!isset($data['state_id']) && $parent && $parent->getModel()->profile->state_id)
           {
               $data['state_id'] = $parent->getModel()->profile->state_id;
           }

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

    public function verified(): bool
    {
        return boolval($this->model->verified_at);
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

    public function getMeansOfIdentification(): ?string
    {
        $meansOfId = $this->getModel()->profile ? $this->getModel()->profile->means_of_identification : '';
        if(isJson($meansOfId))
        {

            $meansOfId = json_decode($meansOfId, false, JSON_UNESCAPED_SLASHES);
            return str_replace("//", "/", $meansOfId->path);
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

    public function purse()
    {
        $wallet = $this->model->wallets()->where('type', \App\Wallet::WALLET_TYPE_COMMISSION)->first();

        if(!$wallet) return null;
        return new Wallet($wallet);
    }


    public function checkWalletIsValid($wallet = null) : self
    {

        if(!$wallet)
            $wallet = $this->mainWallet();

        if(!$wallet || !$wallet->isValid()) throw new BilingException('Wallet is not in a valid state');

        return $this;
    }


    public function canChargeWallet($amount, $wallet=null)
    {
        if(!$wallet)
            $wallet = $this->mainWallet();

        $this->checkWalletIsValid($wallet);

        if($wallet->getAmount() < $amount) throw new BilingException('Insufficient funds');

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
                $authUser->chargeWallet($data['amount'], 'New saving created for ' . e($user->getName()), Transaction::LABEL_CONTRIBUTION);
                $contribData['created_by'] = $authUser->getId();
            }

            $data = $user->validateForTransaction($data);

            $saving = $user->makeNewSaving($data);

            $contribution = $saving->contributions()->create($contribData);
            SavingCommission::getInstance($contribution)->computeCommission();

            $contribution->sendContributionMessageToUser($user);

            event(new NewSavingCreated($saving));

            DB::commit();

            return $saving;
        } catch (\Exception $e) {
            Log::channel('KOLOO_USER')->error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }


    public function chargeWallet($amount, $reason='Charged', $label='')
    {

        $this->canChargeWallet($amount);

        $wallet = $this->mainWallet();

        event(new PreWalletBilled($wallet));

        $wallet->debit($amount );

        event(new WalletBilled($wallet, $amount, $reason, $label) );

    }


    public function creditWalletSource($amount, $wallet, $reason='Credit', $label='')
    {
        $this->checkWalletIsValid($wallet);

        $wallet->credit($amount );

        event(new WalletCredited($wallet, $amount, $reason, $label) );
    }

    public function chargeWalletSource($amount, $wallet, $reason='Charged', $label='')
    {
        $this->canChargeWallet($amount, $wallet);

        event(new PreWalletBilled($wallet));

        $wallet->debit($amount );

        event(new WalletBilled($wallet, $amount, $reason, $label) );

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
            ->where('amount', $data['amount'])
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

    public function writeTransaction($amount, $type, $remark = '', $label='', $wallet=null)
    {
        $data = [
            'type' => $type,
            'amount' => $amount,
            'trans_ref' => $this->makeTransactionRef(),
            'remark' => $remark,
            'label' => $label
        ];

        if($wallet)
        {
            $data['wallet_id'] = $wallet->getId();
        }
        return $this->model->transactions()
                ->create($data);

    }

    public function writeCreditTransaction($amount, $remark = '', $label='', $wallet=null)
    {
        return $this->writeTransaction($amount, Transaction::TRANSACTION_TYPE_CREDIT, $remark, $label, $wallet);
    }

    public function writeDebitTransaction($amount, $remark = '', $label='', $wallet=null)
    {
        return $this->writeTransaction($amount, Transaction::TRANSACTION_TYPE_DEBIT, $remark, $label, $wallet);
    }

    private function makeTransactionRef()
    {
        return Uuid::generate()->string;
    }

    public function updateLastLogin()
    {
        $this->model->last_login = now();
        $this->model->save();
    }

    public function suspend()
    {
        $this->model->is_suspended = now();
        $this->model->save();
    }

    public function unsuspend()
    {
        $this->model->is_suspended = null;
        $this->model->save();
    }

    public function isSuspended()
    {
        return $this->model->is_suspended;
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

        return $savings ? $savings->with('cycle:id,title,description')->latest()->orderBy('maturity', 'desc')->get() : [];
    }

    public function contributeToSaving(Saving $saving, $amount)
    {
        try {
            DB::beginTransaction();

             $saving->canAcceptNewContribution();

             $customer = User::findByInstance($saving->owner);
             User::checkExistence($customer);

            $this->chargeWallet($amount, 'New contribution for ' . e($customer->getName()), Transaction::LABEL_CONTRIBUTION);

            $contribData = ['amount' => $amount, 'created_by' => $this->getId()];

            $contribution = $saving->contributions()->create($contribData);
            SavingCommission::getInstance($contribution)->computeCommission();

            event(new NewContributionCreated($contribution));

            $customer->writeCreditTransaction($amount, 'New contribution', Transaction::LABEL_CONTRIBUTION);

            DB::commit();

            return $contribution;

        } catch (\Exception $e) {
            Log::channel('KOLOO_USER')->error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }


    public function setProvidusBankDetail(string $accountNumber, string $accountRef)
    {
        return $this->getModel()->update(['providus_account_number' => $accountNumber, 'providus_account_ref' => $accountRef]);
    }

    public function sendWelcomeSMS()
    {
        $message = '';
        $channel = 'sms';
        if($this->isCustomer())
        {
            $message  = sprintf(config('koloo.customer_welcome_sms'), $this->getAccountNumber());
        } else if(($this->isSuperAgent() || $this->isAgent()) && $this->isApproved())
        {
            $message  = sprintf(config('koloo.agent_welcome_sms'), $this->getModel()->providus_account_number);
        }

        if($message)
        {
            $data = [
                'message' => $message,
                'message_type' => $channel,
                'user_id' => $this->getId(),
                'sender' => $this->getId(),
                'subject' => ''
            ];
            event(new SendMessage(\App\Message::create($data), $channel));
        }

    }


    public static function rootUser() : self
    {
        $rootEmail = settings('root_email', config('koloo.root_email'));
        $phone = settings('root_phone', config('koloo.root_phone'));
        $countryCode = config('koloo.default_country');

        $check = Model::where('email', $rootEmail)->orWhere('is_root', true)->first();
        if($check) return new static($check);

        $data = [
            'email' => $rootEmail,
            'name' => 'Koloo',
            'country_code' => $countryCode,
            'phone' => PhoneNumber::format($phone, $countryCode),
            'email_verified_at' => now(),
            'password' =>  Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_root' => true,
            'status' => \App\User::STATUS_APPROVED,
            'account_number' => Model::makeAccountNumber()
        ];

        $user =  Model::create($data);

        $user->setAsAdmin();

        $wallet = \App\Wallet::start($user);
        if(!$wallet) throw new \Exception('Unable to start wallet');

        $user->profile()->create(['commission' => settings('min_commission'), 'commission_for_agent' => 60 * 100]);

        return  new static($user);
    }

    private function checkProfile()
    {
       if(!$this->model->profile)
       {
           Log::error($this->getName() . ' with ID ' . $this->getId() . ' has no profile');
           throw new \Exception('User profile not found for ' . $this->getName());
       }

    }
    public function getCommission()
    {
       $this->checkProfile();
       return $this->model->profile->commission * 100;
    }

    public function setCommission($commission)
    {
        $this->checkProfile();

        $this->model->profile->commission = $commission;
        $this->model->profile->save();

        return $this->model->profile->commission;
    }

    public function getCommissionForAgent()
    {
        $this->checkProfile();

        if($this->isAdmin() && !$this->model->profile->commission_for_agent)
        {
            return settings('max_commission') - $this->getCommission();
        }
        return $this->model->profile->commission_for_agent * 100;
    }

    public function setCommissionForAgent($commission)
    {
        $this->checkProfile();

        $this->model->profile->commission_for_agent = $commission;
        $this->model->profile->save();

        return $this->model->profile->commission_for_agent;
    }


    /**
     * This method is used to send OTP to the user before they can continue
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Koloo\User          $user
     *
     * @throws \Exception
     */
    public static function otpRequiredToContinue(Request $request, self $user)
    {
        if(!$user) throw new \Exception('User not found.');

        if(settings('transaction_auth') === 'pin')
        {
            static::useTransactionPinForTransaction($request, $user);
            return;
        }

        static::useOtpForTransaction($request, $user);

    }

    public static function useOtpForTransaction(Request $request, self $user)
    {

        $otp = new OtpVerification($user);
        $code = $request->input('otp');

        if(!$code || !$request->has('otp'))
        {
            $otp->send();
            throw new OTPRequiredException('Enter OTP to continue.');
        }

        if($request->has('otp') && !$otp->isValid($code))
        {
            throw new OTPRequiredException('Invalid OTP code entered.');
        }

        $otp->invalidateActiveOtp();
    }

    public static  function useTransactionPinForTransaction(Request $request, self $user)
    {

        $code = $request->input('otp');

        if(!$code || !$request->has('otp'))
        {
            throw new OTPRequiredException('Enter transaction pin to continue.');
        }

        if($request->has('otp') && !$user->isValidTransactionPin($code))
        {
            throw new OTPRequiredException('Invalid transaction pin entered.');
        }

    }


    public function earnCommission($amount, Contribution $contribution)
    {

        $wallet = $this->purse();
        if(!$wallet) throw new \Exception('Wallet for commission not set for this user.');

        $wallet->credit($amount);
        $this->model->transactions()
            ->create([
                'type' => Transaction::TRANSACTION_TYPE_CREDIT,
                'amount' => $amount,
                'label' => Transaction::LABEL_COMMISSION,
                'trans_ref' => $this->makeTransactionRef(),
                'remark' => 'Commission earned from savings. NGN' . $amount,
                'wallet_id' => $wallet->getId(),
            ]);

        event(new CommissionEarned($amount, $contribution));
    }

    public function getNewPasswordReset() : ?PasswordReset
    {
        PasswordReset::where('email', $this->getEmail())->delete();

        $days = settings()->get('password_reset_validity_days') ?: 5;

        $plainHash = sprintf('%s%s', sha1(str_random(32)),sha1($this->getId()));
        $result = PasswordReset::create([
           'hash' => Hash::make($plainHash),
           'expires_at' => now()->addDays($days),
           'email' => $this->getEmail()
        ]);

        if($result)
        {
            $result->plain_hash  = $plainHash;
            return $result;
        }

        return null;
    }

    public function passwordResetValid($code) : bool
    {
        $res =  PasswordReset::where('email', $this->getEmail())
                ->where('expires_at', '>', now())->first();
        if(!$res) return false;

        return Hash::check($code, $res->hash);
    }

    public function clearResetPassword()
    {
        PasswordReset::where('email', $this->getEmail())->delete();
    }

    public function clearCommission()
    {

        $this->model->profile->commission = 0;
        $this->model->profile->commission_for_agent = 0;
        $this->model->profile->save();
    }

    public static function findByIdentity($identity, $country='NG'): ?self
    {
        $user = null;

        $phone =  PhoneNumber::format($identity,$country);
        if($phone)
            $user = static::findByPhone($phone);

        if(!$user)
            $user = static::findByEmail($identity);

        if(!$user)
            $user = static::findByAccountNumber($identity);

        return $user;
    }


    public function checkPendingRequest()
    {
        $req  = CommissionPayout::whereNull('completed_by')
                        ->whereNull('paid')->where('user_id', $this->getId())->first();

        if($req) throw new \Exception('You have a pending request.');

        return true;
    }

    public function setParent(self $parent)
    {
        $this->model->parent_id = $parent->getId();
        $this->model->save();
    }

    public function getProvidusAccountNumber(): string
    {
        $api = app('App\Services\Monnify\Api');
        try {
            $res = $api->reserveAccountNumber($this->getName(), $this->getEmail(), $this->getId());
            $this->setProvidusBankDetail($res->accountNumber, $res->accountReference);
            return $res->accountNumber;
        } catch (\Exception $e) {
            $this->logInfo('Error getting new account number: ' . $e->getMessage() . ' at ' . $e->getFile() . ' on line ' . $e->getLine());
        }

        return '';
    }

    public function stats(Request $request)
    {
        $data = [
            'joined_on' => $this->model->created_at,
            'wallet_balance' => $this->mainWallet()->getAmount(),
            'purse_balance' => $this->purse()->getAmount(),
            'total_inflow_from_providus' => $this->getTotalInflowFromProvidus(),
            'paid_out' => $this->getTotalPaidOut(),
            'commission_earned' => $this->getTotalCommissionEarned(),
        ];

        $countAgents = function($accountType) {
            return $this->model->children()->whereHas('roles', function($query) use ($accountType){
                $query->where('name', $accountType);
            })->count();

        };

        if($this->isSuperAgent() || $this->isAdmin())
        {

            $data['super_agents_under_you'] = $countAgents(\App\User::ROLE_SUPER_AGENT);
            $data['agents_under_you'] = $countAgents(\App\User::ROLE_AGENT);
            $data['customers_under_you'] = $countAgents(\App\User::ROLE_CUSTOMER);

        }


        return $data;
    }

    public function getTotalInflowFromProvidus()
    {
        return $this->model->transactions()
            ->where('type', Transaction::TRANSACTION_TYPE_CREDIT)
            ->where('label', Transaction::LABEL_MONNIFY)
            ->sum('amount') / 100;

    }

    public function getTotalPaidOut()
    {
        return $this->model->transactions()
            ->where('type', Transaction::TRANSACTION_TYPE_DEBIT)
            ->where('label', Transaction::LABEL_PAYOUT)
            ->sum('amount') / 100;
    }

    public function getTotalCommissionEarned()
    {
        return $this->model->transactions()
            ->where('type', Transaction::TRANSACTION_TYPE_CREDIT)
            ->where('label', Transaction::LABEL_COMMISSION)
            ->sum('amount') / 100;
    }

    public function profileCompleted()
    {

    }

    public function setTransactionPin($pin)
    {
        $pin = Hash::make($pin);
        $this->model->transaction_pin = $pin;
        $this->model->save();
    }

    public function getHashedTransactionPin()
    {
        return $this->model->transaction_pin;
    }

    public function isValidTransactionPin($pin) : bool
    {
        return Hash::check($pin, $this->getHashedTransactionPin());
    }

    public function hasTransactionPin(): bool
    {
        return $this->getHashedTransactionPin() ? true : false;
    }

    public function update(array $data)
    {
        $this->getModel()->update([
            'phone' => $data['phone'],
            'name' => $data['name']
        ]);
        unset($data['name'], $data['phone']);
        return $this->getProfile()->update($data);
    }

    public function hasActiveSaving(): bool  {

        return (bool)$this->model->savings()
                                ->whereDate('maturity', '>', now())
                                 ->whereNull('completed')
                                ->count();
    }
}

<?php

namespace App\Koloo;



use App\OTP;
use Carbon\Carbon;
/**
 * Class OtpVerification
 *
 * @package \App\Koloo
 */
class OtpVerification
{


    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $otpCount;

    /**
     * @var OTP
     */
    private $lastOtp;
    /**
     * @var Carbon
     */
    private $resendDate;


    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function send(): self
    {
        /* TODO: Extract to a new method/hook

         * if ($this->user->isPhoneVerified()) {
            throw new \Exception("Phone is already verified.");
        }*/

        $this->loadOtpInformation();

        if (! $this->lastOtp) {
            $this->sendVerificationCode();
        }

        $this->determineResendDate();

        if (now()->greaterThanOrEqualTo($this->resendDate)) {
            $this->sendVerificationCode();
            $this->determineResendDate();

        }



        return $this;
    }

    private function sendVerificationCode()
    {
        $this->invalidateActiveOtp();
        $this->lastOtp = $this->user->sendOTP($this->determineChannel());
        $this->otpCount++;
    }


    public function getResendDate(): ?Carbon
    {
        return $this->resendDate;
    }

    public function getLastOtpSentAt(): ?Carbon
    {
        return $this->lastOtp ? $this->lastOtp->created_at->copy() : null;
    }

    public function getLastOtpChannel(): ?string
    {
        return $this->lastOtp ? $this->determineChannel($this->otpCount - 1) : null;
    }

    public function getLastOtp()
    {
        return $this->lastOtp;
    }

    public function verifyPhone(string $code = null): bool
    {

        if($this->isValid($code))
        {
            $this->user->getModel()->phone_verified = now();
            $this->user->getModel()->save();

           $this->delete($code);
        }


        return true;
    }


    /**
     * Check if OTP is still valid
     *
     * @param string $code
     *
     * @return bool
     */
    public function isValid(string $code): bool
    {
        if (! $code) return false;

       $this->lastOtp = OTP::where('code', $code)
            ->where('expire_at', '>', now())
            ->where('phone', $this->user->getPhone())
            ->orderByDesc('created_at')
            ->first();

        if (! $this->lastOtp OR ! $code) {
            return false;
        }

        return true;
    }


    private function delete(string $code)
    {
        $otp = OTP::where('otp', $code)
            ->where('expire_at', '>', now())
            ->where('phone', $this->user->getPhone())
            ->first();

        if($otp)
            $otp->delete();
    }

    private function determineResendDate(): Carbon
    {
        // Switch to email channel
        /*if ($this->otpCount > 2) {
            $this->resendDate = $this->lastOtp->created_at->copy()->addMinute();
        }
        // 24h wait period
        elseif ($this->otpCount === 2) {
            $this->resendDate = $this->lastOtp->created_at->copy()->addHours(24);
        }
        // 1 minute wait period
        else {
            $this->resendDate = $this->lastOtp->created_at->copy()->addMinute();
        }*/
        $this->resendDate = $this->lastOtp->created_at->copy();

        return $this->resendDate;
    }


    /**
     * If we need to send it via SMS or email
     * A way to keep sms cost low
     *
     * @param int|null $count
     *
     * @return string
     */
    private function determineChannel(int $count = null): string
    {
       // $count = $count ?: $this->otpCount;

        //TODO: take '2' out into a config file
       // return $count >= 2 ? 'email' : 'sms';
        return 'both';
    }


    private function loadOtpInformation()
    {
        $this->lastOtp = OTP::where('phone', $this->user->getPhone())->orderByDesc('created_at')->first();
        $this->otpCount = OTP::where('phone', $this->user->getPhone())->count();
    }

    public function invalidateActiveOtp()
    {
        OTP::where('phone', $this->user->getPhone())
            ->where('expire_at', '>', now())
            ->update(['expire_at' => now()->subYear(), 'response' => null, 'code' => '']);
    }

    public function getExpiresAt()
    {
        return $this->getLastOtp() ? $this->getLastOtp()->expire_at : null;
    }



}

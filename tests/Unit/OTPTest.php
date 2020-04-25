<?php

namespace Tests\Unit;
use App\OTP;


use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OTPTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_new_otp()
    {
        $otp = factory('App\OTP')->create();

        $this->assertNotNull($otp);
        $this->assertInstanceOf(OTP::class, $otp);
    }
}

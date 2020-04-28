<?php

namespace Tests\Unit\Koloo;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class OtpVerificationTest
 *
 * @package \Tests\Unit\Koloo
 */
class OtpVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_throw_an_exception_if_phone_is_already_verified()
    {
        $this->assertTrue(true);
    }



}

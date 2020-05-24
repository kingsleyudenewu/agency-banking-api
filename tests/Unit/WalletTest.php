<?php

namespace Tests\Unit;

use App\User;
use \Tests\TestCase;
use App\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;



class WalletTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_be_created()
    {
        $wallet = factory('App\Wallet')->create();

        $this->assertNotNull($wallet);
        $this->assertInstanceOf(Wallet::class, $wallet);
    }


    /** @test */
    public function must_belong_to_a_user()
    {

        $wallet = factory('App\Wallet')->create();

        $this->assertNotNull($wallet);
        $this->assertInstanceOf(User::class, $wallet->user);
    }

}

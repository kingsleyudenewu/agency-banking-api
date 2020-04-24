<?php

namespace Tests\Unit;



use App\Country;
use App\Profile;
use App\Wallet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function has_one_profile()
    {
        $user = factory('App\User')->create();

        factory('App\Profile')->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Profile::class, $user->profile);

    }


    /** @test */
    public function has_one_wallet()
    {
        $user = factory('App\User')->create();

        factory('App\Wallet')->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Wallet::class, $user->wallet);

    }

    /** @test */
    public function should_have_a_country()
    {
        $user = factory('App\User')->create();

        $this->assertInstanceOf(Country::class, $user->country);
    }


}

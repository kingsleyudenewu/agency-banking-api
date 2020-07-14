<?php

namespace Tests\Unit;



use App\Country;
use App\Profile;
use Illuminate\Database\Eloquent\Collection;
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
    public function has_many_wallet()
    {
        $user = factory('App\User')->create();

        factory('App\Wallet')->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Collection::class, $user->wallets);

    }

    /** @test */
    public function should_have_a_country()
    {
        $countryCode = factory('App\Country')->create()->code;
        $user = factory('App\User')->create(['country_code' => $countryCode]);

        $this->assertInstanceOf(Country::class, $user->country);
    }


    /** @test */
    public function a_user_can_have_many_savings()
    {
        $user = factory('App\User')->create();

        $this->assertInstanceOf(Collection::class, $user->savings);
    }


    /** @test */
    public function a_user_can_have_many_savings_created_by_them()
    {
        $user = factory('App\User')->create();

        $this->assertInstanceOf(Collection::class, $user->savingsCreated);
    }


    /** @test */
    public function a_user_can_have_many_transactions()
    {
        $user = factory('App\User')->create();

        $this->assertInstanceOf(Collection::class, $user->transactions);
    }


}

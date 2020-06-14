<?php

namespace Tests\Unit\Koloo;

use App\Koloo\Wallet;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
/**
 * Class WalletTest
 *
 * @package \Tests\Unit\Koloo
 */
class WalletTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_new_instance()
    {
        $wallet = new Wallet(factory('App\Wallet')->create());

        $this->assertInstanceOf(\App\Wallet::class, $wallet->getModel());
    }

    /** @test */
    public function can_get_properties_from_model()
    {
        $w = factory('App\Wallet')->create(); // Eloquent model
        $wallet = new Wallet($w);

        $this->assertEquals($w->id, $wallet->getId());
        $this->assertInstanceOf(\App\Koloo\User::class, $wallet->getOwner());
        $this->assertInstanceOf(User::class, $wallet->getOwner()->getModel());
    }

    /** @test */
    public function can_receive_positive_credit()
    {
        $w = factory('App\Wallet')->create(); // Eloquent model
        $wallet = new Wallet($w);
        $oldValue = $w->amount;

        $wallet->credit(100);

        $this->assertEquals($oldValue + 100, $wallet->getAmount());

    }


    /** @test */
    public function can_receive_negative_credit()
    {
        $w = factory('App\Wallet')->create(); // Eloquent model
        $wallet = new Wallet($w);
        $oldValue = $w->amount;

        $wallet->credit(-100);

        $this->assertEquals($oldValue -100, $wallet->getAmount());

    }

    /** @test */
    public function can_be_debited()
    {

        $w = factory('App\Wallet')->create(); // Eloquent model
        $wallet = new Wallet($w);
        $oldValue = $w->amount;

        $wallet->debit(100);

        $this->assertEquals($oldValue -  100, $wallet->getAmount());

    }


    /** @test */
    public function can_check_wallet_validity()
    {

        $w = factory('App\Wallet')->create(['hash' => null]); // Eloquent model

        $wallet = new Wallet($w);

        // At first, it should be valid
        $this->assertTrue($wallet->isValid());

        // Credit should still remain valid
        $wallet->credit(100);
        $this->assertTrue($wallet->isValid());

        // Credit should still remain valid
        $wallet->credit(-100);
        $this->assertTrue($wallet->isValid());

        // Debit should still remain valid
        $wallet->debit(100);
        $this->assertTrue($wallet->isValid());

        // Illegal attempt should render the wallet invalid
        $w->amount = 1000;
        $w->save();
        $this->assertFalse($wallet->isValid());



    }

    /** @test */
    public function can_start_wallets_for_a_new_user()
    {

        $user = factory('App\User')->create();

        $this->assertEquals(0, $user->wallets()->count());

        $wallets = \App\Wallet::start($user);

        $this->assertEquals(2, $user->wallets()->count());
        $this->assertEquals(2, $wallets->count());

    }


    /** @test */
    public function do_not_start_morethan_two_wallets_for_the_user()
    {
        $user = factory('App\User')->create();

        $this->assertEquals(0, $user->wallets()->count());

        \App\Wallet::start($user);
        $this->assertEquals(2, $user->wallets()->count());

        \App\Wallet::start($user);
        $this->assertEquals(2, $user->wallets()->count());

    }


}

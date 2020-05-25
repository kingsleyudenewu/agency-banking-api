<?php

namespace Tests\Unit;


use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_transaction_belongs_to_a_user()
    {
        $transaction = factory('App\Transaction')->create();

        $this->assertInstanceOf(User::class, $transaction->owner);
    }

}

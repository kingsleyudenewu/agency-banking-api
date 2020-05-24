<?php

namespace Tests\Feature\Api\Saving;

use App\Koloo\User;
use App\Koloo\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class SavingsTest - Test savings controller
 *
 * @package \Tests\Feature\Api\Saving
 */
class SavingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadUsersWithPermission();
    }

    private function generateProfileData(array $more = []) {

        $country = factory('App\Country')->create(['code' => 'NG', 'currency' => 'NGN', 'name' => 'Nigeria']);
        $userData = factory('App\User')->raw(['country_code' => $country->code]);
        $customerData = factory('App\Profile')->raw();
        return array_merge($userData, $customerData, $more);

    }

    private function userWithWallet()
    {
        $profile = User::createWithProfile($this->generateProfileData());
        return $profile;
    }

    private function walletWithFund(Wallet $wallet, int $amount) {
        $wallet->credit($amount*100);
        return $wallet;
    }

    /** @test */
    public function a_user_with_the_right_access_can_create_savings()
    {
        $amountToCredit = 50000;

        $user = $this->userWithWallet();
        $this->assertNotNull($user);

        $this->signIn($user->getModel());

        $wallet = $this->walletWithFund($user->mainWallet(), $amountToCredit);

        $this->assertEquals($amountToCredit*100, $wallet->getAmount());

        $customer = $this->userWithWallet();
        $this->assertNotNull($customer);


        $customer = $this->getCustomerWithFund(50000);

        $data = [
            'saving_cycle_id' => factory('App\SavingCycle')->create()->id,
            'amount' => 1500,
            'owner_id' => $customer->getId()
        ];

        $this->postJson(route('api.savings.new'), $data)
                ->assertJson([
                    'status' => 'success',
                    'data' => ['amount' => 1500 * 100]
                ]);

    }


    private function getCustomerWithFund($amountToCredit) {

        $user = $this->userWithWallet();
        $this->assertNotNull($user);

        $this->signIn($user->getModel());

        $wallet = $this->walletWithFund($user->mainWallet(), $amountToCredit);

        $this->assertEquals($amountToCredit*100, $wallet->getAmount());

        $customer = $this->userWithWallet();
        $this->assertNotNull($customer);

        return $customer;
    }

    /** @test */
    public function user_can_not_create_a_billing_with_insufficient_funds()
    {
        $this->withoutExceptionHandling();

        $customer = $this->getCustomerWithFund(500);

        $data = [
            'saving_cycle_id' => factory('App\SavingCycle')->create()->id,
            'amount' => 1500,
            'owner_id' => $customer->getId()
        ];

        $this->postJson(route('api.savings.new'), $data)
            ->assertJson([
                'status' => 'error',
                'message' => 'Insufficient funds',
                'errors' => null
            ]);
    }

}

<?php

namespace Tests\Unit;

use App\Koloo\SavingCommission;
use App\Koloo\User;
use App\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class CommissionTest
 *
 * @package \Tests\Unit
 */
class CommissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withSettings()->loadUsersWithPermission();
    }


    /** @test */
    public function super_agent_and_the_root_user_can_earn_commission_from_a_customer_savings_if_the_transaction_was_done_by_a_regular_agent()
    {

        $customerProfile = factory('App\Profile')->create([
        ]);
        Wallet::start($customerProfile->user);
        $customer = new User($customerProfile->user);



        $savingCycle = factory('App\SavingCycle')->create(); // 30 days savings
        $saving = factory('App\Saving')->create([
            'amount' => 3000,
            'saving_cycle_id' => $savingCycle->id,
            'owner_id' => $customer->getId(),
            'creator_id' => $this->agentUser->getId(),
            'maturity' => now()->addDays(30)
            ]);



        $contribution = factory('App\Contribution')->create([
            'amount' => 3000,
            'created_by' => $this->agentUser->getId(),
            'saving_id' => $saving->id
        ]);

      $savingCommission = SavingCommission::getInstance($contribution);
      $savingCommission->computeCommission();


      $this->assertEquals(99, $savingCommission->getSystemDeductions());
      $this->assertEquals(59.40, $this->agentUser->purse()->getAmount()); // 60% profit
      $this->assertEquals(19.80, $this->adminUser->purse()->getAmount()); // 20% profit
      $this->assertEquals(19.80, $this->superAgentUser->purse()->getAmount()); // 20% profit
      $total =   $this->agentUser->purse()->getAmount() + $this->adminUser->purse()->getAmount() + $this->superAgentUser->purse()->getAmount();
      $this->assertEquals(99, $total);

    }


    /** @test */
    public function super_agent_should_earn_regular_agent_commission_if_transaction_was_done_by_them_and_the_rest_goes_to_the_root_user()
    {

        $customerProfile = factory('App\Profile')->create([
        ]);
        Wallet::start($customerProfile->user);
        $customer = new User($customerProfile->user);



        $savingCycle = factory('App\SavingCycle')->create(); // 30 days savings
        $saving = factory('App\Saving')->create([
            'amount' => 3000,
            'saving_cycle_id' => $savingCycle->id,
            'owner_id' => $customer->getId(),
            'creator_id' => $this->superAgentUser->getId(),
            'maturity' => now()->addDays(30)
        ]);



        $contribution = factory('App\Contribution')->create([
            'amount' => 3000,
            'created_by' => $this->superAgentUser->getId(),
            'saving_id' => $saving->id
        ]);

        $savingCommission = SavingCommission::getInstance($contribution);
        $savingCommission->computeCommission();


        $this->assertEquals(99, $savingCommission->getSystemDeductions());
        $this->assertEquals(59.40, $this->superAgentUser->purse()->getAmount()); // 60% profit
        $this->assertEquals(39.6, $this->adminUser->purse()->getAmount()); // 40% profit
        $this->assertEquals(99, $this->superAgentUser->purse()->getAmount() + $this->adminUser->purse()->getAmount());

    }

}

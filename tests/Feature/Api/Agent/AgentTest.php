<?php

namespace Tests\Feature\Api\Agent;

use App\Events\AgentAccountCreated;
use App\Koloo\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class AgentTest
 *
 * @package \Tests\Feature\Api\Agent
 */
class AgentTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $superAgentUser;
    protected $agentUser;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed');

        $this->adminUser = User::findOneByRole(\App\User::ROLE_ADMIN);
        $this->superAgentUser = User::findOneByRole(\App\User::ROLE_SUPER_AGENT);

    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // RefreshDatabase is going to clean the data store, no need to tear down
        $this->superAgentUser = null;
        $this->adminUser = null;

    }

    /** @test */

    public function can_create_agent_with_the_right_credentials()
    {

        $authUser = $this->signIn($this->adminUser->getModel());

        // We need a valid country
        factory('App\Country')->create(['code' => 'NG']);

        Event::fake([AgentAccountCreated::class]);

        $userData = factory('App\User')->raw(['phone' => '08129531720']);
        $profileData = factory('App\Profile')->raw([
                'business_phone' => '08037312520',
                'next_of_kin_phone' => '08054473524'
        ]);

        $payload = array_merge($profileData, $userData);
        $payload['country'] = 'NG';

        $this->postJson(route('api.agents.create-new'), $payload)
                ->dump()
                ->assertStatus(200)
                ->assertJson([
                    "status" => "success",
                    "message" => "OK",
                    "data" => ["first_name" => $userData['first_name']]
                ]);

        Event::assertDispatched(AgentAccountCreated::class, 1);
    }

}


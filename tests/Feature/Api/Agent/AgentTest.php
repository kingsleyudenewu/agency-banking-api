<?php

namespace Tests\Feature\Api\Agent;

use App\Events\AgentAccountCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    /** @test */

    public function can_create_agent_with_the_right_credentials()
    {


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
                ->assertStatus(200)
                ->assertJson([
                    "status" => "success",
                    "message" => "OK",
                    "data" => ["first_name" => $userData['first_name']]
                ]);

        Event::assertDispatched(AgentAccountCreated::class, 1);
    }

}


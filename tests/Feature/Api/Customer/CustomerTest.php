<?php

namespace Tests\Feature\Api\Customer;

use App\Koloo\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class CustomerTest
 *
 * @package \Tests\Feature\Api\Customer
 */
class CustomerTest extends TestCase
{


    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadUsersWithPermission();
    }

    /** @test */
    public function can_create_a_customer_account_with_the_right_data()
    {

        $this->withoutExceptionHandling();

        $payload = $this->profile_creation_data();

        $res = $this->postJson(route('api.customers.new'), $payload)
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $content = $res->json();

        $user = User::find($content['data']['id']);
        $this->assertNotNull($user);
        $this->assertTrue($user->getModel()->hasRole('customer'));
        $this->assertFalse($user->getModel()->hasRole('admin'));
        $this->assertFalse($user->getModel()->hasRole('super-agent'));
        $this->assertFalse($user->getModel()->hasRole('agent'));
        $this->assertNull($user->getParentID());

    }


    /** @test */
    public function authenticated_user_can_create_a_customer_account_with_the_right_data()
    {

        $auhUser = $this->signIn($this->agentUser->getModel());

        $this->withoutExceptionHandling();

        $payload = $this->profile_creation_data();

        $res = $this->postJson(route('api.customers.new'), $payload)
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $content = $res->json();

        $user = User::find($content['data']['id']);
        $this->assertNotNull($user);
        $this->assertTrue($user->getModel()->hasRole('customer'));
        $this->assertFalse($user->getModel()->hasRole('admin'));
        $this->assertFalse($user->getModel()->hasRole('super-agent'));
        $this->assertFalse($user->getModel()->hasRole('agent'));
        $this->assertEquals($auhUser->id, $user->getParentID());

    }


    private function profile_creation_data()
    {
        $userData = factory('App\User')->raw(['phone' => '08129531720']);
        $profileData = factory('App\Profile')->raw([
            'phone' => '08037312520',
            'next_of_kin_phone' => '08054473524',
            'secondary_phone' => '08054473523'
        ]);

        $payload = array_merge($profileData, $userData);
        $payload['country_code'] = 'NG';

        return $payload;
    }

}

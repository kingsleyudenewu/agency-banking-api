<?php

namespace Tests\Unit\Koloo;

use App\Koloo\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_find_user_by_id()
    {
        $id = factory('App\User')->create()->id;

        $user = User::find($id);

        $this->assertNotNull($user);
        $this->assertInstanceOf(\App\User::class, $user->getModel());
    }

    /** @test */
    public function api_token_should_be_null_for_newly_created_user()
    {
        $id = factory('App\User')->create()->id;

        $user = User::find($id);

        $this->assertNull($user->getAPIToken());

    }



    /** @test */
    public function newAPIToken_should_create_new_token()
    {

        $user = $this->getUser();

        $user->newAPIToken();

        $plain = $user->getPlainToken();

        $this->assertEquals(64, strlen($user->getAPIToken()));
        $this->assertEquals(80, strlen($plain));

    }

    private function getUser() : User
    {
        $id = factory('App\User')->create()->id;

        return User::find($id);
    }

    /** @test */
    public function can_store_and_get_user_settings()
    {
        $user = $this->getUser();

        $this->assertDatabaseMissing('settings', ['group' => $user->getId(), 'name' => 'test', 'val' => 'test']);

        $user->settings()->set('test', 'test');

        $this->assertEquals('test', $user->settings()->get('test'));

        $this->assertDatabaseHas('settings', ['group' => $user->getId(), 'name' => 'test', 'val' => 'test']);

    }



}

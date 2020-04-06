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
        $id = factory('App\User')->create()->id;

        $user = User::find($id);

        $user->newAPIToken();

        $plain = $user->getPlainToken();

        $this->assertEquals(64, strlen($user->getAPIToken()));
        $this->assertEquals(80, strlen($plain));

    }



}

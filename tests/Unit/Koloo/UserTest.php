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
    public function can_find_by_phone()
    {
        $factoryUser = factory('App\User')->create();

        $user = User::findByPhone($factoryUser->phone);

        $this->assertNotNull($user);
        $this->assertInstanceOf(\App\User::class, $user->getModel());
    }

    /** @test */
    public function find_by_phone_return_null_for_invalid_phone()
    {
        $user = User::findByPhone('invalid phone');

        $this->assertNull($user);

    }


    /** @test */
    public function find_should_return_null_for_invalid_id()
    {

        $user = User::find(1);

        $this->assertNull($user);

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


    /** @test */
    public function can_check_if_an_instance_belongs_to_another_instance()
    {
        $user1 = User::find(factory('App\User')->create()->id);
        $user2 = User::find(factory('App\User')->create(['parent_id' => $user1->getId()])->id);

        $this->assertTrue($user2->belongsTo($user1));
        $this->assertFalse($user1->belongsTo($user2));

    }

    /** @test */
    public function can_check_if_an_instance_does_not_belongs_to_another_instance()
    {
        $user1 = User::find(factory('App\User')->create()->id);
        $user2 = User::find(factory('App\User')->create()->id);

        $this->assertFalse($user2->belongsTo($user1));
        $this->assertFalse($user1->belongsTo($user2));

    }


    /** @test */
    public function can_get_parent_id()
    {
        $user1 = User::find(factory('App\User')->create()->id);
        $user2 = User::find(factory('App\User')->create(['parent_id' => $user1->getId()])->id);

       $this->assertNotNull($user2->getParentID());
       $this->assertEquals($user1->getId(), $user2->getParentID());

    }


}

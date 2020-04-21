<?php

namespace Tests\Unit;

use App\Profile;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_create_profile()
    {
        $profile = factory('App\Profile')->create();


        $this->assertNotNull($profile);
        $this->assertInstanceOf(Profile::class, $profile);
    }


    /** @test */
    public function a_profile_belongs_to_a_user()
    {
        $user = factory('App\User')->create();
        $profile = factory('App\Profile')->create(['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $profile->user);
    }

    /** @test */
    public function default_profile_status_should_be_setting_up()
    {
        $profile = factory('App\Profile')->create();

        $this->assertTrue($profile->isSettingUp());
    }

    /** @test */
    public function profile_setup_can_be_marked_completed()
    {
        $profile = factory('App\Profile')->create();

        $profile->setupCompleted();

        $this->assertFalse($profile->isSettingUp());
    }
}

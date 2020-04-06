<?php

namespace Tests\Unit;



use App\Profile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function user_has_one_profile()
    {
        $user = factory('App\User')->create();

        factory('App\Profile')->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Profile::class, $user->profile);

    }



}

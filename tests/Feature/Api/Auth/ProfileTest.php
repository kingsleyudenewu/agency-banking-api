<?php

namespace Tests\Feature\Api\Auth;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ProfileTest
 *
 * @package \Tests\Feature\Api\Auth
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->loadUsersWithPermission();
    }


    /** @test */
    public function admin_can_not_fetch_another_user_profile_with_invalid_profile_id()
    {

        $admin =   $this->adminUser;
        $this->signIn($admin->getModel());


        $this->json('GET', route('api.profile.get') . '?id=inavlid_profile_id' )
            ->assertStatus(404)
            ->assertJson(['status' => 'error', 'errors' => null, 'message' => 'User not found']);
    }






    /** @test */
    public function must_not_be_able_to_fetch_profile_for_none_authenticated_user()
    {

        $this->json('GET', route('api.profile.get'))
            ->assertStatus(401) ;
    }



}

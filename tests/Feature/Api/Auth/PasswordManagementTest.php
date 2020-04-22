<?php

namespace Tests\Feature\Api\Auth;


use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class PasswordManagementTest
 *
 * @package \Tests\Feature\Api\Auth
 */
class PasswordManagementTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadUsersWithPermission();
    }

    /** @test */
    public function admin_can_set_new_password()
    {
        $this->withoutExceptionHandling();

        $this->signIn($this->adminUser->getModel());

        $user = factory('App\User')->create();
        $oldPassword = $user->password; // Old password hash
        $newPassword = 'a2A#45124';

        $this->json('POST', route('api.admin.set-password'), ['id' => $user->id, 'password' => $newPassword])
                ->assertStatus(200)
                ->assertJson(['status' => 'success']);

        // Check to make sure password hash is different
        $updatedUser = User::find($user->id);

        $this->assertNotEquals($oldPassword, $updatedUser->password);
    }





}

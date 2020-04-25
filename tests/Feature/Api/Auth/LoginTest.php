<?php

namespace Tests\Feature\Api\Auth;


use App\Koloo\PhoneNumber;
use App\Koloo\User;
use Tests\TestCase;


use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class Login
 *
 * @package \Tests\Feature\Api\Auth
 */
class LoginTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_login_with_valid_credential()
    {

        $this->loadUsersWithPermission();

        $this->postJson(route('api.auth.login.post'), [
            'identity' => $this->adminUser->getPhone(),
             'country' => 'NG',
             'password' => 'password'
            ]
        )->assertStatus(200)
        ->assertJsonMissing(['errors'])
            ->assertJson(['status' => 'success'])
      ;

    }

    /** @test */
    public function identity_is_required()
    {

        $this->post(route('api.auth.login.post'),
            [
                'identity' =>'',
                'country' => 'NG',
                'password' => 'password'
            ])->assertStatus(302)
            ->assertSessionHasErrors('identity');

    }

    /** @test */
    public function country_is_required()
    {
        $this->post(route('api.auth.login.post'),
            [
                'identity' =>'2348066100671',
                'country' => '',
                'password' => 'password'
            ])->assertStatus(302)
            ->assertSessionHasErrors('country');


    }


    /** @test */
    public function password_is_required()
    {
        $this->post(route('api.auth.login.post'),
            [
                'identity' =>'2348066100671',
                'country' => 'NG',
                'password' => ''
            ])->assertStatus(302)
            ->assertSessionHasErrors('password');


    }

    /** @test */
    public function do_not_allow_invalid_country_code()
    {
        $this->post(route('api.auth.login.post'),
            [
                'identity' =>'2348066100671',
                'country' => 'NG',
                'password' => 'password'
            ])->assertStatus(302)
            ->assertSessionHasErrors('country');


    }

    /** @test */
    public function logged_in_user_must_not_be_allowed_to_login()
    {

        $this->signIn();

        $this->post(route('api.auth.login.post'),
            [
                'identity' =>'2348066100671',
                'country' => 'NG',
                'password' => 'password'
            ])->assertStatus(403);
    }


    /** @test */
    public function login_should_fail_if_identity_is_valid_and_password_is_not()
    {
        factory('App\Country')->create(['code' => 'NG']);

        $countryCode = 'NG';
        $id = factory('App\User')->create([
            'phone' => PhoneNumber::format('08066100671', $countryCode)
        ])->id;

        $user = User::find($id);

        $this->postJson(route('api.auth.login.post'), [
                'identity' => $user->getPhone(),
                'country' => $countryCode,
                'password' => 'invalid_password'
            ]
        )->assertStatus(400)
            ->assertJson(['status' => 'error', 'message' => 'Login and/or password are incorrect.'])
        ;

    }
}

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
class Login extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_login_with_valid_credential()
    {
        $countryCode = 'NG';
        $id = factory('App\User')->create([
            'phone' => PhoneNumber::format('08066100671', $countryCode)
        ])->id;

        $user = User::find($id);

        $this->postJson(route('api.auth.login.post'), [
            'identity' => $user->getPhone(),
             'country' => $countryCode,
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
            ])->assertSessionHasErrors('identity');

    }

    /** @test */
    public function country_is_required()
    {
        $this->post(route('api.auth.login.post'),
            [
                'identity' =>'2348066100671',
                'country' => '',
                'password' => 'password'
            ])->assertSessionHasErrors('country');

    }


    /** @test */
    public function password_is_required()
    {
        $this->post(route('api.auth.login.post'),
            [
                'identity' =>'2348066100671',
                'country' => 'NG',
                'password' => ''
            ])->assertSessionHasErrors('password');

    }

    /** @test */
    public function do_not_allow_invalid_country_code()
    {
        $this->post(route('api.auth.login.post'),
            [
                'identity' =>'2348066100671',
                'country' => 'NG',
                'password' => 'password'
            ])->assertSessionHasErrors('country');

    }
}

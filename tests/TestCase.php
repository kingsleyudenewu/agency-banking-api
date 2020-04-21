<?php

namespace Tests;

use App\Koloo\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $adminUser;
    protected $superAgentUser;
    protected $agentUser;

    /**
     * Load user with permission using the seeder
     */
    protected function loadUsersWithPermission()
    {

        Artisan::call('db:seed');

        // We need a valid country
        factory('App\Country')->create(['code' => 'NG']);

        $this->adminUser = User::findOneByRole(\App\User::ROLE_ADMIN);
        $this->superAgentUser = User::findOneByRole(\App\User::ROLE_SUPER_AGENT);
        $this->agentUser = User::findOneByRole(\App\User::ROLE_AGENT);
    }


    protected function signIn($user = null, $driver="api") {

        $user =  $user ?: factory('App\User')->create();

        $this->actingAs($user, $driver);

        return $user;
    }

    protected function signInAsAdmin($user = null, $driver="api")
    {
        return $this->signIn($user, $driver);

    }

    protected function assertArraySimilar(array $expected, array $array)
    {
        $this->assertTrue(count(array_diff_key($array, $expected)) === 0);
        foreach ($expected as $key => $value) {
            if (is_array($value)) {
                $this->assertArraySimilar($value, $array[$key]);
            } else {
                $this->assertContains($value, $array);
            }
        }
    }

    protected function withSettings()
    {
        $this->artisan('db:seed --class=SettingsSeeder');
        return $this;
    }

    protected function getSetting($key, $group=null)
    {

        return $group ?
            settings()->group($group)->get($key, env(strtoupper($key)))  :
            settings()->get($key, env(strtoupper($key)));
    }
}

<?php

namespace Tests;

use App\Koloo\User;
use App\Wallet;
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

        $this->adminUser = User::rootUser();

        $this->superAgentUser = User::findOneByRole(\App\User::ROLE_SUPER_AGENT);
        $this->superAgentUser->setParent($this->adminUser);
        Wallet::start($this->superAgentUser->getModel());

        $this->agentUser = User::findOneByRole(\App\User::ROLE_AGENT);
        $this->agentUser->setParent($this->superAgentUser);
        Wallet::start($this->agentUser->getModel());


        factory('App\Profile')->create([
            'user_id' => $this->superAgentUser->getId(),
            'commission' => 20 * 100,
            'commission_for_agent' => 60 * 100
        ]);
        factory('App\Profile')->create([
             'user_id' => $this->agentUser->getId(),
                'commission' => 60 * 100,
                'commission_for_agent' => 0
        ]);

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

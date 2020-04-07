<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function signIn($user = null, $driver="api") {

        $user =  $user ?: factory('App\User')->create();

        $this->actingAs($user, $driver);

        return $user;
    }

    protected function signInAsAdmin($user = null, $driver="api")
    {
        $user = $this->signIn($user, $driver);

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
}

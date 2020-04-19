<?php

namespace Tests\Unit;

use App\Http\Controllers\APIBaseController;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class APIBaseControllerTest
 *
 * @package \Tests\Unit
 */
class APIBaseControllerTest  extends \Tests\TestCase
{

    use RefreshDatabase;

    /** @test */
    public function test_success_response_with_user()
    {
        $controller = new APIBaseController();

        $user = $this->signIn(factory('App\User')->create());

        $res = json_decode($controller->successResponseWithUser()->content());

        $this->assertNotNull($res->data);
        $this->assertNotNull($res->data->user);

        $this->assertEquals($user->id, $res->data->user->id);
    }
}

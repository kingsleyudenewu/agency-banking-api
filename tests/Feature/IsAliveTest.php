<?php

namespace Tests\Feature;


use Tests\TestCase;

class IsAliveTest extends TestCase
{
    /** @test */
    public function api_route_should_return_200_status_code()
    {
        $response = $this->get('/api/v1');

        $response->assertStatus(200);
    }
}

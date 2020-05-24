<?php

namespace Tests\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class SavingCycleTest
 *
 * @package \Tests\Feature\Api\Admin
 */
class SavingCycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadUsersWithPermission();
    }

    /** @test */
    public function admin_can_create_a_new_saving_cycle()
    {

       $this->signIn($this->adminUser->getModel());

        $data = [
            'title' => '30 days saving',
            'description' => 'test description',
            'duration' => 20,
            'min_saving_frequent' => 70,
            'charge_type' => 'flat',
            'min_saving_amount' => 1500
        ];

        $res = $this->postJson(route('api.admin.savings.cycle.create'), $data)
                ->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    ]);

        $jsonData = $res->json()["data"];

        $this->assertEquals($data['title'], $jsonData['title']);
        $this->assertEquals($data['description'], $jsonData['description']);
        $this->assertEquals($data['duration'], $jsonData['duration']);
        $this->assertEquals(36, strlen($jsonData["id"]));
        $this->assertNotNull($jsonData["created_at"]);
        $this->assertNotNull($jsonData["updated_at"]);

    }


    /** @test */
    public function title_is_required_to_create_a_new_saving_cycle()
    {

        $this->signIn($this->adminUser->getModel());

        $data = [
            'title' => '',
            'description' => 'test description',
            'duration' => 20
        ];

        $this->postJson(route('api.admin.savings.cycle.create'), $data)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'title' => ['The title field is required.']
                ]
            ]);


    }

    /** @test */
    public function duration_is_required_to_create_a_new_saving_cycle()
    {

        $this->signIn($this->adminUser->getModel());

        $data = [
            'title' => 'test title',
            'description' => 'test description',
            'duration' => null
        ];

        $this->postJson(route('api.admin.savings.cycle.create'), $data)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'duration' => ['The duration field is required.']
                ]
            ]);


    }

    /** @test */
    public function duration_must_be_a_number_to_create_a_new_saving_cycle()
    {

        $this->signIn($this->adminUser->getModel());

        $data = [
            'title' => 'test title',
            'description' => 'test description',
            'duration' => "sss"
        ];

        $this->postJson(route('api.admin.savings.cycle.create'), $data)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'duration' => ['The duration must be a number.']
                ]
            ]);


    }


    /** @test */
    public function non_admin_can_not_create_a_new_saving_cycle()
    {

        $this->signIn($this->agentUser->getModel());

        $data = [  ];

        $res = $this->postJson(route('api.admin.savings.cycle.create'), $data)
            ->assertStatus(401);

    }


    /** @test */
    public function admin_can_fetch_all_saving_cycles()
    {

        $this->signIn($this->adminUser->getModel());

        $this->get(route('api.admin.savings.cycle.get'))
            ->assertStatus(200);

    }

    /** @test */
    public function non_admin_must_not_fetch_all_saving_cycles()
    {

        $this->signIn($this->agentUser->getModel());

        $this->get(route('api.admin.savings.cycle.get'))
            ->assertStatus(401);

    }


}

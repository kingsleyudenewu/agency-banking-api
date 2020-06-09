<?php

namespace Tests\Feature\Api\Customer;

use App\Koloo\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class CustomerTest
 *
 * @package \Tests\Feature\Api\Customer
 */
class CustomerTest extends TestCase
{


    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadUsersWithPermission();
    }



    /** @test */
    public function logged_in_can_create_a_new_customer()
    {


        $authUser = $this->agentUser;
        $this->signIn($authUser->getModel());

        $payload = $this->profile_creation_data();

        $disk = $this->getSetting('document_storage_driver');
        $path = $this->getSetting('document_storage_path');
        $maxSize = $this->getSetting('document_storage_max_size'); // default to 2mb

        Storage::fake($disk);


        $file = UploadedFile::fake()->create('means_of_identification.png', $maxSize, 'image/png');
        $payload['means_of_identification'] = $file;


        $res = $this->postJson(route('api.customers.new'), $payload)
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $content = $res->json();

        $user = User::find($content['data']['id']);
        $this->assertNotNull($user);
        $this->assertTrue($user->getModel()->hasRole('customer'));
        $this->assertFalse($user->getModel()->hasRole('admin'));
        $this->assertFalse($user->getModel()->hasRole('super-agent'));
        $this->assertFalse($user->getModel()->hasRole('agent'));
        $this->assertEquals($authUser->getId(), $user->getParentID());

        $fullPath  = $path  . $file->hashName();

        $this->assertEquals($fullPath, $user->getMeansOfIdentification());

        Storage::disk($disk)->assertExists($fullPath);

    }

    private function profile_creation_data()
    {
        $userData = factory('App\User')->raw(['phone' => '08129531720']);
        $profileData = factory('App\Profile')->raw([
            'phone' => '08037312520',
            'next_of_kin_phone' => '08054473524',
            'next_of_kin_name' => 'Doe',
            'secondary_phone' => '08054473523'
        ]);

        $payload = array_merge($profileData, $userData);
        $payload['country_code'] = 'NG';

        return $payload;
    }

}

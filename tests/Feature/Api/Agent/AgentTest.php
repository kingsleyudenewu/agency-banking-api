<?php

namespace Tests\Feature\Api\Agent;

use App\Events\AgentAccountCreated;
use App\Events\AgentDocumentUploaded;
use App\Koloo\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class AgentTest
 *
 * @package \Tests\Feature\Api\Agent
 */
class AgentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadUsersWithPermission();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function can_create_agent_with_the_right_credentials_as_an_admin()
    {
        $this->can_create_agent_with_the_right_credentials_as($this->adminUser->getModel());
    }

    /** @test */
    public function can_create_agent_with_the_right_credentials_as_as_super_agent()
    {
        $this->can_create_agent_with_the_right_credentials_as($this->superAgentUser->getModel());
    }


    /** @test */
    public function must_not_create_agent_with_non_admin_or_super_agent_right()
    {
        Event::fake([AgentAccountCreated::class]);
        $agentUser = User::findOneByRole(\App\User::ROLE_AGENT);
        $this->assertNotNull($agentUser);

        $this->signIn($agentUser->getModel());

        $payload = $this->agent_create_data();

        $this->postJson(route('api.agents.create-new'), $payload)
            ->assertStatus(400)
            ->assertJson(['status' => 'error']);

        Event::assertNotDispatched(AgentAccountCreated::class);
    }

    protected function can_create_agent_with_the_right_credentials_as($user)
    {

        $this->signIn($user);

        Event::fake([AgentAccountCreated::class]);

        $payload = $this->agent_create_data();

        $res = $this->postJson(route('api.agents.create-new'), $payload)
            ->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "message" => "OK",
                "data" => ["first_name" => $payload['first_name']]
            ]);

        $content = $res->json();

        $createdUser = User::find($content['data']['id']);

        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->isAgent(), 'Expecting user to be an agent');
        $this->assertNotNull($createdUser->getParentID(), 'Parent not set');
        $this->assertTrue($createdUser->belongsTo(User::find($user->id)));

        Event::assertDispatched(AgentAccountCreated::class, 1);
    }

    protected function agent_create_data() : array
    {
        $userData = factory('App\User')->raw(['phone' => '08129531720']);
        $profileData = factory('App\Profile')->raw([
            'business_phone' => '08037312520',
            'next_of_kin_phone' => '08054473524'
        ]);

        $payload = array_merge($profileData, $userData);
        $payload['country_code'] = 'NG';

        return $payload;
    }



    /** @test */
    public function can_upload_valid_agent_document()
    {
        $this->withoutExceptionHandling();

        Event::fake([AgentDocumentUploaded::class]);

        $authUser  = $this->adminUser->getModel();

        $disk = $this->getSetting('document_storage_driver');

        $fullPath = $this->agent_document_upload($authUser, $disk);

        $this->assertEquals($fullPath, $this->agentUser->getDocumentPath('passport_photograph'));

        $this->assertEquals('', $this->agentUser->getDocumentPath('invalid document'));

        Storage::disk($disk)->assertExists($fullPath);

        Event::assertDispatched(AgentDocumentUploaded::class, 1);
    }


    /** @test */
    public function user_must_be_an_admin_or_a_super_agent_to_upload_document()
    {

        Event::fake([AgentDocumentUploaded::class]);

        $authUser  = $this->agentUser->getModel();
        $this->signIn($authUser);

        $this->json('post', route('api.agents.document.upload'),
            [
                'doc' => '',
                'id' => '',
                'document_type' => ''
            ]
        )->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.']);

        Event::assertNotDispatched(AgentDocumentUploaded::class, 1);
    }


    /**
     * Helper method
     *  Setup the upload process and return full path
     *
     * @param $authUser
     * @param $disk
     *
     * @return string
     */
    private function agent_document_upload($authUser, $disk)
    {


        factory('App\Profile')->create(['user_id' => $this->agentUser->getID()]);

        $this->withSettings()->signIn($authUser);

        $path = $this->getSetting('document_storage_path');
        $maxSize = $this->getSetting('document_storage_max_size'); // default to 2mb

        Storage::fake($disk);


        $file = UploadedFile::fake()->create('document.pdf', $maxSize, 'application/pdf');

        $this->json('post', route('api.agents.document.upload'),
            [
                'doc' => $file,
                'id' => $this->agentUser->getId(),
                'document_type' => 'passport_photograph'
            ]
        )->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $fullPath  = $path  . $file->hashName();
        return $fullPath;

    }

}


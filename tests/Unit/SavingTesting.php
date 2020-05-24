<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Saving;
use App\SavingCycle;

class SavingTesting extends TestCase
{
    use RefreshDatabase;


    /** @test */
   public function can_create_saving()
   {
        $saving = factory('App\Saving')->create();

        $this->assertNotNull($saving);
        $this->assertInstanceOf(Saving::class, $saving);

   }

    /** @test */
    public function a_saving_belongs_to_a_saving_cycle()
    {
        $saving = factory('App\Saving')->create();

        $this->assertNotNull($saving);
        $this->assertInstanceOf(SavingCycle::class, $saving->cycle);

    }

    /** @test */
    public function a_saving_must_have_a_creator()
    {
        $saving = factory('App\Saving')->create();

        $this->assertNotNull($saving);
        $this->assertInstanceOf(User::class, $saving->creator);

    }

    /** @test */
    public function a_saving_must_have_an_owner()
    {
        $saving = factory('App\Saving')->create();

        $this->assertNotNull($saving);
        $this->assertInstanceOf(User::class, $saving->owner);

    }

}

<?php

namespace Tests\Unit;

use App\SavingCycle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingCycleTest extends TestCase
{
    use RefreshDatabase;

   /** @test */
    public function can_create_saving_cycle()
    {
        $cycle = factory('App\SavingCycle')->create();

        $this->assertNotNull($cycle);
        $this->assertInstanceOf(SavingCycle::class, $cycle);
    }
}

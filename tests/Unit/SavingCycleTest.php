<?php

namespace Tests\Unit;

use App\SavingCycle;
use Illuminate\Database\Eloquent\Collection;
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

    /** @test */
    public function can_have_many_savings()
    {
        $cycle = factory('App\SavingCycle')->create();


        $this->assertInstanceOf(Collection::class, $cycle->savings);
    }
}

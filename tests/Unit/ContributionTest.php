<?php

namespace Tests\Unit;



use App\Saving;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContributionTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function belongs_to_a_saving()
    {
        $contrib = factory('App\Contribution')->create();

        $this->assertInstanceOf(Saving::class, $contrib->savingPlan);
    }
}

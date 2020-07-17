<?php

namespace Tests\Unit\Koloo;

use App\Koloo\SavingManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class SavingManagementTest
 *
 * @package \Tests\Unit\Koloo
 */
class SavingManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_matured_savings()
    {
        $savings = SavingManagement::getMaturedSavings();

        print_r($savings);
    }
}

<?php

namespace Tests;

use Database\Seeders\ReferenceSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    /**
     * Seed reference data (a single country and city, the industry list and the
     * currencies derived from them) for tests that rely on RefreshDatabase. Only
     * reference data is seeded so the database stays small and no demo user is
     * created that could clash with registration tests.
     */
    protected bool $seed = true;

    protected string $seeder = ReferenceSeeder::class;

    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }
}

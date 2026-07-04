<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReferenceSeeder extends Seeder
{
    /**
     * Seed the shared reference data (countries + cities, industries and the
     * currencies derived from them) without creating any demo users. Used as
     * the default test seeder so feature tests have dropdown data to work with.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            IndustrySeeder::class,
            CurrencySeeder::class,
        ]);
    }
}

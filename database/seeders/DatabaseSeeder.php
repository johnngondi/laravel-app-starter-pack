<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            CountrySeeder::class,
            IndustrySeeder::class,
            CurrencySeeder::class,
        ]);

        if (app()->environment('testing')) {
            $owner = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

            Organisation::createForOwner($owner, 'Wegon AI Demo');
        }
    }
}

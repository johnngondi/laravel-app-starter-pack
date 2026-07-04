<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Seed the countries and their cities from the bundled JSON dataset.
     *
     * During tests only the first country and its first city are seeded to
     * keep the test database small and fast.
     */
    public function run(): void
    {
        $path = storage_path('app/seeders/countries.json');

        if (! File::exists($path)) {
            return;
        }

        /** @var array<int, array<string, mixed>> $countries */
        $countries = json_decode(File::get($path), true) ?: [];

        $limited = app()->runningUnitTests() || app()->environment('testing');

        if ($limited) {
            $countries = array_slice($countries, 0, 1);
        }

        foreach ($countries as $data) {
            $country = Country::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'native' => ($data['native'] ?? '') ?: null,
                    'phone_code' => (string) ($data['phone'][0] ?? ''),
                    'continent' => $data['continent'] ?? null,
                    'capital' => $data['capital'] ?? null,
                    'currency' => $data['currency'][0] ?? null,
                ],
            );

            $cities = $data['cities'] ?? [];

            if ($limited) {
                $cities = array_slice($cities, 0, 1);
            }

            // Replace the country's cities so re-running the seeder stays idempotent.
            $country->cities()->delete();

            $now = now();

            $rows = array_map(fn (array $city): array => [
                'country_id' => $country->getKey(),
                'name' => $city['name'],
                'latitude' => $city['latitude'] ?? null,
                'longitude' => $city['longitude'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ], $cities);

            foreach (array_chunk($rows, 500) as $chunk) {
                City::insert($chunk);
            }
        }
    }
}

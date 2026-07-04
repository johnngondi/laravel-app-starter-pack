<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Friendly name + symbol for the currency codes we know about. Codes that
     * are present on a seeded country but missing here fall back to the code as
     * their name. Run after CountrySeeder so the country currencies exist.
     *
     * @var array<string, array{name: string, symbol: string|null}>
     */
    protected array $known = [
        'KES' => ['name' => 'Kenyan Shilling', 'symbol' => 'KSh'],
        'UGX' => ['name' => 'Ugandan Shilling', 'symbol' => 'USh'],
        'TZS' => ['name' => 'Tanzanian Shilling', 'symbol' => 'TSh'],
        'RWF' => ['name' => 'Rwandan Franc', 'symbol' => 'FRw'],
        'BIF' => ['name' => 'Burundian Franc', 'symbol' => 'FBu'],
        'SSP' => ['name' => 'South Sudanese Pound', 'symbol' => '£'],
        'SOS' => ['name' => 'Somali Shilling', 'symbol' => 'Sh'],
        'CDF' => ['name' => 'Congolese Franc', 'symbol' => 'FC'],
        'USD' => ['name' => 'US Dollar', 'symbol' => '$'],
        'EUR' => ['name' => 'Euro', 'symbol' => '€'],
        'GBP' => ['name' => 'Pound Sterling', 'symbol' => '£'],
    ];

    /**
     * Seed the currencies used by the countries we already seed.
     */
    public function run(): void
    {
        /** @var array<int, string> $codes */
        $codes = Country::query()
            ->whereNotNull('currency')
            ->distinct()
            ->pluck('currency')
            ->all();

        foreach ($codes as $code) {
            $details = $this->known[$code] ?? ['name' => $code, 'symbol' => null];

            Currency::updateOrCreate(
                ['code' => $code],
                ['name' => $details['name'], 'symbol' => $details['symbol']],
            );
        }
    }
}

<?php

namespace App\Concerns;

use App\Models\Country;

trait InteractsWithPhoneNumbers
{
    /**
     * Combine a dialing code and a national number into a single E.164-style
     * string, e.g. "254" + "0712 345 678" => "+254712345678". Returns null when
     * no number was supplied.
     */
    protected function normalisePhone(string $countryCode, ?string $number): ?string
    {
        $digits = ltrim(preg_replace('/\D+/', '', (string) $number) ?? '', '0');

        if ($digits === '') {
            return null;
        }

        return '+'.$countryCode.$digits;
    }

    /**
     * Split a stored phone back into its dialing code and national number,
     * matching the longest known country code prefix. Falls back to the given
     * default code when the number can't be matched.
     *
     * @return array{0: string, 1: string} [dialing code, national number]
     */
    protected function splitPhone(?string $phone, string $default = '254'): array
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';

        if ($digits === '') {
            return [$default, ''];
        }

        if (! str_starts_with((string) $phone, '+')) {
            return [$default, ltrim($digits, '0')];
        }

        $codes = Country::query()
            ->pluck('phone_code')
            ->map(fn ($code): string => (string) $code)
            ->sortByDesc(fn (string $code): int => strlen($code));

        foreach ($codes as $code) {
            if (str_starts_with($digits, $code)) {
                return [$code, substr($digits, strlen($code))];
            }
        }

        return [$default, $digits];
    }
}

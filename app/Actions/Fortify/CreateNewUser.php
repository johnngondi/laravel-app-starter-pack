<?php

namespace App\Actions\Fortify;

use App\Concerns\InteractsWithPhoneNumbers;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use InteractsWithPhoneNumbers, PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        $validated = Validator::make($input, [
            ...$this->profileRules(),
            'phone_country_code' => ['required', 'string', Rule::exists(Country::class, 'phone_code')],
            'phone' => $this->phoneRules(),
            'tax_pin' => $this->taxPinRules(),
            'photo' => ['nullable', 'image', 'max:2048'],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $this->normalisePhone($validated['phone_country_code'], $validated['phone']),
            'tax_pin' => $validated['tax_pin'] ?? null,
            'password' => $validated['password'],
        ]);

        if (($input['photo'] ?? null) instanceof UploadedFile) {
            $user->updateProfilePhoto($input['photo']);
        }

        return $user;
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyHas(Features::registration());
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'phone_country_code' => '254',
            'phone' => '0712 345 678',
            'tax_pin' => 'A001234567Z',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();

        $user = User::where('email', 'test@example.com')->first();

        $this->assertSame('+254712345678', $user->phone);
        $this->assertSame('A001234567Z', $user->tax_pin);
    }

    public function test_new_users_can_register_with_a_profile_photo(): void
    {
        Storage::fake('public');

        $response = $this->post(route('register.store'), [
            'name' => 'Jane Doe',
            'email' => 'photo@example.com',
            'phone_country_code' => '254',
            'phone' => '0712 345 678',
            'password' => 'password',
            'password_confirmation' => 'password',
            'photo' => UploadedFile::fake()->image('dp.png'),
        ]);

        $response->assertSessionHasNoErrors();

        $user = User::where('email', 'photo@example.com')->first();

        $this->assertNotNull($user->profile_photo_path);
        Storage::disk('public')->assertExists($user->profile_photo_path);
    }

    public function test_registration_requires_phone(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'phone_country_code' => '254',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertGuest();
    }

    public function test_test_database_is_seeded_with_a_single_country_and_city(): void
    {
        $this->assertSame(1, Country::count());
        $this->assertSame(1, City::count());
    }
}

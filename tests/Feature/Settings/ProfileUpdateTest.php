<?php

namespace Tests\Feature\Settings;

use App\Livewire\Settings\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->get('/settings/profile')->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = Livewire::test(Profile::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->call('updateProfileInformation');

        $response->assertHasNoErrors();

        $user->refresh();

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_phone_and_tax_pin_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('name', $user->name)
            ->set('email', $user->email)
            ->set('phoneCountryCode', '254')
            ->set('phone', '0712 345 678')
            ->set('tax_pin', 'A001234567Z')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertSame('+254712345678', $user->phone);
        $this->assertSame('A001234567Z', $user->tax_pin);
    }

    public function test_existing_phone_is_split_into_code_and_number_on_mount(): void
    {
        $user = User::factory()->create(['phone' => '+254712345678']);

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->assertSet('phoneCountryCode', '254')
            ->assertSet('phone', '712345678');
    }

    public function test_profile_photo_can_be_uploaded_and_removed(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('name', $user->name)
            ->set('email', $user->email)
            ->set('photo', UploadedFile::fake()->image('avatar.jpg'))
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $path = $user->refresh()->profile_photo_path;

        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);

        Livewire::test(Profile::class)->call('removePhoto');

        $this->assertNull($user->refresh()->profile_photo_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_profile_photo_must_be_an_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('name', $user->name)
            ->set('email', $user->email)
            ->set('photo', UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'))
            ->call('updateProfileInformation')
            ->assertHasErrors(['photo']);

        $this->assertNull($user->refresh()->profile_photo_path);
    }

    public function test_email_verification_status_is_unchanged_when_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = Livewire::test(Profile::class)
            ->set('name', 'Test User')
            ->set('email', $user->email)
            ->call('updateProfileInformation');

        $response->assertHasNoErrors();

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = Livewire::test('settings.delete-user-form')
            ->set('password', 'password')
            ->call('deleteUser');

        $response
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertNull($user->fresh());
        $this->assertFalse(auth()->check());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = Livewire::test('settings.delete-user-form')
            ->set('password', 'wrong-password')
            ->call('deleteUser');

        $response->assertHasErrors(['password']);

        $this->assertNotNull($user->fresh());
    }
}

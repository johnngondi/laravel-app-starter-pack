<?php

use App\Livewire\Organisations\CreateOrganisation;
use App\Livewire\Organisations\OrganisationSwitcher;
use App\Livewire\Organisations\Settings;
use App\Livewire\Organisations\StaffManager;
use App\Models\City;
use App\Models\Country;
use App\Models\Industry;
use App\Models\Organisation;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
});

it('redirects a user without an organisation to the create page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('organisations.create'));
});

it('creates an organisation through the onboarding component', function () {
    $user = User::factory()->create();
    $industry = Industry::query()->first();
    $country = Country::query()->first();

    $component = Livewire::actingAs($user)
        ->test(CreateOrganisation::class)
        ->set('name', 'AlienSoft Collections')
        ->set('industryId', $industry->id)
        ->set('countryId', $country->id)
        ->call('create');

    $organisation = Organisation::where('name', 'AlienSoft Collections')->first();

    expect($organisation)->not->toBeNull()
        ->and($organisation->slug)->toBe('aliensoft-collections')
        ->and($organisation->industry_id)->toBe($industry->id)
        ->and($organisation->country_id)->toBe($country->id)
        ->and($user->fresh()->current_organisation_id)->toBe($organisation->id);

    $component->assertRedirect(route('organisation.dashboard', $organisation));
});

it('serves the organisation dashboard under its slug', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    $this->actingAs($owner)
        ->get(route('organisation.dashboard', $organisation))
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('Overview');

    expect(route('organisation.dashboard', $organisation))->toContain('/acme-collections/dashboard');
});

it('redirects the bare organisation settings url to the general page', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    $this->actingAs($owner)
        ->get(route('organisation.settings', $organisation))
        ->assertRedirect(route('organisation.settings.general', $organisation));
});

it('renders the organisation general settings page for the owner', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    $this->actingAs($owner)
        ->get(route('organisation.settings.general', $organisation))
        ->assertOk()
        ->assertSee('General')
        ->assertSee('Organisation name')
        ->assertSee('Delete organisation');
});

it('renders the organisation staff settings page with staff management for the owner', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    $this->actingAs($owner)
        ->get(route('organisation.settings.staff', $organisation))
        ->assertOk()
        ->assertSee('Staff')
        ->assertSee($owner->name);
});

it('forbids accessing an organisation the user does not belong to', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    $outsider = User::factory()->create();
    Organisation::createForOwner($outsider, 'Other Org');

    $this->actingAs($outsider)
        ->get(route('organisation.dashboard', $organisation))
        ->assertForbidden();
});

it('switches the active organisation', function () {
    $user = User::factory()->create();
    $first = Organisation::createForOwner($user, 'First Org');
    $second = Organisation::createForOwner($user, 'Second Org');

    expect($user->fresh()->current_organisation_id)->toBe($second->id);

    Livewire::actingAs($user)
        ->test(OrganisationSwitcher::class)
        ->call('switch', $first->id)
        ->assertRedirect(route('organisation.dashboard', $first));

    expect($user->fresh()->current_organisation_id)->toBe($first->id);
});

it('lets an owner add a member of staff with a role', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    Livewire::actingAs($owner)
        ->test(StaffManager::class, ['organisation' => $organisation])
        ->set('name', 'Jane Agent')
        ->set('email', 'jane@example.com')
        ->set('role', 'Agent')
        ->call('addStaff')
        ->assertHasNoErrors();

    $jane = User::where('email', 'jane@example.com')->first();

    expect($jane)->not->toBeNull()
        ->and($organisation->fresh()->hasMember($jane))->toBeTrue();

    setPermissionsTeamId($organisation->id);
    expect($jane->hasRole('Agent'))->toBeTrue();
});

it('prevents adding the same member twice', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');
    $existing = User::factory()->create(['email' => 'jane@example.com']);
    $organisation->members()->attach($existing);

    Livewire::actingAs($owner)
        ->test(StaffManager::class, ['organisation' => $organisation])
        ->set('name', 'Jane Agent')
        ->set('email', 'jane@example.com')
        ->set('role', 'Agent')
        ->call('addStaff')
        ->assertHasErrors('email');
});

it('does not let a viewer manage members', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    $viewer = User::factory()->create();
    $organisation->members()->attach($viewer);
    setPermissionsTeamId($organisation->id);
    $viewer->assignRole('Viewer');
    $viewer->forceFill(['current_organisation_id' => $organisation->id])->save();

    Livewire::actingAs($viewer)
        ->test(StaffManager::class, ['organisation' => $organisation])
        ->set('name', 'Mark')
        ->set('email', 'mark@example.com')
        ->set('role', 'Agent')
        ->call('addStaff')
        ->assertForbidden();
});

it('walks through the create-organisation wizard steps before creating', function () {
    $user = User::factory()->create();
    $industry = Industry::query()->first();
    $country = Country::query()->first();

    Livewire::actingAs($user)
        ->test(CreateOrganisation::class)
        ->assertSet('step', 1)
        ->call('nextStep')                  // required fields missing — stays on step 1
        ->assertHasErrors(['name', 'industryId', 'countryId'])
        ->assertSet('step', 1)
        ->set('name', 'AlienSoft Collections')
        ->set('industryId', $industry->id)
        ->set('countryId', $country->id)
        ->call('nextStep')                  // advances to the contact step
        ->assertHasNoErrors()
        ->assertSet('step', 2)
        ->call('nextStep')                  // advances to the review step
        ->assertSet('step', 3)
        ->call('previousStep')
        ->assertSet('step', 2)
        ->call('nextStep')
        ->call('create')
        ->assertRedirect(route('organisation.dashboard', Organisation::firstWhere('name', 'AlienSoft Collections')));

    expect(Organisation::where('name', 'AlienSoft Collections')->exists())->toBeTrue();
});

it('defaults the currency and dial code from the chosen country', function () {
    $user = User::factory()->create();
    $country = Country::query()->first();

    Livewire::actingAs($user)
        ->test(CreateOrganisation::class)
        ->set('countryId', $country->id)
        ->assertSet('currencyCode', $country->currency)
        ->assertSet('phoneCountryCode', (string) $country->phone_code);
});

it('persists the full organisation profile from the wizard', function () {
    $user = User::factory()->create();
    $industry = Industry::query()->first();
    $country = Country::query()->first();
    $city = City::query()->first();

    Livewire::actingAs($user)
        ->test(CreateOrganisation::class)
        ->set('name', 'Acme Collections')
        ->set('industryId', $industry->id)
        ->set('countryId', $country->id)
        ->set('cityId', $city->id)
        ->set('currencyCode', $country->currency)
        ->set('taxPin', 'P12345678X')
        ->set('phoneCountryCode', '254')
        ->set('phone', '712345678')
        ->set('email', 'info@acme.test')
        ->set('address', '123 Moi Avenue')
        ->call('create')
        ->assertHasNoErrors();

    $organisation = Organisation::firstWhere('name', 'Acme Collections');

    expect($organisation->city_id)->toBe($city->id)
        ->and($organisation->currency_code)->toBe($country->currency)
        ->and($organisation->tax_pin)->toBe('P12345678X')
        ->and($organisation->email)->toBe('info@acme.test')
        ->and($organisation->address)->toBe('123 Moi Avenue')
        ->and($organisation->phone)->toBe('+254712345678');
});

it('lets an owner delete an organisation and purges all of its data', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    $member = User::factory()->create();
    $organisation->members()->attach($member);

    expect(Role::where('organisation_id', $organisation->id)->exists())->toBeTrue();

    Livewire::actingAs($owner)
        ->test(Settings::class, ['organisation' => $organisation])
        ->set('password', 'password')
        ->call('deleteOrganisation')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect(Organisation::find($organisation->id))->toBeNull()
        ->and(DB::table('organisation_user')->where('organisation_id', $organisation->id)->exists())->toBeFalse()
        ->and(Role::where('organisation_id', $organisation->id)->exists())->toBeFalse()
        ->and(DB::table('model_has_roles')->where('organisation_id', $organisation->id)->exists())->toBeFalse()
        ->and($owner->fresh()->current_organisation_id)->toBeNull();
});

it('populates the settings form from the existing organisation', function () {
    $owner = User::factory()->create();
    $industry = Industry::query()->first();
    $country = Country::query()->first();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections', [
        'industry_id' => $industry->id,
        'country_id' => $country->id,
        'currency_code' => $country->currency,
        'phone' => '+254700111222',
        'email' => 'hello@acme.test',
    ]);

    Livewire::actingAs($owner)
        ->test(Settings::class, ['organisation' => $organisation])
        ->assertSet('industryId', $industry->id)
        ->assertSet('countryId', $country->id)
        ->assertSet('currencyCode', $country->currency)
        ->assertSet('email', 'hello@acme.test')
        ->assertSet('phoneCountryCode', '254')
        ->assertSet('phone', '700111222');
});

it('updates the organisation profile from settings', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');
    $industry = Industry::query()->first();
    $country = Country::query()->first();
    $city = City::query()->first();

    Livewire::actingAs($owner)
        ->test(Settings::class, ['organisation' => $organisation])
        ->set('name', 'Acme Recovery')
        ->set('industryId', $industry->id)
        ->set('countryId', $country->id)
        ->set('cityId', $city->id)
        ->set('currencyCode', $country->currency)
        ->set('taxPin', 'A001122B')
        ->set('phoneCountryCode', '254')
        ->set('phone', '700111222')
        ->set('email', 'hello@acme.test')
        ->set('address', '1 Kenyatta Ave')
        ->call('updateOrganisation')
        ->assertHasNoErrors();

    $organisation->refresh();

    expect($organisation->name)->toBe('Acme Recovery')
        ->and($organisation->industry_id)->toBe($industry->id)
        ->and($organisation->country_id)->toBe($country->id)
        ->and($organisation->city_id)->toBe($city->id)
        ->and($organisation->currency_code)->toBe($country->currency)
        ->and($organisation->tax_pin)->toBe('A001122B')
        ->and($organisation->email)->toBe('hello@acme.test')
        ->and($organisation->address)->toBe('1 Kenyatta Ave')
        ->and($organisation->phone)->toBe('+254700111222');
});

it('uploads an organisation logo through the wizard', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $industry = Industry::query()->first();
    $country = Country::query()->first();

    Livewire::actingAs($user)
        ->test(CreateOrganisation::class)
        ->set('name', 'Logo Org')
        ->set('industryId', $industry->id)
        ->set('countryId', $country->id)
        ->set('logo', UploadedFile::fake()->image('logo.png'))
        ->call('create')
        ->assertHasNoErrors();

    $organisation = Organisation::firstWhere('name', 'Logo Org');

    expect($organisation->profile_photo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($organisation->profile_photo_path);
});

it('updates and removes the organisation logo from settings', function () {
    Storage::fake('public');

    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections', [
        'industry_id' => Industry::query()->first()->id,
        'country_id' => Country::query()->first()->id,
    ]);

    Livewire::actingAs($owner)
        ->test(Settings::class, ['organisation' => $organisation])
        ->set('logo', UploadedFile::fake()->image('logo.png'))
        ->call('updateOrganisation')
        ->assertHasNoErrors();

    $path = $organisation->refresh()->profile_photo_path;

    expect($path)->not->toBeNull();
    Storage::disk('public')->assertExists($path);

    Livewire::actingAs($owner)
        ->test(Settings::class, ['organisation' => $organisation])
        ->call('removeLogo');

    expect($organisation->refresh()->profile_photo_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('requires industry and country when updating from settings', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    Livewire::actingAs($owner)
        ->test(Settings::class, ['organisation' => $organisation])
        ->set('industryId', null)
        ->set('countryId', null)
        ->call('updateOrganisation')
        ->assertHasErrors(['industryId', 'countryId']);
});

it('requires the correct password to delete an organisation', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    Livewire::actingAs($owner)
        ->test(Settings::class, ['organisation' => $organisation])
        ->set('password', 'wrong-password')
        ->call('deleteOrganisation')
        ->assertHasErrors('password');

    expect(Organisation::find($organisation->id))->not->toBeNull();
});

it('does not let a non-owner delete an organisation', function () {
    $owner = User::factory()->create();
    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    $admin = User::factory()->create();
    $organisation->members()->attach($admin);
    setPermissionsTeamId($organisation->id);
    $admin->assignRole('Admin');
    $admin->forceFill(['current_organisation_id' => $organisation->id])->save();

    Livewire::actingAs($admin)
        ->test(Settings::class, ['organisation' => $organisation])
        ->set('password', 'password')
        ->call('deleteOrganisation')
        ->assertForbidden();

    expect(Organisation::find($organisation->id))->not->toBeNull();
});

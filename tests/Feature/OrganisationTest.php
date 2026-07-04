<?php

use App\Models\Organisation;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
});

it('creates an organisation owned by a user and adds them as a member', function () {
    $owner = User::factory()->create();

    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    expect($organisation->owner_id)->toBe($owner->id)
        ->and($organisation->slug)->toBe('acme-collections')
        ->and($organisation->hasMember($owner))->toBeTrue()
        ->and($owner->fresh()->current_organisation_id)->toBe($organisation->id);
});

it('provisions the default roles for a new organisation', function () {
    $owner = User::factory()->create();

    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    setPermissionsTeamId($organisation->id);

    $roles = Role::all()->pluck('name');

    expect($roles)->toContain('Owner', 'Admin', 'Manager', 'Agent', 'Viewer');
});

it('assigns the owner the Owner role with full permissions', function () {
    $owner = User::factory()->create();

    $organisation = Organisation::createForOwner($owner, 'Acme Collections');

    setPermissionsTeamId($organisation->id);

    expect($owner->hasRole('Owner'))->toBeTrue()
        ->and($owner->can('delete organisation'))->toBeTrue()
        ->and($owner->can('manage members'))->toBeTrue();
});

it('scopes roles to the active organisation', function () {
    $owner = User::factory()->create();

    $first = Organisation::createForOwner($owner, 'First Org');
    $second = Organisation::createForOwner($owner, 'Second Org');

    // Owner role only granted in the first organisation.
    setPermissionsTeamId($first->id);
    expect($owner->hasRole('Owner'))->toBeTrue();

    setPermissionsTeamId($second->id);
    expect($owner->hasRole('Owner'))->toBeTrue();

    // A member added only to the second org has no role in the first.
    $member = User::factory()->create();
    $second->members()->attach($member);

    setPermissionsTeamId($second->id);
    $member->assignRole('Agent');
    expect($member->hasRole('Agent'))->toBeTrue();

    setPermissionsTeamId($first->id);
    expect($member->fresh()->hasRole('Agent'))->toBeFalse();
});

it('only lets a user switch to organisations they belong to', function () {
    $user = User::factory()->create();
    $organisation = Organisation::createForOwner($user, 'Acme Collections');

    $foreign = Organisation::createForOwner(User::factory()->create(), 'Other Org');

    expect($user->switchOrganisation($organisation))->toBeTrue()
        ->and($user->switchOrganisation($foreign))->toBeFalse()
        ->and($user->fresh()->current_organisation_id)->toBe($organisation->id);
});

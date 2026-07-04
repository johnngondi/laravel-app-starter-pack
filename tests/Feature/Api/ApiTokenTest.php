<?php

use App\Models\Organisation;
use App\Models\User;
use Database\Seeders\PermissionSeeder;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
});

it('issues an API token for valid credentials', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/v1/tokens', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'Test Device',
    ]);

    $response->assertCreated()->assertJsonStructure(['token']);

    expect($user->tokens()->count())->toBe(1);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $this->postJson('/api/v1/tokens', [
        'email' => $user->email,
        'password' => 'wrong-password',
        'device_name' => 'Test Device',
    ])->assertStatus(422);
});

it('allows an authenticated token to reach the user endpoint', function () {
    $user = User::factory()->create();
    $token = $user->createToken('Test Device')->plainTextToken;

    $this->withToken($token)
        ->getJson('/api/v1/user')
        ->assertOk()
        ->assertJsonPath('email', $user->email);
});

it('blocks the user endpoint without a token', function () {
    $this->getJson('/api/v1/user')->assertUnauthorized();
});

it('lists the organisations the authenticated user belongs to', function () {
    $user = User::factory()->create();
    $organisation = Organisation::createForOwner($user, 'Acme Collections');
    Organisation::createForOwner(User::factory()->create(), 'Other Org');

    $token = $user->createToken('Test Device')->plainTextToken;

    $this->withToken($token)
        ->getJson('/api/v1/organisations')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Acme Collections')
        ->assertJsonPath('data.0.is_owner', true);
});

it('revokes the current token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('Test Device')->plainTextToken;

    $this->withToken($token)->deleteJson('/api/v1/tokens')->assertNoContent();

    expect($user->fresh()->tokens()->count())->toBe(0);
});

<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_without_an_organisation_are_sent_to_onboarding(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('organisations.create'));
    }

    public function test_authenticated_users_can_visit_their_organisation_dashboard(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $organisation = Organisation::createForOwner($user, 'Acme Collections');
        $this->actingAs($user);

        // The param-free dashboard route bounces to the current organisation.
        $this->get(route('dashboard'))
            ->assertRedirect(route('organisation.dashboard', $organisation));

        $this->get(route('organisation.dashboard', $organisation))->assertOk();
    }
}

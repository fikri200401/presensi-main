<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DashboardEncodingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_employee_dashboard_does_not_render_mojibake_characters(): void
    {
        Role::create(['name' => 'employee']);
        $user = User::factory()->create(['name' => 'Budi Santoso']);
        $user->assignRole('employee');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Halo, Budi Santoso')
            ->assertDontSee('ðŸ', false)
            ->assertDontSee('âœ', false);
    }

    public function test_admin_dashboard_chart_tooltip_does_not_render_mojibake_characters(): void
    {
        Role::create(['name' => 'super_admin']);
        $user = User::factory()->create(['name' => 'Super Admin']);
        $user->assignRole('super_admin');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertDontSee('â”', false);
    }
}

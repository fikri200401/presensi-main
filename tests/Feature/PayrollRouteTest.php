<?php

namespace Tests\Feature;

use App\Http\Controllers\PayrollController;
use App\Models\Payroll;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tests\TestCase;

class PayrollRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_employee_payroll_links_use_explicit_detail_and_print_paths(): void
    {
        Role::create(['name' => 'employee']);
        $user = User::factory()->create();
        $user->assignRole('employee');
        $payroll = $this->approvedPayrollFor($user);

        $this->assertSame("/payroll/detail/{$payroll->id}", parse_url(route('payroll.show', $payroll), PHP_URL_PATH));
        $this->assertSame("/payroll/print/{$payroll->id}", parse_url(route('payroll.exportPdf', $payroll), PHP_URL_PATH));

        $this->actingAs($user)
            ->get(route('payroll.show', $payroll))
            ->assertOk()
            ->assertSee($payroll->periode_name);

        $this->actingAs($user)
            ->get(route('payroll.exportPdf', $payroll))
            ->assertOk()
            ->assertSee('SLIP GAJI');
    }

    public function test_employee_can_view_approved_payroll_when_database_returns_user_id_as_string(): void
    {
        Role::create(['name' => 'employee']);
        $user = User::factory()->create();
        $user->assignRole('employee');
        $payroll = $this->approvedPayrollFor($user);

        $attributes = $payroll->getAttributes();
        $attributes['user_id'] = (string) $user->id;
        $payroll->setRawAttributes($attributes, true);

        $this->actingAs($user);

        $authorize = Closure::bind(
            fn (Payroll $payroll) => $this->authorizePayrollVisibility($payroll),
            app(PayrollController::class),
            PayrollController::class,
        );

        try {
            $authorize($payroll);
            $this->assertTrue(true);
        } catch (HttpExceptionInterface $exception) {
            $this->fail("Expected employee payroll visibility to be allowed, got {$exception->getStatusCode()}.");
        }
    }

    private function approvedPayrollFor(User $user): Payroll
    {
        return Payroll::query()->create([
            'user_id' => $user->id,
            'periode' => '2026-05',
            'bulan' => 5,
            'tahun' => 2026,
            'total_hari_kerja' => 21,
            'total_hari_hadir' => 16,
            'total_jam_kerja' => 168,
            'total_jam_hadir' => 128,
            'gaji_pokok' => 6814912,
            'gaji_kotor' => 6814912,
            'gaji_bersih' => 6642722,
            'status' => 'approved',
        ]);
    }
}

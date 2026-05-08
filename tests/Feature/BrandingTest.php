<?php

namespace Tests\Feature;

use App\Models\LandingPageSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_login_page_uses_configured_logo_and_branding(): void
    {
        $this->createLandingPageSetting();

        $this->get('/login')
            ->assertOk()
            ->assertSee('HRIS BPRS AHA')
            ->assertSee('/storage/landing/company-logo.png', false);
    }

    public function test_sidebar_uses_configured_logo_and_branding(): void
    {
        $this->createLandingPageSetting();

        Role::create(['name' => 'super_admin']);
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->actingAs($user);

        $html = view('layouts.partials.sidebar')->render();

        $this->assertStringContainsString('HRIS BPRS AHA', $html);
        $this->assertStringContainsString('Management System', $html);
        $this->assertStringContainsString('/storage/landing/company-logo.png', $html);
    }

    private function createLandingPageSetting(): LandingPageSetting
    {
        return LandingPageSetting::query()->create(array_merge(
            LandingPageSetting::defaultValues(),
            [
                'brand_name' => 'HRIS BPRS AHA',
                'brand_subtitle' => 'Management System',
                'logo_image' => 'landing/company-logo.png',
            ],
        ));
    }
}

<?php

namespace Tests\Feature;

use App\Models\LandingPageSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class LandingPageSettingUploadValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_hero_image_upload_error_has_clear_message(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'update_setting']);
        $user->givePermissionTo('update_setting');

        $payload = array_merge(
            LandingPageSetting::defaultValues(),
            [
                'hero_image' => $this->failedUpload('hero-too-large.jpg'),
            ],
        );

        $this->actingAs($user)
            ->from('/settings')
            ->put(route('settings.update'), $payload)
            ->assertRedirect('/settings')
            ->assertSessionHasErrors([
                'hero_image' => 'Foto hero gagal diunggah. Ukuran file kemungkinan melebihi batas upload server atau maksimal 4MB.',
            ]);
    }

    private function failedUpload(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'upload-test-');
        file_put_contents($path, 'not uploaded');

        return new UploadedFile(
            $path,
            $name,
            'image/jpeg',
            UPLOAD_ERR_INI_SIZE,
            true,
        );
    }
}

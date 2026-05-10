<?php

namespace Tests\Feature;

use App\Http\Controllers\NotificationController;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tests\TestCase;

class NotificationReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_mark_own_notification_read_when_database_returns_user_id_as_string(): void
    {
        $user = User::factory()->create();
        $notification = Notification::query()->create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Slip Gaji Tersedia',
            'message' => 'Slip gaji Anda sudah bisa dilihat.',
            'url' => '/payroll',
        ]);

        $attributes = $notification->getAttributes();
        $attributes['user_id'] = (string) $user->id;
        $notification->setRawAttributes($attributes, true);

        $this->actingAs($user);

        try {
            $response = app(NotificationController::class)->markRead($notification);
        } catch (HttpExceptionInterface $exception) {
            $this->fail("Expected notification to be marked read, got {$exception->getStatusCode()}.");
        }

        $this->assertTrue($response->getData()->success);
        $this->assertNotNull($notification->fresh()->read_at);
    }
}

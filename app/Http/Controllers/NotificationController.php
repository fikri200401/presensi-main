<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** GET /notifications — ambil 15 notif terbaru milik user login */
    public function index(): JsonResponse
    {
        $notifications = Notification::forUser(auth()->id())
            ->latest()
            ->take(15)
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'icon'       => $n->icon,
                'color'      => $n->color,
                'title'      => $n->title,
                'message'    => $n->message,
                'url'        => $n->url,
                'is_read'    => $n->isRead(),
                'time'       => $n->created_at->diffForHumans(),
            ]);

        $unreadCount = Notification::forUser(auth()->id())->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    /** POST /notifications/{id}/read — tandai satu notif sebagai dibaca */
    public function markRead(Notification $notification): JsonResponse
    {
        $userId = auth()->id();

        abort_if($userId === null || (int) $notification->user_id !== (int) $userId, 403);

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /** POST /notifications/read-all — tandai semua notif sebagai dibaca */
    public function markAllRead(): JsonResponse
    {
        Notification::forUser(auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Technician app notification inbox (app-only, Sanctum-authenticated).
 */
class NotificationController extends Controller
{
    /**
     * Paginated list of the authenticated user's notifications, newest first.
     * Optional ?filter=unread to return only unread ones.
     *
     * GET /api/notifications
     */
    public function index(Request $request): JsonResponse
    {
        $query = AppNotification::where('user_id', $request->user()->id)->latest();

        if ($request->string('filter')->toString() === 'unread') {
            $query->whereNull('read_at');
        }

        $page = $query->paginate((int) $request->integer('per_page', 20));

        return response()->json([
            'unread_count' => AppNotification::where('user_id', $request->user()->id)->whereNull('read_at')->count(),
            'notifications' => $page->getCollection()->map(fn ($n) => $this->format($n))->values(),
            'meta' => [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ]);
    }

    /**
     * Unread count only — for the app's badge.
     *
     * GET /api/notifications/unread-count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'unread_count' => AppNotification::where('user_id', $request->user()->id)->whereNull('read_at')->count(),
        ]);
    }

    /**
     * Mark one notification as read.
     *
     * POST /api/notifications/{notification}/read
     */
    public function markRead(Request $request, AppNotification $notification): JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['message' => 'Marked as read.', 'notification' => $this->format($notification->fresh())]);
    }

    /**
     * Mark all of the user's notifications as read.
     *
     * POST /api/notifications/read-all
     */
    public function markAllRead(Request $request): JsonResponse
    {
        AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read.', 'unread_count' => 0]);
    }

    /**
     * Delete a notification.
     *
     * DELETE /api/notifications/{notification}
     */
    public function destroy(Request $request, AppNotification $notification): JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }

    private function format(AppNotification $n): array
    {
        return [
            'id' => $n->id,
            'type' => $n->type,
            'title' => $n->title,
            'body' => $n->body,
            'data' => $n->data,
            'is_read' => $n->read_at !== null,
            'read_at' => optional($n->read_at)->toIso8601String(),
            'created_at' => optional($n->created_at)->toIso8601String(),
            'time_ago' => optional($n->created_at)->diffForHumans(),
        ];
    }
}

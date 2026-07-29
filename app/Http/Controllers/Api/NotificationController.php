<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 20));

        // Attach inspection date/time to notifications that reference an inspection.
        // Load all referenced inspections in a single query to avoid N+1.
        $collection = $notifications->getCollection();
        $inspectionIds = $collection->pluck('data.inspection_id')->filter()->unique()->toArray();
        $inspections = \App\Models\Inspection::whereIn('id', $inspectionIds)->get()->keyBy('id');

        $collection->transform(function ($notification) use ($inspections) {
            $data = $notification->data ?? [];
            $inspectionId = $data['inspection_id'] ?? null;
            if ($inspectionId && ($inspection = $inspections->get((int) $inspectionId))) {
                $data['inspection_scheduled_at'] = $inspection->scheduled_at?->format('Y-m-d H:i:s');
                $data['inspection_date'] = $inspection->scheduled_at?->format('Y-m-d');
                $data['inspection_time'] = $inspection->scheduled_at?->format('H:i');
                $notification->data = $data;
            }
            return $notification;
        });

        return response()->json($notifications);
    }

    public function markRead(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Marked as read.']);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All marked as read.']);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}

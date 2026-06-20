<?php

namespace App\Http\Controllers;

use App\Models\NotificationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = NotificationItem::where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'is_read' => $n->is_read,
                    'time' => $n->created_at->diffForHumans(),
                ];
            });

        $unreadCount = NotificationItem::where('user_id', Auth::id())->where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = NotificationItem::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        NotificationItem::where('user_id', Auth::id())->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}

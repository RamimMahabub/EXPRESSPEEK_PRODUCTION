<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function all(Request $request)
    {
        return view('notifications.index', [
            'notifications' => $request->user()->notifications()->latest()->paginate(25),
        ]);
    }

    public function index(Request $request)
    {
        return response()->json([
            'notifications' => $request->user()->notifications()->latest()->limit(25)->get(),
            'unread_count' => $request->user()->notifications()->whereNull('read_at')->count(),
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }
    
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}

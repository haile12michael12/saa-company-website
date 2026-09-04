<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Communication\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index(Request $request)
    {
        $notifications = $this->notificationService->getAllNotifications(auth()->user());

        if ($request->wantsJson()) {
            return response()->json($notifications);
        }

        if (view()->exists('admin.notifications')) {
            return view('admin.notifications', compact('notifications'));
        }

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, string $id)
    {
        $marked = $this->notificationService->markAsRead(auth()->user(), $id);

        if ($request->wantsJson()) {
            return response()->json(['marked' => $marked]);
        }

        return redirect()->back();
    }

    public function markAllAsRead(Request $request)
    {
        $this->notificationService->markAllAsRead(auth()->user());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'All notifications marked as read.']);
        }

        toastr()->success('All notifications marked as read.');
        return redirect()->back();
    }
}

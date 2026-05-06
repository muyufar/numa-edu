<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $notifications = $user->notifications()->paginate(25);
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(DatabaseNotification $notification): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user, 403);

        abort_unless(
            $notification->notifiable_type === get_class($user) && (string) $notification->notifiable_id === (string) $user->id,
            403
        );

        $notification->markAsRead();

        return redirect()->back();
    }

    public function readAll(): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $user->unreadNotifications()->update(['read_at' => now()]);

        return redirect()->back();
    }
}

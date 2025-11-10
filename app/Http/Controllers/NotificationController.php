<?php

namespace App\Http\Controllers;

use App\Support\NotificationPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function redirect(Request $request, string $notificationId, NotificationPresenter $presenter): RedirectResponse
    {
        /** @var DatabaseNotification $notification */
        $notification = $request->user()
            ->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        $url = $presenter->url($notification, $request->user());

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect()->to($url);
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}

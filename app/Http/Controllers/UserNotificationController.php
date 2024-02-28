<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Response;

class UserNotificationController extends Controller
{
    public function markAsRead(Request $request, Notification $notification)
    {
        $notification->setReadAt();
    }
    public function markAllAsRead(Request $request, User $user)
    {
        $user->unreadNotifications->markAsRead();

        return $user->notifications;
    }
}

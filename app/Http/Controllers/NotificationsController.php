<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\NotificationsRepository;

use App\Models\Notification;

class NotificationsController extends Controller
{
    public function showNotifications(Request $request) {

        if (!$request->user()){
            return response()->json(['error' => 'user id not found']);
        }
        $user = $request->user();

        return response()->json([$user->notifications]);
    }

    public function markRead(Request $request, NotificationsRepository $notificationsRepository) {

        $notification = Notification::where('id', $request->input('notification_id'))->first();

        if ($notification) {
            $notificationsRepository->markRead($notification);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Missing notification ID']);
        }
    }
}

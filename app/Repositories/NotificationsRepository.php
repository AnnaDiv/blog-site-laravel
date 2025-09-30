<?php

namespace App\Repositories;

use App\Models\Notification;

class NotificationsRepository {

    public function markRead(Notification $notificiation) {
        $notificiation->update(['used' => 1]);
    }
}

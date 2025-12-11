<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function get()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated'])->setStatusCode(401);
        }

        $userId = $session->get('userId');
        $count = $this->notificationModel->getUnreadCount($userId);
        // Only get unread notifications for the dropdown
        $notifications = $this->notificationModel->getNotificationsForUser($userId, true);

        // Update session count to keep it in sync
        $session->set('unreadNotifications', (int) $count);

        return $this->response->setJSON([
            'success' => true,
            'unread_count' => (int) $count,
            'notifications' => $notifications,
        ]);
    }

    public function mark_as_read($id = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated'])->setStatusCode(401);
        }

        if (!is_numeric($id) || $id <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid notification id'])->setStatusCode(400);
        }

        $notification = $this->notificationModel->find($id);
        if (!$notification) {
            return $this->response->setJSON(['success' => false, 'message' => 'Notification not found'])->setStatusCode(404);
        }

        // Ensure the user owns this notification
        if ($notification['user_id'] != $session->get('userId')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Forbidden'])->setStatusCode(403);
        }

        $this->notificationModel->markAsRead($id);

        // Update session count
        $newCount = $this->notificationModel->getUnreadCount($session->get('userId'));
        $session->set('unreadNotifications', (int) $newCount);

        return $this->response->setJSON(['success' => true, 'message' => 'Marked as read', 'unread_count' => (int) $newCount]);
    }
}

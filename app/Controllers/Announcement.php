<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;

class Announcement extends BaseController
{
    public function index()
    {
        $announcements = [];

        // Check DB connectivity and table existence before querying
        $dbAvailable = true;
        try {
            $db = \Config\Database::connect();
            $db->connect();
        } catch (\Throwable $e) {
            $dbAvailable = false;
            log_message('error', 'Database unavailable for announcements: ' . $e->getMessage());
        }

        if ($dbAvailable) {
            try {
                $model = new AnnouncementModel();
                $announcements = $model->orderBy('created_at', 'DESC')->findAll();
            } catch (\Throwable $e) {
                // Log and continue with empty list
                log_message('error', 'Error fetching announcements: ' . $e->getMessage());
                $announcements = [];
            }
        }

        return view('announcements', ['announcements' => $announcements]);
    }
}

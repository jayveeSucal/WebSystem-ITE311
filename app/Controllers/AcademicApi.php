<?php

namespace App\Controllers;

class AcademicApi extends BaseController
{
    public function semestersByYear($yearId)
    {
        $db = \Config\Database::connect();

        $rows = $db->table('semesters')
            ->where('academic_year_id', (int) $yearId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($rows);
    }

    public function termsBySemester($semesterId)
    {
        $db = \Config\Database::connect();

        $rows = $db->table('terms')
            ->where('semester_id', (int) $semesterId)
            ->orderBy('term_number', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($rows);
    }
}

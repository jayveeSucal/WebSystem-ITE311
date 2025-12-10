<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedAcademicStructure extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Insert a default academic year if none exists
        $year = $db->table('academic_years')->where('name', '2025-2026')->get()->getRowArray();
        if (! $year) {
            $db->table('academic_years')->insert(['name' => '2025-2026']);
            $yearId = $db->insertID();
        } else {
            $yearId = (int) $year['id'];
        }

        // Insert a default semester for that year
        $sem = $db->table('semesters')
            ->where('academic_year_id', $yearId)
            ->where('name', '1st Semester')
            ->get()
            ->getRowArray();
        if (! $sem) {
            $db->table('semesters')->insert([
                'academic_year_id' => $yearId,
                'name'             => '1st Semester',
            ]);
            $semId = $db->insertID();
        } else {
            $semId = (int) $sem['id'];
        }

        // Insert a default term for that semester
        $term = $db->table('terms')
            ->where('semester_id', $semId)
            ->where('term_number', 1)
            ->get()
            ->getRowArray();
        if (! $term) {
            $db->table('terms')->insert([
                'semester_id' => $semId,
                'term_number' => 1,
            ]);
        }
    }

    public function down()
    {
        // Optional: do not delete data on rollback to avoid removing real data.
    }
}

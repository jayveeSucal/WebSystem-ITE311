<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedAcademicStructure extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Insert a default academic year if none exists
        // Note: academic_years table has year_start and year_end, not name
        $year = $db->table('academic_years')
            ->where('year_start', 2025)
            ->where('year_end', 2026)
            ->get()
            ->getRowArray();
        if (! $year) {
            $db->table('academic_years')->insert([
                'year_start' => 2025,
                'year_end'   => 2026,
                'is_current' => 1,
            ]);
            $yearId = $db->insertID();
        } else {
            $yearId = (int) $year['id'];
        }

        // Insert a default semester for that year
        // Note: semesters table has name and sequence
        $sem = $db->table('semesters')
            ->where('academic_year_id', $yearId)
            ->where('name', 'First')
            ->get()
            ->getRowArray();
        if (! $sem) {
            $db->table('semesters')->insert([
                'academic_year_id' => $yearId,
                'name'             => 'First',
                'sequence'         => 1,
                'is_current'       => 1,
            ]);
            $semId = $db->insertID();
        } else {
            $semId = (int) $sem['id'];
        }

        // Insert a default term for that semester
        // Note: terms table has name and sequence, not term_number
        $term = $db->table('terms')
            ->where('semester_id', $semId)
            ->where('name', 'Term 1')
            ->get()
            ->getRowArray();
        if (! $term) {
            $db->table('terms')->insert([
                'semester_id' => $semId,
                'name'        => 'Term 1',
                'sequence'    => 1,
                'is_current'  => 1,
            ]);
        }
    }

    public function down()
    {
        // Optional: do not delete data on rollback to avoid removing real data.
    }
}

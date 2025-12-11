-- ============================================
-- FIX FOR: "Unknown column 'name' in 'where clause'"
-- ============================================
-- 
-- The 'sequential' table has an incorrect structure.
-- This script will drop and recreate it with the correct structure.
--
-- IMPORTANT: This will reset your migration history, but migrations will
-- be re-run automatically with the correct structure.
--
-- Run this SQL in your database (phpMyAdmin, MySQL Workbench, or command line):
-- ============================================

-- Step 1: Drop the incorrect table
DROP TABLE IF EXISTS sequential;

-- Step 2: After running this, go back to your terminal and run:
--        php spark migrate
--
-- CodeIgniter will automatically recreate the table with the correct structure:
--   - id (BIGINT, auto_increment, primary key)
--   - version (VARCHAR 255)
--   - class (VARCHAR 255)
--   - group (VARCHAR 255)
--   - namespace (VARCHAR 255)
--   - time (INT)
--   - batch (INT, unsigned)
--
-- The table should NOT have a 'name' column.


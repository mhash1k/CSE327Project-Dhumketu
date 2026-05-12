-- ==============================================================================
-- DATABASE CONSTRAINTS (CHECK, UNIQUE, DEFAULT, NOT NULL)
-- ==============================================================================
-- This script adds all missing constraints to meet DBMS course requirements
-- Run this after creating the initial database structure

USE simple_school_db;

-- ==============================================================================
-- 1. CHECK CONSTRAINTS
-- ==============================================================================

/*
 * Constraint: Ensure marks are within valid range (0-100)
 * Applied to: results table
 */
ALTER TABLE results 
ADD CONSTRAINT chk_marks_range 
CHECK (marks >= 0 AND marks <= 100);

/*
 * Constraint: Ensure fee amounts are positive
 * Applied to: fees table
 */
ALTER TABLE fees 
ADD CONSTRAINT chk_amount_positive 
CHECK (amount > 0);

/*
 * Constraint: Ensure roll numbers are positive
 * Applied to: students table
 */
ALTER TABLE students 
ADD CONSTRAINT chk_roll_positive 
CHECK (roll_number > 0);

-- ==============================================================================
-- 2. UNIQUE CONSTRAINTS
-- ==============================================================================

/*
 * Constraint: Unique roll number per class and section combination
 * Prevents duplicate roll numbers within the same section
 * Applied to: students table
 */
ALTER TABLE students 
ADD CONSTRAINT uq_roll_per_section 
UNIQUE (class_id, section_id, roll_number);

/*
 * Constraint: Unique subject codes
 * Ensures each subject has a distinct code
 * Applied to: subjects table
 */
ALTER TABLE subjects 
ADD CONSTRAINT uq_subject_code 
UNIQUE (subject_code);

-- Note: UNIQUE constraint on users.username already exists from initial setup

-- ==============================================================================
-- 3. DEFAULT VALUES
-- ==============================================================================

/*
 * Default: Attendance status defaults to 'Present'
 * Applied to: attendance table
 */
ALTER TABLE attendance 
ALTER COLUMN status SET DEFAULT 'Present';

/*
 * Default: Fee status defaults to 'Unpaid'
 * Applied to: fees table
 */
ALTER TABLE fees 
ALTER COLUMN status SET DEFAULT 'Unpaid';

-- ==============================================================================
-- 4. NOT NULL CONSTRAINTS
-- ==============================================================================

/*
 * Make essential name fields NOT NULL
 * Ensures data integrity for core entities
 */

-- Students must have a name
ALTER TABLE students 
MODIFY COLUMN name VARCHAR(100) NOT NULL;

-- Teachers must have a name
ALTER TABLE teachers 
MODIFY COLUMN name VARCHAR(100) NOT NULL;

-- Classes must have a name
ALTER TABLE classes 
MODIFY COLUMN class_name VARCHAR(50) NOT NULL;

-- Subjects must have a name
ALTER TABLE subjects 
MODIFY COLUMN subject_name VARCHAR(100) NOT NULL;

-- Exams must have a name
ALTER TABLE exams 
MODIFY COLUMN exam_name VARCHAR(100) NOT NULL;

-- Attendance must have a date
ALTER TABLE attendance 
MODIFY COLUMN date DATE NOT NULL;

-- ==============================================================================
-- VERIFICATION QUERIES
-- ==============================================================================

-- Verify constraints were added successfully
SELECT 
    CONSTRAINT_NAME, 
    CONSTRAINT_TYPE, 
    TABLE_NAME 
FROM 
    INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
WHERE 
    TABLE_SCHEMA = 'simple_school_db' 
ORDER BY 
    TABLE_NAME, CONSTRAINT_TYPE;

-- Check individual constraint details
SELECT 
    TABLE_NAME, 
    COLUMN_NAME, 
    CONSTRAINT_NAME, 
    CHECK_CLAUSE 
FROM 
    INFORMATION_SCHEMA.CHECK_CONSTRAINTS 
WHERE 
    CONSTRAINT_SCHEMA = 'simple_school_db';

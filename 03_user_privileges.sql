-- ==============================================================================
-- USER ACCESS CONTROL
-- ==============================================================================
-- This script creates custom database users and grants appropriate privileges
-- Demonstrates DBMS security and access control concepts

USE simple_school_db;

-- ==============================================================================
-- 1. DROP EXISTING USERS (if they exist)
-- ==============================================================================
-- Clean up any existing users to avoid conflicts

DROP USER IF EXISTS 'school_admin'@'localhost';
DROP USER IF EXISTS 'school_teacher'@'localhost';
DROP USER IF EXISTS 'school_readonly'@'localhost';

-- ==============================================================================
-- 2. CREATE CUSTOM USERS
-- ==============================================================================

/*
 * User: school_admin
 * Purpose: Full database administrator with all privileges
 * Password: Admin@123
 */
CREATE USER 'school_admin'@'localhost' IDENTIFIED BY 'Admin@123';

/*
 * User: school_teacher
 * Purpose: Limited access for teachers to manage attendance and results
 * Password: Teacher@123
 */
CREATE USER 'school_teacher'@'localhost' IDENTIFIED BY 'Teacher@123';

/*
 * User: school_readonly
 * Purpose: Read-only access for reports and viewing data
 * Password: Readonly@123
 * Note: Includes WITH GRANT OPTION to delegate SELECT privileges
 */
CREATE USER 'school_readonly'@'localhost' IDENTIFIED BY 'Readonly@123';

-- ==============================================================================
-- 3. GRANT PRIVILEGES
-- ==============================================================================

-- -----------------------------------------------------------------------------
-- ADMIN USER: Full access to all tables
-- -----------------------------------------------------------------------------
/*
 * Grant all DML operations (SELECT, INSERT, UPDATE, DELETE) on entire database
 * This user can perform all data operations but cannot modify structure
 */
GRANT SELECT, INSERT, UPDATE, DELETE ON simple_school_db.* 
TO 'school_admin'@'localhost';

-- -----------------------------------------------------------------------------
-- TEACHER USER: Limited access for teaching activities
-- -----------------------------------------------------------------------------

-- Can view student information
GRANT SELECT ON simple_school_db.students 
TO 'school_teacher'@'localhost';

-- Can view class and section information
GRANT SELECT ON simple_school_db.classes 
TO 'school_teacher'@'localhost';

GRANT SELECT ON simple_school_db.sections 
TO 'school_teacher'@'localhost';

-- Can view subject information
GRANT SELECT ON simple_school_db.subjects 
TO 'school_teacher'@'localhost';

-- Can view exam information
GRANT SELECT ON simple_school_db.exams 
TO 'school_teacher'@'localhost';

-- Can manage attendance (view, add, update)
GRANT SELECT, INSERT, UPDATE ON simple_school_db.attendance 
TO 'school_teacher'@'localhost';

-- Can manage exam results (view, add, update)
GRANT SELECT, INSERT, UPDATE ON simple_school_db.results 
TO 'school_teacher'@'localhost';

-- Can view fees but not modify
GRANT SELECT ON simple_school_db.fees 
TO 'school_teacher'@'localhost';

-- -----------------------------------------------------------------------------
-- READONLY USER: View-only access with GRANT OPTION
-- -----------------------------------------------------------------------------
/*
 * SELECT privilege on all tables
 * WITH GRANT OPTION allows this user to grant SELECT to other users
 * Useful for report generation and data analysis
 */
GRANT SELECT ON simple_school_db.* 
TO 'school_readonly'@'localhost' 
WITH GRANT OPTION;

-- ==============================================================================
-- 4. GRANT VIEW ACCESS
-- ==============================================================================

-- All users can access views for simplified queries
GRANT SELECT ON simple_school_db.v_student_info 
TO 'school_teacher'@'localhost', 'school_readonly'@'localhost';

GRANT SELECT ON simple_school_db.v_attendance_summary 
TO 'school_teacher'@'localhost', 'school_readonly'@'localhost';

GRANT SELECT ON simple_school_db.v_exam_results 
TO 'school_teacher'@'localhost', 'school_readonly'@'localhost';

GRANT SELECT ON simple_school_db.v_subject_assignments 
TO 'school_teacher'@'localhost', 'school_readonly'@'localhost';

GRANT SELECT ON simple_school_db.v_fee_summary 
TO 'school_teacher'@'localhost', 'school_readonly'@'localhost';

GRANT SELECT ON simple_school_db.v_class_statistics 
TO 'school_teacher'@'localhost', 'school_readonly'@'localhost';

-- ==============================================================================
-- 5. APPLY CHANGES
-- ==============================================================================

FLUSH PRIVILEGES;

-- ==============================================================================
-- 6. VERIFICATION QUERIES
-- ==============================================================================

-- View all users
SELECT 
    User, 
    Host 
FROM 
    mysql.user 
WHERE 
    User LIKE 'school_%'
ORDER BY 
    User;

-- View privileges for school_admin
SHOW GRANTS FOR 'school_admin'@'localhost';

-- View privileges for school_teacher
SHOW GRANTS FOR 'school_teacher'@'localhost';

-- View privileges for school_readonly
SHOW GRANTS FOR 'school_readonly'@'localhost';

-- ==============================================================================
-- TESTING USER ACCESS
-- ==============================================================================

/*
 * To test these users, connect with:
 * 
 * mysql -u school_admin -p'Admin@123' simple_school_db
 * mysql -u school_teacher -p'Teacher@123' simple_school_db
 * mysql -u school_readonly -p'Readonly@123' simple_school_db
 * 
 * Then try various operations to verify privilege restrictions:
 * 
 * As school_teacher:
 *   SELECT * FROM students;  -- Should work
 *   INSERT INTO attendance (student_id, date, status) VALUES (1, '2025-01-15', 'Present');  -- Should work
 *   DELETE FROM students WHERE student_id = 1;  -- Should FAIL (no DELETE privilege)
 * 
 * As school_readonly:
 *   SELECT * FROM v_student_info;  -- Should work
 *   INSERT INTO students (name) VALUES ('Test');  -- Should FAIL (read-only)
 *   GRANT SELECT ON simple_school_db.students TO 'another_user'@'localhost';  -- Should work (has GRANT OPTION)
 */

-- ==============================================================================
-- DEMONSTRATION OF WITH GRANT OPTION
-- ==============================================================================
/*
 * The school_readonly user can grant SELECT privileges to other users
 * This demonstrates the WITH GRANT OPTION functionality
 * 
 * Example (run as school_readonly user):
 * GRANT SELECT ON simple_school_db.students TO 'another_user'@'localhost';
 */

-- ==============================================================================
-- PUBLIC ACCESS (Optional - not recommended for production)
-- ==============================================================================
/*
 * If PUBLIC access is needed (not recommended for security):
 * GRANT SELECT ON simple_school_db.* TO ''@'localhost';
 * 
 * This is commented out as it's a security risk
 */

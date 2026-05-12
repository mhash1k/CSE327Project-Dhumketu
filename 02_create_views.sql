-- ==============================================================================
-- DATABASE VIEWS
-- ==============================================================================
-- This script creates all required views for the DBMS course project
-- Views simplify complex queries and provide a layer of abstraction

USE simple_school_db;

-- ==============================================================================
-- VIEW 1: Student Complete Information
-- ==============================================================================
/*
 * Purpose: Provides a comprehensive view of student information
 * Includes: Student details with their class and section names
 * Uses: LEFT JOIN to include students even without assigned classes
 */
CREATE OR REPLACE VIEW v_student_info AS
SELECT 
    s.student_id,
    s.name AS student_name,
    s.roll_number,
    c.class_name,
    sec.section_name,
    s.guardian_name,
    s.guardian_phone,
    s.contact_details
FROM students s
LEFT JOIN classes c ON s.class_id = c.class_id
LEFT JOIN sections sec ON s.section_id = sec.section_id;

-- ==============================================================================
-- VIEW 2: Attendance Summary
-- ==============================================================================
/*
 * Purpose: Calculates attendance statistics for each student
 * Includes: Total days, present/absent/late counts, and attendance percentage
 * Uses: Aggregate functions (COUNT, SUM) and GROUP BY
 * Demonstrates: CASE expressions, aggregate functions, GROUP BY
 */
CREATE OR REPLACE VIEW v_attendance_summary AS
SELECT 
    s.student_id,
    s.name AS student_name,
    c.class_name,
    COUNT(a.attendance_id) AS total_days,
    SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present_days,
    SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent_days,
    SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) AS late_days,
    ROUND(
        CASE 
            WHEN COUNT(a.attendance_id) > 0 
            THEN (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) * 100.0 / COUNT(a.attendance_id))
            ELSE 0 
        END, 2
    ) AS attendance_percentage
FROM students s
LEFT JOIN classes c ON s.class_id = c.class_id
LEFT JOIN attendance a ON s.student_id = a.student_id
GROUP BY s.student_id, s.name, c.class_name;

-- ==============================================================================
-- VIEW 3: Exam Results with Grades
-- ==============================================================================
/*
 * Purpose: Shows exam results with calculated letter grades
 * Includes: Exam info, student info, subject, marks, and grade
 * Uses: Multiple INNER JOINs (4 tables), CASE expression for grading
 * Demonstrates: Multi-table INNER JOIN, calculated columns
 */
CREATE OR REPLACE VIEW v_exam_results AS
SELECT 
    e.exam_id,
    e.exam_name,
    e.exam_date,
    s.student_id,
    s.name AS student_name,
    c.class_name,
    sub.subject_name,
    r.marks,
    CASE 
        WHEN r.marks >= 80 THEN 'A+'
        WHEN r.marks >= 70 THEN 'A'
        WHEN r.marks >= 60 THEN 'B'
        WHEN r.marks >= 50 THEN 'C'
        WHEN r.marks >= 40 THEN 'D'
        ELSE 'F'
    END AS grade
FROM results r
INNER JOIN exams e ON r.exam_id = e.exam_id
INNER JOIN students s ON r.student_id = s.student_id
INNER JOIN subjects sub ON r.subject_id = sub.subject_id
LEFT JOIN classes c ON s.class_id = c.class_id;

-- ==============================================================================
-- VIEW 4: Subject Teacher Assignments
-- ==============================================================================
/*
 * Purpose: Shows which teacher teaches which subject in which class
 * Demonstrates: 3-table JOIN with descriptive naming
 */
CREATE OR REPLACE VIEW v_subject_assignments AS
SELECT 
    sub.subject_id,
    sub.subject_name,
    sub.subject_code,
    c.class_name,
    t.name AS teacher_name,
    t.subject_speciality AS teacher_specialization
FROM subjects sub
LEFT JOIN classes c ON sub.class_id = c.class_id
LEFT JOIN teachers t ON sub.teacher_id = t.teacher_id;

-- ==============================================================================
-- VIEW 5: Fee Summary by Student
-- ==============================================================================
/*
 * Purpose: Calculates total fees, paid amount, and unpaid amount per student
 * Demonstrates: Aggregate functions (SUM, COUNT), GROUP BY, CASE expressions
 */
CREATE OR REPLACE VIEW v_fee_summary AS
SELECT 
    s.student_id,
    s.name AS student_name,
    c.class_name,
    COUNT(f.fee_id) AS total_fee_records,
    SUM(f.amount) AS total_fees,
    SUM(CASE WHEN f.status = 'Paid' THEN f.amount ELSE 0 END) AS paid_amount,
    SUM(CASE WHEN f.status = 'Unpaid' THEN f.amount ELSE 0 END) AS unpaid_amount,
    CASE 
        WHEN SUM(CASE WHEN f.status = 'Unpaid' THEN f.amount ELSE 0 END) = 0 THEN 'Cleared'
        ELSE 'Pending'
    END AS payment_status
FROM students s
LEFT JOIN classes c ON s.class_id = c.class_id
LEFT JOIN fees f ON s.student_id = f.student_id
GROUP BY s.student_id, s.name, c.class_name;

-- ==============================================================================
-- VIEW 6: Class Statistics
-- ==============================================================================
/*
 * Purpose: Shows student count and section count per class
 * Demonstrates: Multiple LEFT JOINs, COUNT DISTINCT, GROUP BY
 */
CREATE OR REPLACE VIEW v_class_statistics AS
SELECT 
    c.class_id,
    c.class_name,
    COUNT(DISTINCT s.student_id) AS student_count,
    COUNT(DISTINCT sec.section_id) AS section_count,
    COUNT(DISTINCT sub.subject_id) AS subject_count
FROM classes c
LEFT JOIN students s ON c.class_id = s.class_id
LEFT JOIN sections sec ON c.class_id = sec.class_id
LEFT JOIN subjects sub ON c.class_id = sub.class_id
GROUP BY c.class_id, c.class_name;

-- ==============================================================================
-- VERIFICATION QUERIES
-- ==============================================================================

-- List all views in the database
SELECT 
    TABLE_NAME, 
    TABLE_TYPE 
FROM 
    INFORMATION_SCHEMA.TABLES 
WHERE 
    TABLE_SCHEMA = 'simple_school_db' 
    AND TABLE_TYPE = 'VIEW'
ORDER BY 
    TABLE_NAME;

-- Test each view
SELECT 'v_student_info' AS view_name, COUNT(*) AS row_count FROM v_student_info
UNION ALL
SELECT 'v_attendance_summary', COUNT(*) FROM v_attendance_summary
UNION ALL
SELECT 'v_exam_results', COUNT(*) FROM v_exam_results
UNION ALL
SELECT 'v_subject_assignments', COUNT(*) FROM v_subject_assignments
UNION ALL
SELECT 'v_fee_summary', COUNT(*) FROM v_fee_summary
UNION ALL
SELECT 'v_class_statistics', COUNT(*) FROM v_class_statistics;

-- Sample data from each view
SELECT * FROM v_student_info LIMIT 5;
SELECT * FROM v_attendance_summary LIMIT 5;
SELECT * FROM v_exam_results LIMIT 5;
SELECT * FROM v_subject_assignments LIMIT 5;
SELECT * FROM v_fee_summary LIMIT 5;
SELECT * FROM v_class_statistics;

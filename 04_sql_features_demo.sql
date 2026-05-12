-- ==============================================================================
-- ADVANCED SQL FEATURES DEMONSTRATION
-- ==============================================================================
-- This script demonstrates all required SQL features for the DBMS course project
-- It includes examples of advanced queries, aggregates, subqueries, and more

USE simple_school_db;

-- ==============================================================================
-- 1. AGGREGATE FUNCTIONS (COUNT, SUM, AVG, MIN, MAX)
-- ==============================================================================

-- Count total students
SELECT COUNT(*) AS total_students 
FROM students;

-- Count students per class with GROUP BY
SELECT 
    c.class_name,
    COUNT(s.student_id) AS student_count
FROM classes c
LEFT JOIN students s ON c.class_id = s.class_id
GROUP BY c.class_id, c.class_name
ORDER BY student_count DESC;

-- Calculate total revenue from fees
SELECT 
    SUM(amount) AS total_revenue,
    SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END) AS collected_revenue,
    SUM(CASE WHEN status = 'Unpaid' THEN amount ELSE 0 END) AS pending_revenue
FROM fees;

-- Average, minimum, and maximum marks per exam
SELECT 
    e.exam_name,
    COUNT(r.result_id) AS total_results,
    AVG(r.marks) AS average_marks,
    MIN(r.marks) AS minimum_marks,
    MAX(r.marks) AS maximum_marks
FROM exams e
INNER JOIN results r ON e.exam_id = r.exam_id
GROUP BY e.exam_id, e.exam_name;

-- Average marks per subject
SELECT 
    sub.subject_name,
    COUNT(r.result_id) AS student_count,
    AVG(r.marks) AS avg_marks,
    MIN(r.marks) AS min_marks,
    MAX(r.marks) AS max_marks
FROM subjects sub
INNER JOIN results r ON sub.subject_id = r.subject_id
GROUP BY sub.subject_id, sub.subject_name
ORDER BY avg_marks DESC;

-- ==============================================================================
-- 2. GROUP BY and HAVING Clauses
-- ==============================================================================

-- Classes with more than 5 students (HAVING clause)
SELECT 
    c.class_name,
    COUNT(s.student_id) AS student_count
FROM classes c
INNER JOIN students s ON c.class_id = s.class_id
GROUP BY c.class_id, c.class_name
HAVING COUNT(s.student_id) > 5
ORDER BY student_count DESC;

-- Subjects with average marks above 50 (HAVING with aggregate)
SELECT 
    sub.subject_name,
    AVG(r.marks) AS avg_marks,
    COUNT(r.result_id) AS result_count
FROM subjects sub
INNER JOIN results r ON sub.subject_id = r.subject_id
GROUP BY sub.subject_id, sub.subject_name
HAVING AVG(r.marks) >= 50
ORDER BY avg_marks DESC;

-- Students with total fees above 1000
SELECT 
    s.name AS student_name,
    SUM(f.amount) AS total_fees
FROM students s
INNER JOIN fees f ON s.student_id = f.student_id
GROUP BY s.student_id, s.name
HAVING SUM(f.amount) > 1000;

-- ==============================================================================
-- 3. SUBQUERIES
-- ==============================================================================

-- Single-row subquery: Find student with highest marks in any exam
SELECT 
    s.name AS student_name,
    r.marks
FROM students s
INNER JOIN results r ON s.student_id = r.student_id
WHERE r.marks = (SELECT MAX(marks) FROM results);

-- Single-row subquery: Find average attendance percentage
SELECT 
    s.name AS student_name,
    COUNT(a.attendance_id) AS total_days,
    SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present_days,
    ROUND(SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) * 100.0 / COUNT(a.attendance_id), 2) AS attendance_pct
FROM students s
INNER JOIN attendance a ON s.student_id = a.student_id
GROUP BY s.student_id, s.name
HAVING attendance_pct > (
    SELECT AVG(pct) FROM (
        SELECT SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) * 100.0 / COUNT(*) AS pct
        FROM attendance
        GROUP BY student_id
    ) AS avg_attendance
);

-- Multiple-row subquery with IN
--Find students enrolled in classes that have completed exams
SELECT 
    student_id,
    name,
    class_id
FROM students
WHERE class_id IN (
    SELECT DISTINCT c.class_id 
    FROM classes c
    INNER JOIN subjects sub ON c.class_id = sub.class_id
    INNER JOIN results r ON sub.subject_id = r.subject_id
);

-- Multiple-row subquery with ANY
-- Find students in any class taught by a specific teacher
SELECT 
    s.student_id,
    s.name,
    c.class_name
FROM students s
INNER JOIN classes c ON s.class_id = c.class_id
WHERE s.class_id = ANY (
    SELECT class_id 
    FROM subjects 
    WHERE teacher_id = 1
);

-- Multiple-row subquery with ALL
-- Find students who scored higher than ALL students in class 1
SELECT 
    s.student_id,
    s.name,
    AVG(r.marks) AS avg_marks
FROM students s
INNER JOIN results r ON s.student_id = r.student_id
GROUP BY s.student_id, s.name
HAVING AVG(r.marks) > ALL (
    SELECT AVG(marks) 
    FROM results r2
    INNER JOIN students s2 ON r2.student_id = s2.student_id
    WHERE s2.class_id = 1
    GROUP BY s2.student_id
);

-- ==============================================================================
-- 4. DISTINCT Keyword
-- ==============================================================================

-- Find distinct classes that have students enrolled
SELECT DISTINCT c.class_name
FROM classes c
INNER JOIN students s ON c.class_id = s.class_id;

-- Find distinct teachers who have assigned subjects
SELECT DISTINCT t.name AS teacher_name
FROM teachers t
INNER JOIN subjects sub ON t.teacher_id = sub.teacher_id;

-- Count distinct students who have attendance records
SELECT COUNT(DISTINCT student_id) AS students_with_attendance
FROM attendance;

-- ==============================================================================
-- 5. IS NULL / IS NOT NULL
-- ==============================================================================

-- Find students without assigned class
SELECT 
    student_id,
    name,
    guardian_name
FROM students
WHERE class_id IS NULL OR section_id IS NULL;

-- Find students with complete information
SELECT 
    student_id,
    name,
    class_id,
    section_id
FROM students
WHERE class_id IS NOT NULL AND section_id IS NOT NULL;

-- Find subjects without assigned teacher
SELECT 
    subject_id,
    subject_name,
    subject_code
FROM subjects
WHERE teacher_id IS NULL;

-- ==============================================================================
-- 6. PATTERN MATCHING with LIKE
-- ==============================================================================

-- Find students whose name starts with 'A'
SELECT * FROM students WHERE name LIKE 'A%';

-- Find students whose name contains 'khan'
SELECT * FROM students WHERE name LIKE '%khan%';

-- Find classes starting with 'Class'
SELECT * FROM classes WHERE class_name LIKE 'Class%';

-- Find teachers specializing in 'Math'
SELECT * FROM teachers WHERE subject_speciality LIKE '%Math%';

-- ==============================================================================
-- 7. ORDER BY and LIMIT
-- ==============================================================================

-- Top 10 students by marks (highest first)
SELECT 
    s.name AS student_name,
    AVG(r.marks) AS avg_marks
FROM students s
INNER JOIN results r ON s.student_id = r.student_id
GROUP BY s.student_id, s.name
ORDER BY avg_marks DESC
LIMIT 10;

-- Recent 5 exams
SELECT * FROM exams 
ORDER BY exam_date DESC 
LIMIT 5;

-- Students with lowest attendance (bottom 10)
SELECT 
    s.name AS student_name,
    SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) * 100.0 / COUNT(*) AS attendance_pct
FROM students s
INNER JOIN attendance a ON s.student_id = a.student_id
GROUP BY s.student_id, s.name
ORDER BY attendance_pct ASC
LIMIT 10;

-- ==============================================================================
-- 8. EXPLICIT JOIN TYPES
-- ==============================================================================

-- INNER JOIN: Only students with exam results
SELECT 
    s.name AS student_name,
    e.exam_name,
    r.marks
FROM students s
INNER JOIN results r ON s.student_id = r.student_id
INNER JOIN exams e ON r.exam_id = e.exam_id;

-- LEFT JOIN: All students, with or without results
SELECT 
    s.name AS student_name,
    COUNT(r.result_id) AS exam_count
FROM students s
LEFT JOIN results r ON s.student_id = r.student_id
GROUP BY s.student_id, s.name;

-- RIGHT JOIN: All teachers, with or without assigned subjects
SELECT 
    t.name AS teacher_name,
    COUNT(sub.subject_id) AS subject_count
FROM subjects sub
RIGHT JOIN teachers t ON sub.teacher_id = t.teacher_id
GROUP BY t.teacher_id, t.name;

-- 3+ TABLE JOIN: Complete exam results with all info
SELECT 
    e.exam_name,
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
INNER JOIN classes c ON s.class_id = c.class_id
ORDER BY e.exam_name, c.class_name, s.name;

-- ==============================================================================
-- 9. LOGICAL OPERATORS (AND, OR, NOT)
-- ==============================================================================

-- Students in Class 1 OR Class 2
SELECT * FROM students
WHERE class_id = 1 OR class_id = 2;

-- Students with roll number between 1 and 10 AND in Class 1
SELECT * FROM students
WHERE roll_number BETWEEN 1 AND 10 AND class_id = 1;

-- Students NOT in Class 1
SELECT * FROM students
WHERE NOT class_id = 1;

-- Complex combination: Students in Class 1 or 2, with roll number > 5, and have guardian info
SELECT * FROM students
WHERE (class_id = 1 OR class_id = 2)
  AND roll_number > 5
  AND guardian_name IS NOT NULL;

-- ==============================================================================
-- 10. EXPRESSIONS and ALIASES
-- ==============================================================================

-- Calculate passing percentage per class
SELECT 
    c.class_name,
    COUNT(r.result_id) AS total_exams,
    SUM(CASE WHEN r.marks >= 40 THEN 1 ELSE 0 END) AS passed,
    SUM(CASE WHEN r.marks < 40 THEN 1 ELSE 0 END) AS failed,
    ROUND(SUM(CASE WHEN r.marks >= 40 THEN 1 ELSE 0 END) * 100.0 / COUNT(r.result_id), 2) AS pass_percentage
FROM classes c
INNER JOIN students s ON c.class_id = s.class_id
INNER JOIN results r ON s.student_id = r.student_id
GROUP BY c.class_id, c.class_name;

-- Student performance with grade calculation
SELECT 
    s.student_id,
    s.name AS student_name,
    COUNT(r.result_id) AS total_exams,
    AVG(r.marks) AS average_marks,
    CASE 
        WHEN AVG(r.marks) >= 80 THEN 'Excellent'
        WHEN AVG(r.marks) >= 60 THEN 'Good'
        WHEN AVG(r.marks) >= 40 THEN 'Average'
        ELSE 'Needs Improvement'
    END AS performance_rating
FROM students s
INNER JOIN results r ON s.student_id = r.student_id
GROUP BY s.student_id, s.name;

-- ==============================================================================
-- SUMMARY OF SQL FEATURES DEMONSTRATED
-- ==============================================================================
/*
✅ Aggregate Functions: COUNT, SUM, AVG, MIN, MAX
✅ GROUP BY clause
✅ HAVING clause (filtering aggregated results)
✅ Subqueries:
   - Single-row subquery
   - Multiple-row subquery with IN
   - Multiple-row subquery with ANY
   - Multiple-row subquery with ALL
✅ DISTINCT keyword
✅ IS NULL / IS NOT NULL
✅ LIKE pattern matching
✅ ORDER BY
✅ LIMIT
✅ INNER JOIN (explicit syntax)
✅ LEFT JOIN
✅ RIGHT JOIN
✅ 3+ table JOINs
✅ Logical operators (AND, OR, NOT)
✅ Expressions and aliases
✅ WHERE clause
✅ CASE expressions
*/

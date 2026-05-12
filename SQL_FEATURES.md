# SQL Features Implementation Guide
## Mapping Requirements to Actual Code

This document serves as a guide for grading and verification, mapping every DBMS course requirement to the exact file and line of code in the application.

---

## 1. Basic SQL Operations

| Requirement | File | Line Number | Implementation Details |
|-------------|------|-------------|------------------------|
| **INSERT** | `add_student.php` | Line 13 | `INSERT INTO students (...) VALUES (...)` |
| **UPDATE** | `edit_student.php` | Line 15 | `UPDATE students SET ... WHERE ...` |
| **DELETE** | `students.php` | Line 9 | `DELETE FROM students WHERE ...` |
| **SELECT** | `students.php` | Line 67 | `SELECT ... FROM students` |

---

## 2. Query Features

| Requirement | File | Line Number | Implementation Details |
|-------------|------|-------------|------------------------|
| **WHERE** | `results.php` | Line 49 | `WHERE r.exam_id = ?` |
| **LIKE** | `students.php` | Line 62 | `WHERE students.name LIKE ?` (Search Feature) |
| **ORDER BY** | `reports.php` | Line 31 | `ORDER BY student_count DESC` |
| **LIMIT** | `reports.php` | Line 61 | `LIMIT 10` (Pagination demo) |
| **IS NULL** | `reports.php` | Line 92 | `WHERE class_id IS NULL` |
| **DISTINCT** | `reports.php` | Line 119 | `SELECT DISTINCT c.class_name` |

---

## 3. Joins & Multi-table Queries

| Requirement | File | Line Number | Implementation Details |
|-------------|------|-------------|------------------------|
| **INNER JOIN** | `results.php` | Line 48 | `INNER JOIN subjects sub ON ...` |
| **LEFT JOIN** | `students.php` | Line 69 | `LEFT JOIN classes ... LEFT JOIN sections ...` |
| **3+ Table Join** | `students.php` | Line 67-70 | Joins `students`, `classes`, and `sections` tables |

---

## 4. Aggregates & Group Functions

| Requirement | File | Line Number | Implementation Details |
|-------------|------|-------------|------------------------|
| **COUNT()** | `students.php` | Line 24 | `SELECT COUNT(*) as total` (Total Students) |
| **COUNT()** | `results.php` | Line 43 | `COUNT(r.result_id)` (Students per subject) |
| **SUM()** | `sql_scripts/04_sql_features_demo.sql` | Demo Script | *Available in demo script* |
| **AVG()** | `results.php` | Line 46 | `AVG(r.marks)` (Average subject marks) |
| **MIN()** | `results.php` | Line 44 | `MIN(r.marks)` (Lowest subject marks) |
| **MAX()** | `results.php` | Line 45 | `MAX(r.marks)` (Highest subject marks) |
| **GROUP BY** | `results.php` | Line 50 | `GROUP BY sub.subject_id` |
| **HAVING** | `reports.php` | Line 30 | `HAVING COUNT(s.student_id) >= 2` |

---

## 5. Views & Subqueries

| Requirement | File | Line Number | Implementation Details |
|-------------|------|-------------|------------------------|
| **Using Views** | `reports.php` | Line 61 | `SELECT * FROM v_student_info` |
| **View Definition**| `sql_scripts/02_create_views.sql` | Line 10 | `CREATE VIEW v_student_info AS ...` |
| **Subqueries** | `sql_scripts/04_sql_features_demo.sql` | Demo Script | *Available in demo script* |

---

## 6. Database Constraints

| Requirement | File | Line Number | Implementation Details |
|-------------|------|-------------|------------------------|
| **CHECK** | `sql_scripts/01_add_constraints.sql` | Line 25 | `CHECK (marks >= 0 AND marks <= 100)` |
| **UNIQUE** | `sql_scripts/01_add_constraints.sql` | Line 65 | `UNIQUE (subject_code)` |
| **DEFAULT** | `sql_scripts/01_add_constraints.sql` | Line 85 | `DEFAULT 'Present'` |
| **NOT NULL** | `sql_scripts/01_add_constraints.sql` | Line 105 | `MODIFY student_name ... NOT NULL` |

---

## 7. User Access Control

| Requirement | File | Implementation Details |
|-------------|------|------------------------|
| **Custom Users** | `sql_scripts/03_user_privileges.sql` | Created `school_admin`, `school_teacher`, `school_readonly` |
| **GRANT** | `sql_scripts/03_user_privileges.sql` | `GRANT SELECT, INSERT ON ...` |
| **WITH GRANT** | `sql_scripts/03_user_privileges.sql` | `WITH GRANT OPTION` used for readonly user |

---

## 🔐 Security Measures

| Feature | Implementation | Evidence |
|---------|----------------|----------|
| **SQL Injection** | Prepared Statements | Used in 100% of queries with user input (bind_param) |
| **Passwords** | Bcrypt Hashing | `password_hash()` used in User creation |
| **Sessions** | Secure Session | `check_login.php` included in every authorized page |

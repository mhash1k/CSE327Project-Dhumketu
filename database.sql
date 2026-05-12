CREATE DATABASE IF NOT EXISTS simple_school_db;
USE simple_school_db;

-- 1. STUDENTS TABLE
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    class_id INT,
    section_id INT,
    roll_number INT,
    guardian_name VARCHAR(100),
    guardian_phone VARCHAR(20),
    contact_details VARCHAR(255)
);

-- 2. TEACHERS TABLE
CREATE TABLE IF NOT EXISTS teachers (
    teacher_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    subject_speciality VARCHAR(150),
    contact_details VARCHAR(255)
);

-- 3. CLASSES TABLE
CREATE TABLE IF NOT EXISTS classes (
    class_id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(50) NOT NULL
);

-- SECTIONS TABLE (linked to classes)
CREATE TABLE IF NOT EXISTS sections (
    section_id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    section_name VARCHAR(20),
    class_teacher_id INT,
    FOREIGN KEY (class_id) REFERENCES classes(class_id),
    FOREIGN KEY (class_teacher_id) REFERENCES teachers(teacher_id)
);

-- Add Foreign Keys to Students now that classes and sections exist
ALTER TABLE students ADD CONSTRAINT fk_student_class FOREIGN KEY (class_id) REFERENCES classes(class_id);
ALTER TABLE students ADD CONSTRAINT fk_student_section FOREIGN KEY (section_id) REFERENCES sections(section_id);

-- 4. SUBJECTS TABLE
CREATE TABLE IF NOT EXISTS subjects (
    subject_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100),
    subject_code VARCHAR(20),
    teacher_id INT,
    class_id INT,
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id),
    FOREIGN KEY (class_id) REFERENCES classes(class_id)
);

-- 5. ATTENDANCE TABLE
CREATE TABLE IF NOT EXISTS attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    date DATE,
    status ENUM('Present', 'Absent', 'Late'),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- 6. EXAMS TABLE
CREATE TABLE IF NOT EXISTS exams (
    exam_id INT AUTO_INCREMENT PRIMARY KEY,
    exam_name VARCHAR(100),
    exam_date DATE
);

-- RESULTS TABLE
CREATE TABLE IF NOT EXISTS results (
    result_id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT,
    student_id INT,
    subject_id INT,
    marks INT,
    FOREIGN KEY (exam_id) REFERENCES exams(exam_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
);

-- 7. FEES / PAYMENTS TABLE
CREATE TABLE IF NOT EXISTS fees (
    fee_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    amount DECIMAL(10,2),
    due_date DATE,
    payment_method VARCHAR(50) DEFAULT NULL,
    transaction_id VARCHAR(100) DEFAULT NULL;
    status ENUM('Paid', 'Unpaid'),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- 8. USERS / LOGIN TABLE
CREATE TABLE IF NOT EXISTS books (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),        -- hashed password
    role ENUM('Admin', 'Teacher', 'Student','Guardian'),
    related_id INT                -- student_id OR teacher_id if needed
);

-- 9. BOOK TABLE

CREATE TABLE IF NOT EXISTS fees (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(150) NOT NULL,
    class_id INT,
    FOREIGN KEY (class_id) REFERENCES classes(class_id)
);

-- Insert a default admin user (password: admin123)
INSERT INTO users (username, password, role) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin');

INSERT INTO users (username, password, role, student_id) VALUES ('guardian1', '$2y$10$...', 'Guardian', 2);

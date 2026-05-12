<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Optional: define an array of pages that are admin/teacher only
$admin_only_pages = [
    'add_student.php', 'edit_student.php', 'students.php',
    'add_teacher.php', 'edit_teacher.php', 'teachers.php',
    'add_class.php', 'classes.php', 'manage_sections.php',
    'add_subject.php', 'subjects.php',
    'exams.php', 'results.php',
    'fees.php', 'payment_gateway.php', 'process_payment.php'
];

$current_page = basename($_SERVER['PHP_SELF']);

if (in_array($current_page, $admin_only_pages) && 
    !in_array($_SESSION['role'], ['Admin', 'Teacher'])) {
    header("Location: index.php");  // redirect to appropriate dashboard
    exit();
}

// For guardian-specific pages, you may later restrict further.
?>
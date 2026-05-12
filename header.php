<!DOCTYPE html>
<html>
<head>
    <title>Simple SMS</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>Simple SMS</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Teacher'])): ?>
                        <li><a href="students.php">Students</a></li>
                        <li><a href="teachers.php">Teachers</a></li>
                        <li><a href="classes.php">Classes</a></li>
                        <li><a href="subjects.php">Subjects</a></li>
                        <li><a href="exams.php">Exams</a></li>
                        <li><a href="fees.php">Fees</a></li>
                    <?php endif; ?>
                    <!-- Books accessible to all logged-in roles -->
                    <li><a href="books.php">Books</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
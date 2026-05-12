<?php
include 'check_login.php';
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Simple SMS</title>
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
                    <li><a href="reports.php">Reports</a></li>
                    <li><a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Dashboard</h2>
<p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</p>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px;">

<?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Teacher'): ?>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>Students</h3><p>Manage Students</p>
        <a href="students.php"><button>Go</button></a>
    </div>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>Teachers</h3><p>Manage Teachers</p>
        <a href="teachers.php"><button>Go</button></a>
    </div>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>Classes</h3><p>Manage Classes & Sections</p>
        <a href="classes.php"><button>Go</button></a>
    </div>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>Subjects</h3><p>Manage Subjects</p>
        <a href="subjects.php"><button>Go</button></a>
    </div>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>Attendance</h3><p>Mark & View Attendance</p>
        <a href="attendance.php"><button>Go</button></a>
    </div>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>Exams & Results</h3><p>Manage Exams & Results</p>
        <a href="exams.php"><button>Go</button></a>
    </div>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>Fees</h3><p>Manage Fees</p>
        <a href="fees.php"><button>Go</button></a>
    </div>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>Reports</h3><p>SQL Features Demo</p>
        <a href="reports.php"><button>Go</button></a>
    </div>
<?php elseif ($_SESSION['role'] == 'Student'): ?>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>My Attendance</h3><p>View Attendance</p>
        <a href="attendance.php?student_id=<?= $_SESSION['related_id'] ?>"><button>Go</button></a>
    </div>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>My Results</h3><p>View Results</p>
        <a href="results.php?exam_id=0"><button>Go</button></a> <!-- adjust as needed -->
    </div>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>My Fees</h3><p>View Fee Status</p>
        <a href="fees.php"><button>Go</button></a>
    </div>
<?php elseif ($_SESSION['role'] == 'Guardian'): ?>
    <!-- Guardian dashboard: show child's info -->
    <?php
    $child_id = $_SESSION['related_id'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $child_id);
    $stmt->execute();
    $child = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    ?>
    <p><strong>Your Child:</strong> <?= htmlspecialchars($child['name'] ?? 'Unknown') ?></p>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>Attendance</h3><p>View Child's Attendance</p>
        <a href="child_attendance.php"><button>Go</button></a>
    </div>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>Results</h3><p>View Results</p>
        <a href="child_results.php"><button>Go</button></a>
    </div>
    <div style="background: #eee; padding: 20px; text-align: center;">
        <h3>Fees</h3><p>View Fee Status</p>
        <a href="child_fees.php"><button>Go</button></a>
    </div>
<?php endif; ?>

<!-- Book list accessible to all logged‑in users -->
<div style="background: #eee; padding: 20px; text-align: center;">
    <h3>Book List</h3><p>View Class‑wise Books</p>
    <a href="books.php"><button>Go</button></a>
</div>
</div>
    </div>
</body>
</html>

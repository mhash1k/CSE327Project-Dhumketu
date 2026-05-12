<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

if ($_SESSION['role'] != 'Guardian') {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['related_id'];

$stmt = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Your Child's Attendance</h2>
<table>
    <tr><th>Date</th><th>Status</th></tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr><td><?= $row['date'] ?></td><td><?= $row['status'] ?></td></tr>
    <?php endwhile; ?>
</table>
<a href="index.php">Back to Dashboard</a>
<?php include 'footer.php'; ?>
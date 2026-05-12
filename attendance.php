<?php
include 'check_login.php';
include 'db.php';
include 'header.php';
?>

<h2>Attendance Management</h2>
<a href="index.php">Back to Dashboard</a>
<br>
<a href="mark_attendance.php"><button style="width: auto;">Mark Attendance</button></a>

<h3>View Attendance</h3>
<form method="get" action="">
    <label>Select Student:</label>
    <select name="student_id" required>
        <option value="">Select Student</option>
        <?php
        $students = $conn->query("SELECT * FROM students");
        while($s = $students->fetch_assoc()) {
            echo "<option value='".$s['student_id']."'>".$s['name']." (ID: ".$s['student_id'].")</option>";
        }
        ?>
    </select>
    <button type="submit">View</button>
</form>

<?php
if (isset($_GET['student_id'])) {
    $student_id = (int)$_GET['student_id'];
    
    $stmt = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? ORDER BY date DESC");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h3>Attendance Records</h3>";
    echo "<table>
            <tr>
                <th>Date</th>
                <th>Status</th>
            </tr>";
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['date']."</td>
                    <td>".$row['status']."</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='2'>No records found</td></tr>";
    }
    echo "</table>";
}
?>

<?php include 'footer.php'; ?>

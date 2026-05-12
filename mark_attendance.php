<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $statuses = $_POST['status']; // Array of statuses keyed by student_id

    foreach ($statuses as $student_id => $status) {
        $student_id = (int)$student_id;
        $status = $conn->real_escape_string($status);
        
        // Check if already marked
        $check_stmt = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? AND date = ?");
        $check_stmt->bind_param("is", $student_id, $date);
        $check_stmt->execute();
        $check = $check_stmt->get_result();
        
        if ($check->num_rows > 0) {
            $update_stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE student_id = ? AND date = ?");
            $update_stmt->bind_param("sis", $status, $student_id, $date);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            $insert_stmt = $conn->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("iss", $student_id, $date, $status);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
    echo "<p class='success'>Attendance marked successfully!</p>";
}
?>

<h2>Mark Attendance</h2>
<form method="get" action="">
    <label>Select Class:</label>
    <select name="class_id" required>
        <option value="">Select Class</option>
        <?php
        $classes = $conn->query("SELECT * FROM classes");
        while($c = $classes->fetch_assoc()) {
            $selected = (isset($_GET['class_id']) && $_GET['class_id'] == $c['class_id']) ? "selected" : "";
            echo "<option value='".$c['class_id']."' $selected>".$c['class_name']."</option>";
        }
        ?>
    </select>
    <button type="submit">Load Students</button>
</form>

<?php
if (isset($_GET['class_id'])) {
    $class_id = (int)$_GET['class_id'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $students = $stmt->get_result();

    if ($students->num_rows > 0) {
        echo "<form method='post' action=''>";
        echo "<label>Date:</label>";
        echo "<input type='date' name='date' value='".date('Y-m-d')."' required>";
        
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                </tr>";
        
        while($s = $students->fetch_assoc()) {
            echo "<tr>
                    <td>".$s['student_id']."</td>
                    <td>".$s['name']."</td>
                    <td>
                        <select name='status[".$s['student_id']."]'>
                            <option value='Present'>Present</option>
                            <option value='Absent'>Absent</option>
                            <option value='Late'>Late</option>
                        </select>
                    </td>
                  </tr>";
        }
        echo "</table>";
        echo "<button type='submit'>Save Attendance</button>";
        echo "</form>";
    } else {
        echo "<p>No students found in this class.</p>";
    }
}
?>

<a href="attendance.php">Back to Attendance</a>

<?php include 'footer.php'; ?>

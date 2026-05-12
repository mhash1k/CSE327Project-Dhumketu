<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

$exam_id = (int)$_GET['exam_id'];
$stmt = $conn->prepare("SELECT * FROM exams WHERE exam_id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$exam_result = $stmt->get_result();
$exam = $exam_result->fetch_assoc();
$stmt->close();

// Handle Add Result - secure with prepared statement
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = (int)$_POST['student_id'];
    $subject_id = (int)$_POST['subject_id'];
    $marks = (int)$_POST['marks'];

    $stmt = $conn->prepare("INSERT INTO results (exam_id, student_id, subject_id, marks) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $exam_id, $student_id, $subject_id, $marks);

    if ($stmt->execute()) {
        echo "<p class='success'>Result added successfully</p>";
        $stmt->close();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
        $stmt->close();
    }
}
?>

<h2>Results for Exam: <?php echo $exam['exam_name']; ?></h2>

<?php
/*
 * SQL Features: MIN, MAX, AVG with GROUP BY
 * Shows statistics per subject for this exam
 */
$stats_query = $conn->prepare("SELECT 
    sub.subject_name,
    COUNT(r.result_id) as total_students,
    MIN(r.marks) as min_marks,
    MAX(r.marks) as max_marks,
    AVG(r.marks) as avg_marks
    FROM results r
    INNER JOIN subjects sub ON r.subject_id = sub.subject_id
    WHERE r.exam_id = ?
    GROUP BY sub.subject_id, sub.subject_name");
$stats_query->bind_param("i", $exam_id);
$stats_query->execute();
$stats_result = $stats_query->get_result();

if ($stats_result->num_rows > 0) {
    echo "<h3>Subject-wise Statistics (MIN, MAX, AVG, COUNT, GROUP BY)</h3>";
    echo "<table>";
    echo "<tr>
            <th>Subject</th>
            <th>Students</th>
            <th>Min Marks</th>
            <th>Max Marks</th>
            <th>Avg Marks</th>
          </tr>";
    
    while($stat = $stats_result->fetch_assoc()) {
        echo "<tr>
                <td>".$stat['subject_name']."</td>
                <td>".$stat['total_students']."</td>
                <td>".$stat['min_marks']."</td>
                <td>".$stat['max_marks']."</td>
                <td>".number_format($stat['avg_marks'], 2)."</td>
              </tr>";
    }
    echo "</table><br>";
}
$stats_query->close();
?>

<h3>Add Result</h3>
<form method="post" action="">
    <label>Student:</label>
    <select name="student_id" required>
        <option value="">Select Student</option>
        <?php
        $students = $conn->query("SELECT * FROM students");
        while($s = $students->fetch_assoc()) {
            echo "<option value='".$s['student_id']."'>".$s['name']."</option>";
        }
        ?>
    </select>

    <label>Subject:</label>
    <select name="subject_id" required>
        <option value="">Select Subject</option>
        <?php
        $subjects = $conn->query("SELECT * FROM subjects");
        while($sub = $subjects->fetch_assoc()) {
            echo "<option value='".$sub['subject_id']."'>".$sub['subject_name']."</option>";
        }
        ?>
    </select>

    <label>Marks:</label>
    <input type="number" name="marks" required>

    <button type="submit">Add Result</button>
</form>

<h3>Existing Results</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Student</th>
        <th>Subject</th>
        <th>Marks</th>
    </tr>
    <?php
    $sql = "SELECT results.*, students.name as student_name, subjects.subject_name 
            FROM results 
            LEFT JOIN students ON results.student_id = students.student_id 
            LEFT JOIN subjects ON results.subject_id = subjects.subject_id 
            WHERE exam_id=$exam_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['result_id']."</td>
                    <td>".$row['student_name']."</td>
                    <td>".$row['subject_name']."</td>
                    <td>".$row['marks']."</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No results found</td></tr>";
    }
    ?>
</table>
<br>
<a href="exams.php">Back to Exams</a>

<?php include 'footer.php'; ?>

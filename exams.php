<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

// Handle Add Exam - secure with prepared statement
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $exam_name = $_POST['exam_name'];
    $exam_date = $_POST['exam_date'];

    $stmt = $conn->prepare("INSERT INTO exams (exam_name, exam_date) VALUES (?, ?)");
    $stmt->bind_param("ss", $exam_name, $exam_date);
    
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: exams.php");
        exit();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
        $stmt->close();
    }
}

// Handle Delete - secure with prepared statement
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM exams WHERE exam_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: exams.php");
    exit();
}
?>

<h2>Exams Management</h2>
<a href="index.php">Back to Dashboard</a>

<h3>Add New Exam</h3>
<form method="post" action="">
    <label>Exam Name:</label>
    <input type="text" name="exam_name" required>
    <label>Exam Date:</label>
    <input type="date" name="exam_date" required>
    <button type="submit">Add Exam</button>
</form>

<h3>Existing Exams</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Exam Name</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php
    $sql = "SELECT * FROM exams";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['exam_id']."</td>
                    <td>".$row['exam_name']."</td>
                    <td>".$row['exam_date']."</td>
                    <td>
                        <a href='results.php?exam_id=".$row['exam_id']."'>Manage Results</a> | 
                        <a href='exams.php?delete=".$row['exam_id']."' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No exams found</td></tr>";
    }
    ?>
</table>

<?php include 'footer.php'; ?>

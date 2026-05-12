<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

// Secure deletion with prepared statement
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: subjects.php");
    exit();
}
?>

<h2>Subjects Management</h2>
<a href="index.php">Back to Dashboard</a>
<br>
<a href="add_subject.php"><button style="width: auto;">Add New Subject</button></a>

<table>
    <tr>
        <th>ID</th>
        <th>Subject Name</th>
        <th>Code</th>
        <th>Class</th>
        <th>Teacher</th>
        <th>Actions</th>
    </tr>
    <?php
    $sql = "SELECT subjects.*, classes.class_name, teachers.name as teacher_name 
            FROM subjects 
            LEFT JOIN classes ON subjects.class_id = classes.class_id 
            LEFT JOIN teachers ON subjects.teacher_id = teachers.teacher_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['subject_id']."</td>
                    <td>".$row['subject_name']."</td>
                    <td>".$row['subject_code']."</td>
                    <td>".$row['class_name']."</td>
                    <td>".$row['teacher_name']."</td>
                    <td>
                        <a href='subjects.php?delete=".$row['subject_id']."' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No subjects found</td></tr>";
    }
    ?>
</table>

<?php include 'footer.php'; ?>

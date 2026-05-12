<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

// Secure deletion with prepared statement
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM teachers WHERE teacher_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: teachers.php");
    exit();
}
?>

<h2>Teachers Management</h2>
<a href="index.php">Back to Dashboard</a>
<br>
<a href="add_teacher.php"><button style="width: auto;">Add New Teacher</button></a>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Speciality</th>
        <th>Contact</th>
        <th>Actions</th>
    </tr>
    <?php
    $sql = "SELECT * FROM teachers";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['teacher_id']."</td>
                    <td>".$row['name']."</td>
                    <td>".$row['subject_speciality']."</td>
                    <td>".$row['contact_details']."</td>
                    <td>
                        <a href='edit_teacher.php?id=".$row['teacher_id']."'>Edit</a> | 
                        <a href='teachers.php?delete=".$row['teacher_id']."' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No teachers found</td></tr>";
    }
    ?>
</table>

<?php include 'footer.php'; ?>

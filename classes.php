<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM classes WHERE class_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: classes.php");
    exit();
}
?>

<h2>Classes Management</h2>
<a href="index.php">Back to Dashboard</a>
<br>
<a href="add_class.php"><button style="width: auto;">Add New Class</button></a>

<table>
    <tr>
        <th>ID</th>
        <th>Class Name</th>
        <th>Actions</th>
    </tr>
    <?php
    $sql = "SELECT * FROM classes";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['class_id']."</td>
                    <td>".$row['class_name']."</td>
                    <td>
                        <a href='manage_sections.php?class_id=".$row['class_id']."'>Manage Sections</a> | 
                        <a href='classes.php?delete=".$row['class_id']."' onclick='return confirm(\"Are you sure? This will delete all sections in this class!\")'>Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No classes found</td></tr>";
    }
    ?>
</table>

<?php include 'footer.php'; ?>

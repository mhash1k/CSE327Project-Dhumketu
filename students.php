<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

// Handle Deletion - secure with prepared statement
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: students.php");
    exit();
}
?>

<h2>Students Management</h2>
<a href="index.php">Back to Dashboard</a>
<?php
/*
 * SQL Feature: COUNT() function
 * Displays total number of students
 */
$count_result = $conn->query("SELECT COUNT(*) as total FROM students");
$total_students = $count_result->fetch_assoc()['total'];
?>
<p><strong>Total Students (COUNT): <?php echo $total_students; ?></strong></p>
<a href="add_student.php"><button style="width: auto;">Add New Student</button></a>

<!-- SQL Feature: LIKE pattern matching for search -->
<form method="get" action="" style="margin: 15px 0;">
    <label>Search by name:</label>
    <input type="text" name="search" placeholder="Enter student name..." value="<?php echo $_GET['search'] ?? ''; ?>">
    <button type="submit">Search</button>
    <?php if(isset($_GET['search'])): ?>
        <a href="students.php"><button type="button">Clear</button></a>
    <?php endif; ?>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Roll No</th>
        <th>Class</th>
        <th>Section</th>
        <th>Guardian</th>
        <th>Contact</th>
        <th>Actions</th>
    </tr>
    <?php
    /*
     * SQL Features: LEFT JOIN, LIKE pattern matching
     * If search term provided, use LIKE for pattern matching
     */
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $stmt = $conn->prepare("SELECT students.*, classes.class_name, sections.section_name 
                FROM students 
                LEFT JOIN classes ON students.class_id = classes.class_id 
                LEFT JOIN sections ON students.section_id = sections.section_id 
                WHERE students.name LIKE ?");
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT students.*, classes.class_name, sections.section_name 
                FROM students 
                LEFT JOIN classes ON students.class_id = classes.class_id 
                LEFT JOIN sections ON students.section_id = sections.section_id";
        $result = $conn->query($sql);
    }

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['student_id']."</td>
                    <td>".$row['name']."</td>
                    <td>".$row['roll_number']."</td>
                    <td>".$row['class_name']."</td>
                    <td>".$row['section_name']."</td>
                    <td>".$row['guardian_name']."</td>
                    <td>".$row['contact_details']."</td>
                    <td>
                        <a href='edit_student.php?id=".$row['student_id']."'>Edit</a> | 
                        <a href='students.php?delete=".$row['student_id']."' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No students found</td></tr>";
    }
    ?>
</table>

<?php include 'footer.php'; ?>

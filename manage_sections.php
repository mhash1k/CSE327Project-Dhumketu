<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

$class_id = (int)$_GET['class_id'];
$stmt = $conn->prepare("SELECT * FROM classes WHERE class_id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class_result = $stmt->get_result();
$class = $class_result->fetch_assoc();
$stmt->close();

// Handle Add Section
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $section_name = $_POST['section_name'];
    $class_teacher_id = !empty($_POST['class_teacher_id']) ? (int)$_POST['class_teacher_id'] : NULL;

    $stmt = $conn->prepare("INSERT INTO sections (class_id, section_name, class_teacher_id) 
                            VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $class_id, $section_name, $class_teacher_id);

    if ($stmt->execute()) {
        echo "<p class='success'>Section added successfully</p>";
        $stmt->close();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
        $stmt->close();
    }
}

// Handle Delete Section
if (isset($_GET['delete_section'])) {
    $sec_id = (int)$_GET['delete_section'];
    $stmt = $conn->prepare("DELETE FROM sections WHERE section_id = ?");
    $stmt->bind_param("i", $sec_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_sections.php?class_id=$class_id");
    exit();
}
?>

<h2>Manage Sections for Class: <?php echo $class['class_name']; ?></h2>

<h3>Add New Section</h3>
<form method="post" action="">
    <label>Section Name:</label>
    <input type="text" name="section_name" required>

    <label>Class Teacher:</label>
    <select name="class_teacher_id">
        <option value="">Select Teacher</option>
        <?php
        $teachers = $conn->query("SELECT * FROM teachers");
        while($t = $teachers->fetch_assoc()) {
            echo "<option value='".$t['teacher_id']."'>".$t['name']."</option>";
        }
        ?>
    </select>
    <button type="submit">Add Section</button>
</form>

<h3>Existing Sections</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Section Name</th>
        <th>Class Teacher</th>
        <th>Actions</th>
    </tr>
    <?php
    /*
     * SELECT sections for this class with LEFT JOIN to teachers
     */
    $stmt = $conn->prepare("SELECT sections.*, teachers.name as teacher_name 
            FROM sections 
            LEFT JOIN teachers ON sections.class_teacher_id = teachers.teacher_id 
            WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['section_id']."</td>
                    <td>".$row['section_name']."</td>
                    <td>".$row['teacher_name']."</td>
                    <td>
                        <a href='manage_sections.php?class_id=$class_id&delete_section=".$row['section_id']."' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No sections found</td></tr>";
    }
    ?>
</table>
<br>
<a href="classes.php">Back to Classes</a>

<?php include 'footer.php'; ?>

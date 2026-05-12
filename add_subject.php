<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_name = $_POST['subject_name'];
    $subject_code = $_POST['subject_code'];
    $class_id = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : NULL;
    $teacher_id = !empty($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : NULL;

    /*
     * Insert subject using prepared statement
     */
    $stmt = $conn->prepare("INSERT INTO subjects (subject_name, subject_code, class_id, teacher_id) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $subject_name, $subject_code, $class_id, $teacher_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: subjects.php");
        exit();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
        $stmt->close();
    }
}
?>

<h2>Add Subject</h2>
<form method="post" action="">
    <label>Subject Name:</label>
    <input type="text" name="subject_name" required>

    <label>Subject Code:</label>
    <input type="text" name="subject_code">

    <label>Class:</label>
    <select name="class_id">
        <option value="">Select Class</option>
        <?php
        $classes = $conn->query("SELECT * FROM classes");
        while($c = $classes->fetch_assoc()) {
            echo "<option value='".$c['class_id']."'>".$c['class_name']."</option>";
        }
        ?>
    </select>

    <label>Teacher:</label>
    <select name="teacher_id">
        <option value="">Select Teacher</option>
        <?php
        $teachers = $conn->query("SELECT * FROM teachers");
        while($t = $teachers->fetch_assoc()) {
            echo "<option value='".$t['teacher_id']."'>".$t['name']."</option>";
        }
        ?>
    </select>

    <button type="submit">Add Subject</button>
</form>
<a href="subjects.php">Back to Subjects</a>

<?php include 'footer.php'; ?>

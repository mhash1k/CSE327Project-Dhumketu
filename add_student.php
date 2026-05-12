<?php
include 'check_login.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $class_id = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : NULL;
    $section_id = !empty($_POST['section_id']) ? (int)$_POST['section_id'] : NULL;
    $roll_number = !empty($_POST['roll_number']) ? (int)$_POST['roll_number'] : NULL;
    $guardian_name = $_POST['guardian_name'];
    $guardian_phone = $_POST['guardian_phone'];
    $contact_details = $_POST['contact_details'];

    /*
     * Insert new student using prepared statement
     * Prevents SQL injection with parameter binding
     */
    $stmt = $conn->prepare("INSERT INTO students (name, class_id, section_id, roll_number, guardian_name, guardian_phone, contact_details) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiisss", $name, $class_id, $section_id, $roll_number, $guardian_name, $guardian_phone, $contact_details);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: students.php");
        exit();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
        $stmt->close();
    }
}
?>

<h2>Add Student</h2>
<form method="post" action="">
    <label>Name:</label>
    <input type="text" name="name" required>
    
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

    <label>Section:</label>
    <select name="section_id">
        <option value="">Select Section</option>
        <?php
        $sections = $conn->query("SELECT * FROM sections");
        while($s = $sections->fetch_assoc()) {
            echo "<option value='".$s['section_id']."'>".$s['section_name']."</option>";
        }
        ?>
    </select>

    <label>Roll Number:</label>
    <input type="number" name="roll_number">

    <label>Guardian Name:</label>
    <input type="text" name="guardian_name">

    <label>Guardian Phone:</label>
    <input type="text" name="guardian_phone">

    <label>Contact Details:</label>
    <input type="text" name="contact_details">

    <button type="submit">Add Student</button>
</form>
<a href="students.php">Back to Students</a>

<?php include 'footer.php'; ?>

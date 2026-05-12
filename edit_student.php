<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $class_id = (int)$_POST['class_id'];
    $section_id = (int)$_POST['section_id'];
    $roll_number = (int)$_POST['roll_number'];
    $guardian_name = $_POST['guardian_name'];
    $guardian_phone = $_POST['guardian_phone'];
    $contact_details = $_POST['contact_details'];

    /*
     * Update student record using prepared statement
     * Prevents SQL injection through parameter binding
     */
    $stmt = $conn->prepare("UPDATE students SET 
            name = ?, 
            class_id = ?, 
            section_id = ?, 
            roll_number = ?, 
            guardian_name = ?, 
            guardian_phone = ?, 
            contact_details = ? 
            WHERE student_id = ?");
    $stmt->bind_param("siiisssi", $name, $class_id, $section_id, $roll_number, $guardian_name, $guardian_phone, $contact_details, $id);

    if ($stmt->execute()) {
        echo "<p class='success'>Student updated successfully</p>";
        $stmt->close();
        
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
        $stmt->close();
    }
}
?>

<h2>Edit Student</h2>
<form method="post" action="">
    <label>Name:</label>
    <input type="text" name="name" value="<?php echo $student['name']; ?>" required>
    
    <label>Class:</label>
    <select name="class_id" required>
        <option value="">Select Class</option>
        <?php
        $classes = $conn->query("SELECT * FROM classes");
        while($c = $classes->fetch_assoc()) {
            $selected = ($c['class_id'] == $student['class_id']) ? "selected" : "";
            echo "<option value='".$c['class_id']."' $selected>".$c['class_name']."</option>";
        }
        ?>
    </select>

    <label>Section:</label>
    <select name="section_id">
        <option value="">Select Section</option>
        <?php
        $sections = $conn->query("SELECT * FROM sections");
        while($s = $sections->fetch_assoc()) {
            $selected = ($s['section_id'] == $student['section_id']) ? "selected" : "";
            echo "<option value='".$s['section_id']."' $selected>".$s['section_name']."</option>";
        }
        ?>
    </select>

    <label>Roll Number:</label>
    <input type="number" name="roll_number" value="<?php echo $student['roll_number']; ?>">

    <label>Guardian Name:</label>
    <input type="text" name="guardian_name" value="<?php echo $student['guardian_name']; ?>">

    <label>Guardian Phone:</label>
    <input type="text" name="guardian_phone" value="<?php echo $student['guardian_phone']; ?>">

    <label>Contact Details:</label>
    <input type="text" name="contact_details" value="<?php echo $student['contact_details']; ?>">

    <button type="submit">Update Student</button>
</form>
<a href="students.php">Back to Students</a>

<?php include 'footer.php'; ?>

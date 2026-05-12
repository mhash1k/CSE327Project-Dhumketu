<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $subject_speciality = $_POST['subject_speciality'];
    $contact_details = $_POST['contact_details'];

    /*
     * Update teacher using prepared statement
     */
    $stmt = $conn->prepare("UPDATE teachers SET 
            name = ?, 
            subject_speciality = ?, 
            contact_details = ? 
            WHERE teacher_id = ?");
    $stmt->bind_param("sssi", $name, $subject_speciality, $contact_details, $id);

    if ($stmt->execute()) {
        echo "<p class='success'>Teacher updated successfully</p>";
        $stmt->close();
        
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $teacher = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
        $stmt->close();
    }
}
?>

<h2>Edit Teacher</h2>
<form method="post" action="">
    <label>Name:</label>
    <input type="text" name="name" value="<?php echo $teacher['name']; ?>" required>
    
    <label>Subject Speciality:</label>
    <input type="text" name="subject_speciality" value="<?php echo $teacher['subject_speciality']; ?>">

    <label>Contact Details:</label>
    <input type="text" name="contact_details" value="<?php echo $teacher['contact_details']; ?>">

    <button type="submit">Update Teacher</button>
</form>
<a href="teachers.php">Back to Teachers</a>

<?php include 'footer.php'; ?>

<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $subject_speciality = $_POST['subject_speciality'];
    $contact_details = $_POST['contact_details'];

    /*
     * Insert teacher using prepared statement
     * Prevents SQL injection through parameter binding
     */
    $stmt = $conn->prepare("INSERT INTO teachers (name, subject_speciality, contact_details) 
                            VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $subject_speciality, $contact_details);

    if ($stmt->execute()) {
        echo "<p class='success'>New teacher created successfully</p>";
        $stmt->close();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
        $stmt->close();
    }
}
?>

<h2>Add Teacher</h2>
<form method="post" action="">
    <label>Name:</label>
    <input type="text" name="name" required>
    
    <label>Subject Speciality:</label>
    <input type="text" name="subject_speciality">

    <label>Contact Details:</label>
    <input type="text" name="contact_details">

    <button type="submit">Add Teacher</button>
</form>
<a href="teachers.php">Back to Teachers</a>

<?php include 'footer.php'; ?>

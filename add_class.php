<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = $_POST['class_name'];

    $stmt = $conn->prepare("INSERT INTO classes (class_name) VALUES (?)");
    $stmt->bind_param("s", $class_name);

    if ($stmt->execute()) {
        echo "<p class='success'>New class created successfully</p>";
        $stmt->close();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
        $stmt->close();
    }
}
?>

<h2>Add Class</h2>
<form method="post" action="">
    <label>Class Name:</label>
    <input type="text" name="class_name" required>
    <button type="submit">Add Class</button>
</form>
<a href="classes.php">Back to Classes</a>

<?php include 'footer.php'; ?>

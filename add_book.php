<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

// Only Admin or Teacher can add books
if ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Teacher') {
    header("Location: books.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $class_id = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : NULL;

    $stmt = $conn->prepare("INSERT INTO books (title, author, class_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $author, $class_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: books.php?msg=Book added");
        exit();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
        $stmt->close();
    }
}
?>

<h2>Add Book</h2>
<form method="post" action="">
    <label>Title:</label>
    <input type="text" name="title" required>

    <label>Author:</label>
    <input type="text" name="author" required>

    <label>Class:</label>
    <select name="class_id">
        <option value="">Select Class</option>
        <?php
        $classes = $conn->query("SELECT * FROM classes");
        while($c = $classes->fetch_assoc()) {
            echo "<option value='{$c['class_id']}'>{$c['class_name']}</option>";
        }
        ?>
    </select>

    <button type="submit">Add Book</button>
</form>
<a href="books.php">Back to Book List</a>

<?php include 'footer.php'; ?>
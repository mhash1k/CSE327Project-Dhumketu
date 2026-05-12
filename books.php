<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

$sql = "SELECT b.book_id, b.title, b.author, c.class_name 
        FROM books b 
        LEFT JOIN classes c ON b.class_id = c.class_id";
$params = [];
$types = "";

if ($class_id > 0) {
    $sql .= " WHERE b.class_id = ?";
    $params[] = $class_id;
    $types .= "i";
}

// Add ordering
$sql .= " ORDER BY c.class_name, b.title";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Book List</h2>
<a href="index.php">Back to Dashboard</a>

<?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Teacher'): ?>
    <br><a href="add_book.php"><button style="width: auto;">Add New Book</button></a>
<?php endif; ?>

<!-- Class Filter -->
<form method="get" action="">
    <label>Filter by Class:</label>
    <select name="class_id" onchange="this.form.submit()">
        <option value="0">All Classes</option>
        <?php
        $classes = $conn->query("SELECT * FROM classes ORDER BY class_name");
        while($c = $classes->fetch_assoc()) {
            $selected = ($c['class_id'] == $class_id) ? "selected" : "";
            echo "<option value='{$c['class_id']}' $selected>{$c['class_name']}</option>";
        }
        ?>
    </select>
</form>

<table>
    <tr><th>ID</th><th>Title</th><th>Author</th><th>Class</th></tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['book_id']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['author']) ?></td>
                <td><?= htmlspecialchars($row['class_name']) ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4">No books found</td></tr>
    <?php endif; ?>
</table>

<?php
$stmt->close();
include 'footer.php';
?>
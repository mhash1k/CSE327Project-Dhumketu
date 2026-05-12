<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

// Handle Add Fee - secure with prepared statement
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = (int)$_POST['student_id'];
    $amount = (float)$_POST['amount'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO fees (student_id, amount, due_date, status) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $student_id, $amount, $due_date, $status);

    if ($stmt->execute()) {
        echo "<p class='success'>Fee record added successfully</p>";
        $stmt->close();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
        $stmt->close();
    }
}

// Handle Delete - secure with prepared statement
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM fees WHERE fee_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: fees.php");
    exit();
}
?>

<h2>Fees Management</h2>

<a href="index.php">Back to Dashboard</a>
<br>

<h3>Add Fee Record</h3>
<form method="post" action="">
    <label>Student:</label>
    <select name="student_id" required>
        <option value="">Select Student</option>
        <?php
        $students = $conn->query("SELECT * FROM students");
        while($s = $students->fetch_assoc()) {
            echo "<option value='".$s['student_id']."'>".$s['name']."</option>";
        }
        ?>
    </select>

    <label>Amount:</label>
    <input type="number" step="0.01" name="amount" required>

    <label>Due Date:</label>
    <input type="date" name="due_date" required>

    <label>Status:</label>
    <select name="status">
        <option value="Unpaid">Unpaid</option>
        <option value="Paid">Paid</option>
    </select>

    <button type="submit">Add Fee</button>
</form>

<h3>Fee Records</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Student</th>
        <th>Amount</th>
        <th>Due Date</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php
    $sql = "SELECT fees.*, students.name as student_name 
            FROM fees 
            LEFT JOIN students ON fees.student_id = students.student_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['fee_id']."</td>
                    <td>".$row['student_name']."</td>
                    <td>".$row['amount']."</td>
                    <td>".$row['due_date']."</td>
                    <td>".$row['status']."</td>
                    <td>
                        <a href='fees.php?delete=".$row['fee_id']."' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No fee records found</td></tr>";
    }
    ?>
</table>

<?php include 'footer.php'; ?>

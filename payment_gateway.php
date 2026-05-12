<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

$fee_id = isset($_GET['fee_id']) ? (int)$_GET['fee_id'] : 0;
if (!$fee_id) {
    header("Location: fees.php");
    exit();
}

// Fetch fee details for display
$stmt = $conn->prepare("SELECT fees.*, students.name FROM fees JOIN students ON fees.student_id = students.student_id WHERE fee_id = ?");
$stmt->bind_param("i", $fee_id);
$stmt->execute();
$fee = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$fee || $fee['status'] != 'Unpaid') {
    echo "<p>Invalid or already paid fee.</p>";
    include 'footer.php';
    exit();
}
?>

<h2>Mock Payment Gateway</h2>
<p>Student: <?= htmlspecialchars($fee['name']) ?></p>
<p>Amount: <?= htmlspecialchars($fee['amount']) ?> BDT</p>

<form method="post" action="process_payment.php">
    <input type="hidden" name="fee_id" value="<?= $fee_id ?>">
    <label>Card Number (dummy):</label>
    <input type="text" name="card_number" placeholder="1234-5678-9012-3456" required>
    <label>Expiry:</label>
    <input type="text" name="expiry" placeholder="MM/YY" required>
    <label>CVV:</label>
    <input type="text" name="cvv" placeholder="123" required>
    <button type="submit">Pay Now (Simulate)</button>
</form>
<a href="fees.php">Back to Fees</a>

<?php include 'footer.php'; ?>
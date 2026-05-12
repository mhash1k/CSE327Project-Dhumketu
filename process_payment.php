<?php
include 'check_login.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fee_id = (int)$_POST['fee_id'];

    // Simulate 90% success
    $success = (mt_rand(1, 10) <= 9);

    if ($success) {
        $payment_method = 'Card';
        $transaction_id = 'TXN' . time() . rand(1000,9999);

        $stmt = $conn->prepare("UPDATE fees SET status='Paid', payment_method=?, transaction_id=? WHERE fee_id=?");
        $stmt->bind_param("ssi", $payment_method, $transaction_id, $fee_id);
        $stmt->execute();
        $stmt->close();

        header("Location: fees.php?msg=Payment+successful&trx=" . urlencode($transaction_id));
        exit();
    } else {
        header("Location: fees.php?msg=Payment+failed,+try+again");
        exit();
    }
} else {
    header("Location: fees.php");
}
?>
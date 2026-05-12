<?php
include 'db.php';

$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "UPDATE users SET password = '$hashed_password' WHERE username = 'admin'";

if ($conn->query($sql) === TRUE) {
    echo "Admin password reset successfully to 'admin123'. Hash: " . $hashed_password;
} else {
    echo "Error updating record: " . $conn->error;
}
?>

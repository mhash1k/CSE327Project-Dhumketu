<?php
$servername = "localhost";
$username = "root";
$password = ""; // Updated with user provided password
$dbname = "simple_school_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
$host = "localhost";
$user = "root"; // Replace with your MySQL username
$pass = "";     // Replace with your MySQL password
$db = "peace_electricals";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define currency settings
define('CURRENCY_SYMBOL', '₨'); // Pakistani Rupee symbol
define('CURRENCY_CODE', 'PKR');
?>
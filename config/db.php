<?php
$conn = new mysqli("localhost", "root", "", "jai_jalaram");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
date_default_timezone_set("Asia/Kolkata");
?>
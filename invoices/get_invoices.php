<?php
include("../config/db.php");

$user_id = 1;

$result = $conn->query("SELECT * FROM invoices WHERE user_id='$user_id'");

echo json_encode($result->fetch_all(MYSQLI_ASSOC));
?>
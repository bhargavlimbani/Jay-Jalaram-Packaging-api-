<?php
include("../config/db.php");

header("Content-Type: application/json");

// 🔥 FETCH ALL INVOICES (ADMIN)
$result = $conn->query("SELECT * FROM invoices ORDER BY id DESC");

$invoices = [];

while ($row = $result->fetch_assoc()) {
    $invoices[] = $row;
}

echo json_encode($invoices);
?>

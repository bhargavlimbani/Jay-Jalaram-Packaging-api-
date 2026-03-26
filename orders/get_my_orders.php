<?php
include("../config/db.php");

// ✅ TIME FIX
date_default_timezone_set("Asia/Kolkata");

header("Content-Type: application/json");

$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
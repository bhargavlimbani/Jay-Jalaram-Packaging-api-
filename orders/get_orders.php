<?php
include("../config/db.php");

header("Content-Type: application/json");

// GET JSON
$data = json_decode(file_get_contents("php://input"), true);

$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode([]);
    exit;
}

// 🔥 FILTER BY USER
$result = $conn->query("SELECT * FROM orders WHERE user_id='$user_id' ORDER BY id DESC");

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
?>
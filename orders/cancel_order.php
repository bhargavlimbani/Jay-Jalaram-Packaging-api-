<?php
include("../config/db.php");

header("Content-Type: application/json");

// GET JSON
$data = json_decode(file_get_contents("php://input"), true);

$order_id = isset($data['order_id']) ? intval($data['order_id']) : 0;

if ($order_id <= 0) {
    echo json_encode(["status"=>"error","message"=>"Invalid ID"]);
    exit;
}

// CHECK ORDER
$res = $conn->query("SELECT status FROM orders WHERE id='$order_id'");
$row = $res->fetch_assoc();

if (!$row) {
    echo json_encode(["status"=>"error","message"=>"Order not found"]);
    exit;
}

// ❌ Only allow delete if Pending
if ($row['status'] != "Pending") {
    echo json_encode(["status"=>"error","message"=>"Cannot delete processed order"]);
    exit;
}

// 🔥 DELETE ORDER
$conn->query("DELETE FROM orders WHERE id='$order_id'");

echo json_encode([
    "status"=>"success",
    "message"=>"Order deleted successfully"
]);
?>
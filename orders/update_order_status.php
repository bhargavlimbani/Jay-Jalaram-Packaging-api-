<?php
include("../config/db.php");

$data = json_decode(file_get_contents("php://input"));

$order_id = intval($data->order_id);
$status = $data->status;

$conn->query("UPDATE orders SET status='$status' WHERE id='$order_id'");

echo json_encode([
  "status" => "success",
  "message" => "Order updated"
]);
?>
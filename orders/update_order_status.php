<?php
include("../config/db.php");

$order_id = $_POST['order_id'] ?? 0;
$status = $_POST['status'] ?? '';

file_put_contents("order_debug.txt", json_encode($_POST)); // 🔥 DEBUG

if ($order_id <= 0 || $status == "") {
    echo json_encode(["status"=>"error","message"=>"Invalid data"]);
    exit;
}

$sql = "UPDATE orders SET status='$status' WHERE id='$order_id'";

if ($conn->query($sql)) {
    echo json_encode(["status"=>"success","message"=>"Updated"]);
} else {
    echo json_encode(["status"=>"error","message"=>$conn->error]);
}
?>
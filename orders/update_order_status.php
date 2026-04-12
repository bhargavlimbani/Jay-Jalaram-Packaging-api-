<?php
include("../config/db.php");

$order_id = $_POST['order_id'] ?? 0;
$status = $_POST['status'] ?? '';
$comment = $_POST['comment'] ?? '';

if ($order_id <= 0) {
    echo json_encode(["status"=>"error"]);
    exit;
}

$sql = "UPDATE orders 
SET status='$status', admin_comment='$comment' 
WHERE id='$order_id'";

if ($conn->query($sql)) {
    echo json_encode(["status"=>"success","message"=>"Updated"]);
} else {
    echo json_encode(["status"=>"error","message"=>$conn->error]);
}
?>
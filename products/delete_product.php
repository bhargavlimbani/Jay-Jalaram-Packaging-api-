<?php
header("Content-Type: application/json");
include("../config/db.php");

$id = $_POST['id'] ?? 0;

if ($id <= 0) {
    echo json_encode(["status"=>"error","message"=>"Invalid ID"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM products WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Product Deleted"]);
} else {
    echo json_encode(["status"=>"error","message"=>$stmt->error]);
}
?>
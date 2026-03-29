<?php
header("Content-Type: application/json");
include("../config/db.php");

$id = $_GET['id'] ?? 0;

if ($id <= 0) {
    echo json_encode(["status"=>"error","message"=>"Invalid ID"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["status"=>"success","customer"=>$row]);
} else {
    echo json_encode(["status"=>"error","message"=>"Not found"]);
}
?>
<?php
header("Content-Type: application/json");
include("../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? 0;
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$phone = $data['phone'] ?? '';
$address = $data['address'] ?? '';

if ($id <= 0) {
    echo json_encode(["status"=>"error","message"=>"Invalid ID"]);
    exit;
}

$stmt = $conn->prepare("
UPDATE users SET name=?, email=?, phone=?, address=? WHERE id=?
");

$stmt->bind_param("ssssi", $name, $email, $phone, $address, $id);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Customer Updated"]);
} else {
    echo json_encode(["status"=>"error","message"=>$stmt->error]);
}
?>
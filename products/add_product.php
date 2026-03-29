<?php
header("Content-Type: application/json");
include("../config/db.php");

$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? '';
$box_type = $_POST['box_type'] ?? '';
$description = $_POST['description'] ?? '';
$stock = $_POST['stock'] ?? 0;

if (!$name || !$price || !$box_type) {
    echo json_encode(["status"=>"error","message"=>"Missing fields"]);
    exit;
}

// IMAGE
$imageData = "";
if (isset($_FILES['image'])) {
    $file = $_FILES['image']['tmp_name'];
    $imageData = base64_encode(file_get_contents($file));
}

// DATE
$now = date("Y-m-d H:i:s");

$stmt = $conn->prepare("
INSERT INTO products 
(name, box_type, description, image_data, price, stock, createdAt, updatedAt) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssdiss",
    $name,
    $box_type,
    $description,
    $imageData,
    $price,
    $stock,
    $now,
    $now
);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Product added"]);
} else {
    echo json_encode(["status"=>"error","message"=>$stmt->error]);
}
?>
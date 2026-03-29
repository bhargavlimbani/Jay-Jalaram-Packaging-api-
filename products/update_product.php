<?php
header("Content-Type: application/json");
include("../config/db.php");

$id = $_POST['id'];
$name = $_POST['name'];
$price = $_POST['price'];
$box_type = $_POST['box_type'];
$description = $_POST['description'];
$stock = $_POST['stock'];

$imageData = null;

if (isset($_FILES['image'])) {
    $imageData = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
}

$now = date("Y-m-d H:i:s");

if ($imageData != null) {
    $stmt = $conn->prepare("
    UPDATE products SET 
    name=?, box_type=?, description=?, image_data=?, price=?, stock=?, updatedAt=? 
    WHERE id=?
    ");

    $stmt->bind_param("ssssdssi",
        $name,
        $box_type,
        $description,
        $imageData,
        $price,
        $stock,
        $now,
        $id
    );
} else {
    $stmt = $conn->prepare("
    UPDATE products SET 
    name=?, box_type=?, description=?, price=?, stock=?, updatedAt=? 
    WHERE id=?
    ");

    $stmt->bind_param("sssdssi",
        $name,
        $box_type,
        $description,
        $price,
        $stock,
        $now,
        $id
    );
}

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Updated"]);
} else {
    echo json_encode(["status"=>"error","message"=>$stmt->error]);
}
?>
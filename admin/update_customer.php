<?php
include("../config/db.php");

$id = $_POST['id'];
$name = $_POST['name'];
$price = $_POST['price'];
$box_type = $_POST['box_type'];
$description = $_POST['description'];
$stock = $_POST['stock'];

$image_sql = "";

// 🔥 ONLY UPDATE IMAGE IF NEW IMAGE UPLOADED
if (isset($_FILES['image']) && $_FILES['image']['tmp_name'] != "") {
    $image_data = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
    $image_data = "data:image/jpeg;base64," . $image_data;

    $image_sql = ", image_data='$image_data'";
}

// 🔥 MAIN UPDATE
$sql = "UPDATE products SET 
name='$name',
price='$price',
box_type='$box_type',
description='$description',
stock='$stock'
$image_sql
WHERE id='$id'";

if ($conn->query($sql)) {
    echo json_encode(["status"=>"success","message"=>"Updated"]);
} else {
    echo json_encode(["status"=>"error","message"=>$conn->error]);
}
?>
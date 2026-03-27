<?php
header("Content-Type: application/json");
include("../config/db.php");

$sql = "SELECT id, name, email, phone, address FROM users";
$result = $conn->query($sql);

$customers = [];

while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

echo json_encode($customers);
?>
<?php
include("../config/db.php");

header("Content-Type: application/json");

// GET JSON
$data = json_decode(file_get_contents("php://input"), true);

$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode(["status"=>"error","data"=>[]]);
    exit;
}

// 🔥 IMPORTANT: include items + admin_comment
$result = $conn->query("
SELECT 
id,
total_price,
status,
items,
admin_comment,
order_type,
box_length,
box_width,
box_height
FROM orders 
WHERE user_id='$user_id'
ORDER BY id DESC
");

$orders = [];

while ($row = $result->fetch_assoc()) {

    // 🔥 DECODE ITEMS JSON
    if (!empty($row['items'])) {
        $row['items'] = json_decode($row['items'], true);
    } else {
        $row['items'] = [];
    }

    $orders[] = $row;
}

echo json_encode([
    "status"=>"success",
    "data"=>$orders
]);
?>
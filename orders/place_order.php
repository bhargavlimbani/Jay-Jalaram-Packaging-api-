<?php
include("../config/db.php");

// ✅ TIMEZONE FIX
date_default_timezone_set("Asia/Kolkata");

// GET JSON DATA
$data = json_decode(file_get_contents("php://input"));

// ❌ CHECK DATA
if (!$data || !isset($data->items)) {
    echo json_encode([
        "status" => "error",
        "message" => "No items received"
    ]);
    exit;
}

$items = $data->items;

// ✅ GET USER ID
$user_id = isset($data->user_id) ? intval($data->user_id) : 0;

if ($user_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid user"
    ]);
    exit;
}

// ✅ GET USER DETAILS
$user_result = $conn->query("SELECT name, phone FROM users WHERE id = '$user_id'");

if ($user_result->num_rows == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "User not found"
    ]);
    exit;
}

$user = $user_result->fetch_assoc();
$customer_name = $user['name'];
$customer_phone = $user['phone'];

// =========================
// ✅ PROCESS ITEMS
// =========================

$total = 0;
$total_qty = 0;
$new_items = [];

foreach ($items as $item) {

    $product_id = intval($item->product_id);
    $qty = intval($item->quantity);

    // 🔥 GET PRODUCT FROM DB
    $product_result = $conn->query("SELECT name, price, image_data FROM products WHERE id = '$product_id'");

    if ($product_result->num_rows == 0) {
        continue;
    }

    $product = $product_result->fetch_assoc();

    $price = floatval($product['price']);

    // CALCULATE TOTAL
    $total += $price * $qty;
    $total_qty += $qty;

    // ✅ STORE FULL ITEM DETAILS
    $new_items[] = [
        "product_id" => $product_id,
        "name" => $product['name'],
        "price" => $price,
        "quantity" => $qty,
        "image" => $product['image_data']
    ];
}

// ❌ IF NO VALID ITEMS
if (empty($new_items)) {
    echo json_encode([
        "status" => "error",
        "message" => "No valid products"
    ]);
    exit;
}

// ✅ CONVERT TO JSON
$items_json = json_encode($new_items);

// =========================
// ✅ INSERT ORDER
// =========================

$stmt = $conn->prepare("INSERT INTO orders 
(user_id, quantity, total_price, status, createdAt, order_type, items, customer_name, customer_phone)
VALUES 
(?, ?, ?, 'Pending', NOW(), 'product', ?, ?, ?)");

$stmt->bind_param(
    "iidsss",
    $user_id,
    $total_qty,
    $total,
    $items_json,
    $customer_name,
    $customer_phone
);

$stmt->execute();
$stmt->close();

// =========================
// ✅ RESPONSE
// =========================

echo json_encode([
    "status" => "success",
    "message" => "Order placed successfully"
]);
?>
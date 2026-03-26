<?php
include("../config/db.php");

$data = json_decode(file_get_contents("php://input"));

$order_id = $data->order_id;
$message = $data->message;

$res = $conn->query("SELECT chat_messages FROM orders WHERE id='$order_id'");
$row = $res->fetch_assoc();

$chat = [];

if ($row['chat_messages']) {
    $chat = json_decode($row['chat_messages'], true);
}

$chat[] = [
    "sender" => "customer",
    "message" => $message,
    "time" => date("Y-m-d H:i:s")
];

$conn->query("UPDATE orders SET chat_messages='".json_encode($chat)."' WHERE id='$order_id'");

echo json_encode(["messages" => $chat]);
?>
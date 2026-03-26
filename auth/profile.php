<?php
header("Content-Type: application/json");
include("../config/db.php");

$method = $_SERVER['REQUEST_METHOD'];

// GET profile: /auth/profile.php?user_id=1
if ($method === "GET") {
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

    if ($user_id <= 0) {
        echo json_encode(["status" => "error", "message" => "Missing or invalid user_id"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, name, email, phone, address FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(["status" => "success", "profile" => $row]);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }

    $stmt->close();
    exit;
}

// UPDATE profile: POST JSON
if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"));

    $user_id = isset($data->user_id) ? intval($data->user_id) : 0;
    $name = isset($data->name) ? trim($data->name) : "";
    $email = isset($data->email) ? trim($data->email) : "";
    $phone = isset($data->phone) ? trim($data->phone) : "";
    $address = isset($data->address) ? trim($data->address) : "";

    if ($user_id <= 0) {
        echo json_encode(["status" => "error", "message" => "Missing or invalid user_id"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $email, $phone, $address, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed"]);
    }

    $stmt->close();
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid request method"]);
?>

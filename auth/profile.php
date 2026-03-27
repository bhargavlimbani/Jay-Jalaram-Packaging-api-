<?php
header("Content-Type: application/json");
include("../config/db.php");

$method = $_SERVER['REQUEST_METHOD'];

// ================= GET PROFILE =================
if ($method === "GET") {

    if (!isset($_GET['user_id'])) {
        echo json_encode(["status" => "error", "message" => "user_id required"]);
        exit;
    }

    $user_id = intval($_GET['user_id']);

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

// ================= UPDATE PROFILE =================
if ($method === "POST") {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user_id'])) {
        echo json_encode(["status" => "error", "message" => "user_id required"]);
        exit;
    }

    $user_id = intval($data['user_id']);
    $name = $data['name'] ?? "";
    $email = $data['email'] ?? "";
    $phone = $data['phone'] ?? "";
    $address = $data['address'] ?? "";

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $email, $phone, $address, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed"]);
    }

    $stmt->close();
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid request"]);
?>
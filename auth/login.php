<?php
include("../config/db.php");

header("Content-Type: application/json; charset=utf-8");

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid JSON"]);
    exit;
}

$email = isset($data["email"]) ? trim($data["email"]) : "";
$password = isset($data["password"]) ? (string)$data["password"] : "";

if ($email === "" || $password === "") {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Email and password are required"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, name, email, phone, address, password, role, createdAt, updatedAt FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user["password"])) {
        unset($user["password"]);
        echo json_encode([
            "status" => "success",
            "user" => $user
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Wrong password"]);
    }
} else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "User not found"]);
}

$stmt->close();
?>

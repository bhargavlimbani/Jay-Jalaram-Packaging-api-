<?php
include("../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$otp = $data['otp'];

// GET RECORD
$result = $conn->query("SELECT * FROM pending_registrations WHERE email='$email'");

if ($result->num_rows == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "No registration found"
    ]);
    exit();
}

$row = $result->fetch_assoc();

// CHECK OTP EXPIRE
if (strtotime($row['otp_expires_at']) < time()) {
    echo json_encode([
        "status" => "error",
        "message" => "OTP expired"
    ]);
    exit();
}

// VERIFY OTP
if (!password_verify($otp, $row['otp_hash'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid OTP"
    ]);
    exit();
}

// INSERT INTO USERS
$conn->query("INSERT INTO users (name, email, phone, address, password, role)
VALUES (
    '{$row['name']}',
    '{$row['email']}',
    '{$row['phone']}',
    '{$row['address']}',
    '{$row['password_hash']}',
    'customer'
)");

// DELETE FROM PENDING
$conn->query("DELETE FROM pending_registrations WHERE email='$email'");

echo json_encode([
    "status" => "success",
    "message" => "Registration successful"
]);
?>
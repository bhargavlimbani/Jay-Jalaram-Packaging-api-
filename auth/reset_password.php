<?php
include("../config/db.php");

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'], $data['otp'], $data['password'])) {
    echo json_encode(["status"=>"error","message"=>"Missing fields"]);
    exit();
}

$email = $conn->real_escape_string($data['email']);
$otp = $data['otp'];
$password = password_hash($data['password'], PASSWORD_BCRYPT);

// GET USER
$result = $conn->query("SELECT * FROM users WHERE email='$email'");
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(["status"=>"error","message"=>"User not found"]);
    exit();
}

// VERIFY OTP
if (!password_verify($otp, $row['reset_password_otp_hash'])) {
    echo json_encode(["status"=>"error","message"=>"Invalid OTP"]);
    exit();
}

// CHECK EXPIRY
if (strtotime($row['reset_password_otp_expires']) < time()) {
    echo json_encode(["status"=>"error","message"=>"OTP expired"]);
    exit();
}

// UPDATE PASSWORD
$conn->query("UPDATE users SET 
password='$password',
reset_password_otp_hash=NULL,
reset_password_otp_expires=NULL 
WHERE email='$email'");

echo json_encode([
    "status"=>"success",
    "message"=>"Password updated successfully"
]);
?>
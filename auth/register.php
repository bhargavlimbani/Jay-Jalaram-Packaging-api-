<?php
include("../config/db.php");

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// GET JSON DATA
$data = json_decode(file_get_contents("php://input"), true);

$name = $conn->real_escape_string($data['name']);
$email = $conn->real_escape_string($data['email']);
$phone = $conn->real_escape_string($data['phone']);
$address = $conn->real_escape_string($data['address']);
$password = password_hash($data['password'], PASSWORD_BCRYPT);

// GENERATE OTP
$otp = rand(100000, 999999);
$otp_hash = password_hash($otp, PASSWORD_BCRYPT);

// 🔥 DELETE OLD RECORD (RESEND OTP LOGIC)
$conn->query("DELETE FROM pending_registrations WHERE email='$email'");

// INSERT NEW OTP DATA
$insert = $conn->query("INSERT INTO pending_registrations 
(name, email, phone, address, password_hash, otp_hash, otp_expires_at, createdAt)
VALUES 
('$name', '$email', '$phone', '$address', '$password', '$otp_hash', DATE_ADD(NOW(), INTERVAL 5 MINUTE), NOW())");

if (!$insert) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $conn->error
    ]);
    exit();
}

// 📧 SEND EMAIL
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'jayjalarampackaging1@gmail.com';
    $mail->Password = 'diebwycxhgslygvq';

    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // 🔕 REMOVE DEBUG IN PRODUCTION
    // $mail->SMTPDebug = 2;

    $mail->setFrom('jayjalarampackaging1@gmail.com', 'Jalaram Packaging');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';

    $mail->Body = "
        <h2>Jalaram Packaging</h2>
        <p>Your OTP is:</p>
        <h1 style='color:teal;'>$otp</h1>
        <p>This OTP will expire in 5 minutes.</p>
    ";

    $mail->send();

    echo json_encode([
        "status" => "success",
        "message" => "OTP sent to your email"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Email failed: " . $mail->ErrorInfo
    ]);
}
?>
<?php
header("Content-Type: application/json");

include("../config/db.php");

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'])) {
    echo json_encode(["status"=>"error","message"=>"Email required"]);
    exit();
}

$email = $conn->real_escape_string($data['email']);

// CHECK USER
$result = $conn->query("SELECT * FROM users WHERE email='$email'");
if ($result->num_rows == 0) {
    echo json_encode(["status"=>"error","message"=>"Email not found"]);
    exit();
}

// ===== OTP =====
$otp = rand(100000, 999999);
$otp_hash = password_hash($otp, PASSWORD_BCRYPT);

// ===== TOKEN =====
$token = bin2hex(random_bytes(32));
$expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

// SAVE BOTH
$conn->query("UPDATE users SET 
reset_password_otp_hash='$otp_hash',
reset_password_otp_expires=DATE_ADD(NOW(), INTERVAL 5 MINUTE),
reset_password_token='$token',
reset_password_expires='$expiry'
WHERE email='$email'");

// RESET LINK
$reset_link = "http://localhost:3000/reset-password?token=$token";

// SEND MAIL
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'jayjalarampackaging1@gmail.com';
    $mail->Password = 'diebwycxhgslygvq';

    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('jayjalarampackaging1@gmail.com', 'Jalaram Packaging');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Reset Password';

    $mail->Body = "
        <h2>Password Reset</h2>

        <p><b>Option 1: OTP</b></p>
        <h1 style='color:blue;'>$otp</h1>
        <p>Valid for 5 minutes</p>

        <hr>

        <p><b>Option 2: Reset Link</b></p>
        <a href='$reset_link' style='padding:10px;background:green;color:white;text-decoration:none;'>Reset Password</a>
        <p>Valid for 15 minutes</p>
    ";

    $mail->send();

    echo json_encode(["status"=>"success","message"=>"OTP & link sent"]);

} catch (Exception $e) {
    echo json_encode(["status"=>"error","message"=>$mail->ErrorInfo]);
}
?>
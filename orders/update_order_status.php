<?php
include("../config/db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$order_id = $_POST['order_id'];
$status = $_POST['status'];

// UPDATE STATUS
$conn->query("UPDATE orders SET status='$status' WHERE id='$order_id'");

// GET USER EMAIL
$result = $conn->query("
SELECT users.email, users.name, orders.total_price 
FROM orders 
JOIN users ON orders.user_id = users.id 
WHERE orders.id = '$order_id'
");

$data = $result->fetch_assoc();

$email = $data['email'];
$name = $data['name'];
$total = $data['total_price'];

// ================= EMAIL =================
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    $mail->Username = 'jayjalarampackaging1@gmail.com';
    $mail->Password = 'diebwycxhgslygvq';

    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('jayjalarampackaging1@gmail.com', 'Jay Jalaram Packaging');
    $mail->addAddress($email, $name);

    $mail->isHTML(true);
    $mail->Subject = "Order Status Updated";

    $mail->Body = "
        Hello $name,<br><br>
        Your order #$order_id status is <b>$status</b>.<br>
        Total: ₹$total<br><br>
        Thank you!
    ";

    $mail->send();

} catch (Exception $e) {
    // ignore email error
}

echo json_encode([
    "status"=>"success",
    "message"=>"Order updated"
]);
?>
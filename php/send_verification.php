<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = $_POST['email'] ?? '';
    if ($new_email) {
        // Generate 6-digit code
        $code = random_int(100000, 999999);

        // Update email, reset verification
        $stmt = $conn->prepare("UPDATE users SET email=?, is_verified=0, verification_token=? WHERE id=?");
        $stmt->bind_param('ssi', $new_email, $code, $user_id);
        $stmt->execute();

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com'; // your SMTP host
            $mail->SMTPAuth = true;
            $mail->Username = 'rentify.smtp@gmail.com';
            $mail->Password = 'zbku jgdf arct uavi';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('rentify.smtp@gmail.com', 'Rentify');
            $mail->addAddress($new_email);

            $mail->isHTML(true);
            $mail->Subject = 'Your Verification Code';
            $mail->Body = "Your verification code is: <strong>$code</strong>";

            $mail->send();
            $message = "Verification code sent to $new_email";
        } catch (Exception $e) {
            $message = "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "Please enter a valid email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Email Verification</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f4f4; display:flex; justify-content:center; align-items:center; height:100vh; }
.card { background:#fff; padding:30px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1); text-align:center; max-width:400px; width:100%; }
button { padding:10px 16px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer; margin-top:10px; }
button:hover { background:#0056b3; }
input { width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:5px; }
.message { margin-top:15px; color:#a00; }
</style>
</head>
<body>
<div class="card">
    <h2>Verify Your Email</h2>
    <form action="" method="POST">
        <label>Enter Email to Verify / Change:</label>
        <input type="email" name="email" required>
        <button type="submit">Send Verification Code</button>
    </form>
    <?php if($message): ?><p class="message"><?= $message ?></p><?php endif; ?>
    <a href="tenant_profile.php"><button>Back to Profile</button></a>
</div>
</body>
</html>

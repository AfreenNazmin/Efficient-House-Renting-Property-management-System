<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role     = trim($_POST['role']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status"=>"error","message"=>"Invalid email format"]);
        exit;
    }

    // Prevent duplicate registration
    $check = $conn->prepare("
        SELECT email FROM users WHERE email=? 
        UNION 
        SELECT email FROM pending_users WHERE email=?");
    $check->bind_param("ss", $email, $email);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(["status"=>"error","message"=>"Email already registered or pending verification"]);
        exit;
    }

    // Generate unique token
    $token = bin2hex(random_bytes(16));

    // Insert into pending_users
    $stmt = $conn->prepare("
        INSERT INTO pending_users (name, email, password, role, verification_token)
        VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $role, $token);

    if ($stmt->execute()) {
        // Send verification link via email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'rentify.smtp@gmail.com';
            $mail->Password = 'smud otml tuix epmw'; // Gmail app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('rentify.smtp@gmail.com', 'Rentify');
            $mail->addAddress($email, $name);

           $verify_link = "http://localhost/project/Efficient-House-Renting-Property-management-System/php/verify.php?token=" . urlencode($token);


            $mail->isHTML(true);
            $mail->Subject = "Verify your Rentify account";
            $mail->Body = "
                <h3>Hello, $name 👋</h3>
                <p>Thanks for signing up as a <b>$role</b>!</p>
                <p>Click below to verify your email and activate your account:</p>
                <p><a href='$verify_link' 
                      style='background:#5c67f2;color:white;padding:10px 15px;border-radius:6px;
                             text-decoration:none;'>Verify My Account</a></p>
                <p>If you didn’t sign up, you can ignore this email.</p>
                <br><p style='color:gray;'>– The Rentify Team</p>
            ";

            $mail->send();
            echo json_encode(["status"=>"success","message"=>"Verification link sent to your email."]);
        } catch (Exception $e) {
            echo json_encode(["status"=>"error","message"=>"Email send failed: ".$mail->ErrorInfo]);
        }
    } else {
        echo json_encode(["status"=>"error","message"=>"Database insert failed"]);
    }

    $stmt->close();
    $conn->close();
}
?>

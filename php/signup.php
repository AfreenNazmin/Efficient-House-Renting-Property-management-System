<?php
// Turn off errors to prevent JSON parse issues
ini_set('display_errors', 0);
error_reporting(0);

// Ensure JSON output
header('Content-Type: application/json');

// Start buffer to catch accidental output
ob_start();

require '../vendor/autoload.php';
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name          = trim($_POST['name']);
    $email         = trim($_POST['email']);
    $password      = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role          = trim($_POST['role']);
    $country_code  = trim($_POST['countryCode']);
    $phone         = trim($_POST['phone']);

    // Optional landlord-only fields
    $nidNumber = isset($_POST['nidNumber']) ? trim($_POST['nidNumber']) : null;
    $nidFront  = isset($_FILES['nidFront']) ? $_FILES['nidFront'] : null;
    $nidBack   = isset($_FILES['nidBack']) ? $_FILES['nidBack'] : null;

    // Validate email
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

    // Handle NID uploads (only for landlord)
    $nidFrontPath = null;
    $nidBackPath  = null;

    if ($role === 'landlord') {
        $uploadDir = "../uploads/nid/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        if ($nidFront && $nidFront['error'] === 0) {
            $ext = pathinfo($nidFront['name'], PATHINFO_EXTENSION);
            $nidFrontPath = $uploadDir . uniqid("front_") . "." . $ext;
            move_uploaded_file($nidFront['tmp_name'], $nidFrontPath);
        }

        if ($nidBack && $nidBack['error'] === 0) {
            $ext = pathinfo($nidBack['name'], PATHINFO_EXTENSION);
            $nidBackPath = $uploadDir . uniqid("back_") . "." . $ext;
            move_uploaded_file($nidBack['tmp_name'], $nidBackPath);
        }
    }

    // Insert into pending_users
    $stmt = $conn->prepare("
        INSERT INTO pending_users 
        (name, email, password, role, country_code, phone, nid_number, nid_front, nid_back, verification_token)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", 
        $name, $email, $password, $role, $country_code, $phone,
        $nidNumber, $nidFrontPath, $nidBackPath, $token
    );

    if ($stmt->execute()) {
        // Send verification email
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
                <p>Click below to verify your email and continue verification:</p>
                <p><a href='$verify_link' 
                      style='background:#5c67f2;color:white;padding:10px 15px;border-radius:6px;
                             text-decoration:none;'>Verify My Account</a></p>
                <p>If you didn’t sign up, you can ignore this email.</p>
                <br><p style='color:gray;'>– The Rentify Team</p>
            ";

            $mail->send();
            echo json_encode(["status"=>"success","message"=>"Verification link sent to your email."]);
            exit;
        } catch (Exception $e) {
            echo json_encode(["status"=>"error","message"=>"Email send failed: ".$mail->ErrorInfo]);
            exit;
        }
    } else {
        echo json_encode(["status"=>"error","message"=>"Database insert failed"]);
        exit;
    }

    $stmt->close();
    $conn->close();
}
ob_end_flush(); // clear any accidental output
?>

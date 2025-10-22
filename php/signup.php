<?php
// Disable display of errors for clean JSON
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
ob_start();

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name          = trim($_POST['name']);
    $email         = trim($_POST['email']);
    $password      = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role          = trim($_POST['role']);
    $country_code  = trim($_POST['countryCode']);
    $phone         = trim($_POST['phone']);

    // Optional landlord-only fields
    $nidNumber = $_POST['nidNumber'] ?? null;

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

    // Generate OTP (6-digit)
    $otp = rand(100000, 999999);

    // Save user temporarily
    $stmt = $conn->prepare("
        INSERT INTO pending_users 
        (name, email, password, role, country_code, phone, nid_number, otp, otp_expires_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))
    ");
    $stmt->bind_param("sssssssi", 
        $name, $email, $password, $role, $country_code, $phone, $nidNumber, $otp
    );

    if ($stmt->execute()) {

        // Send OTP using sms.net.bd API
        $api_key = "YOUR_SMS_NET_BD_API_KEY"; // replace this
        $senderid = "YOUR_SENDER_ID";          // replace this
        $message = "Your Rentify OTP is: $otp. It will expire in 10 minutes.";

        $url = "https://sms.net.bd/smsapi?api_key=" . urlencode($api_key) . 
               "&type=text&number=" . urlencode($phone) . 
               "&senderid=" . urlencode($senderid) . 
               "&message=" . urlencode($message);

        $response = file_get_contents($url);

        echo json_encode([
            "status" => "success",
            "message" => "OTP sent successfully to $phone",
            "response" => $response
        ]);
        exit;

    } else {
        echo json_encode(["status"=>"error","message"=>"Database insert failed"]);
        exit;
    }

    $stmt->close();
    $conn->close();
}

ob_end_flush();
?>

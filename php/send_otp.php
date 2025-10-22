<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
ini_set('log_errors',1);
ini_set('error_log', __DIR__.'/php-error.log');
error_reporting(E_ALL);
ini_set('display_errors', 0);

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Invalid request method']);
    exit;
}

$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$country = trim($_POST['countryCode'] ?? '');
$phone_input = preg_replace('/\D/','', $_POST['phone'] ?? '');
$phone = $country . $phone_input;
$password = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
$role = trim($_POST['role'] ?? '');

if (!$name || !$email || !$phone || !$password || !$role) {
    echo json_encode(['status'=>'error','message'=>'Missing required fields']); 
    exit;
}

// Check if already registered
$check = $conn->prepare("SELECT email FROM users WHERE email=? UNION SELECT email FROM pending_users WHERE email=?");
$check->bind_param('ss', $email, $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['status'=>'error','message'=>'Email already exists or pending']);
    exit;
}

// Create OTP
$otp = rand(100000, 999999);
$otp_time = date('Y-m-d H:i:s');
$otp_expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));

$stmt = $conn->prepare("INSERT INTO pending_users (name, email, phone, password, role, otp, otp_time, otp_expires_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $name, $email, $phone, $password, $role, $otp, $otp_time, $otp_expires_at);
$stmt->execute();


// ✅ Send OTP via SMS.net.bd
$sms_url = 'https://api.sms.net.bd/sendsms';
$sms_data = [
  'api_key' => '22FZ5mmiSajTZYdleN3HgnkYU8h373Uzn5VE87yE',
  'msg' => "Your Rentify OTP Code is $otp",
  'to' => $phone
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sms_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sms_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['status'=>'error','message'=>'SMS error: '.curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

file_put_contents(__DIR__.'/sms-response.log', date('c').' '.$phone.' '.$response.PHP_EOL, FILE_APPEND);

echo json_encode(['status'=>'success','message'=>'OTP sent! Please check your phone.']);
exit;
?>

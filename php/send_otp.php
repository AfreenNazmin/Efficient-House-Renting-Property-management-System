<?php
header('Content-Type: application/json');
include 'config.php';

// --- Configurable Options ---
$NEVER_EXPIRE_OTP = true; // make false if you want expiry
$OTP_EXPIRY_MINUTES = 10; // ignored if NEVER_EXPIRE_OTP=true

// --- Check method ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

// --- Clean Inputs ---
$country = trim($_POST['countryCode'] ?? '');
$phone_input = preg_replace('/\D/', '', $_POST['phone'] ?? '');
$phone = $country . $phone_input; // +880XXXXXXXXXX
$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = password_hash(trim($_POST['password'] ?? ''), PASSWORD_DEFAULT);
$role  = trim($_POST['role'] ?? '');
$nid_number = $_POST['nidNumber'] ?? null;

// --- Generate OTP ---
$otp = rand(100000, 999999);
$otp_time = date('Y-m-d H:i:s');
$otp_expires_at = $NEVER_EXPIRE_OTP 
    ? '2099-12-31 23:59:59'
    : date('Y-m-d H:i:s', strtotime("+$OTP_EXPIRY_MINUTES minutes"));

// --- Log start ---
file_put_contents(__DIR__ . '/otp-debug.log', date('c') . " [SEND_OTP] Sending to $phone OTP: $otp\n", FILE_APPEND);

// --- Check if email or phone already exists ---
$check = $conn->prepare("
    SELECT email FROM users WHERE email=? 
    UNION 
    SELECT email FROM pending_users WHERE email=?");
$check->bind_param("ss", $email, $email);
$check->execute();
$res = $check->get_result();
if ($res->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email already exists or pending verification']);
    exit;
}

// --- Store temporarily ---
$stmt = $conn->prepare("
    INSERT INTO pending_users (name, email, password, role, phone, nid_number, otp, otp_time, otp_expires_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("sssssssss", $name, $email, $password, $role, $phone, $nid_number, $otp, $otp_time, $otp_expires_at);
$stmt->execute();

// --- Send OTP via SMS.net.bd ---
$sms_url = 'https://api.sms.net.bd/sendsms';
$sms_data = [
  'api_key' => '22FZ5mmiSajTZYdleN3HgnkYU8h373Uzn5VE87yE',
  'msg' => "Your Rentify OTP is: $otp",
  'to'  => $phone
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sms_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sms_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    $err = curl_error($ch);
    file_put_contents(__DIR__ . '/otp-debug.log', date('c') . " [SEND_OTP] SMS Error: $err\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'SMS sending failed.']);
    curl_close($ch);
    exit;
}
curl_close($ch);

// --- Log response ---
file_put_contents(__DIR__ . '/otp-debug.log', date('c') . " [SEND_OTP] SMS Response: $response\n", FILE_APPEND);

echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully! Please check your phone.']);
exit;
?>

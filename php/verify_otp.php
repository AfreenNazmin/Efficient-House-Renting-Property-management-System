<?php
header('Content-Type: application/json');
include 'config.php';

// --- Check method ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

// --- Clean Inputs ---
$country = trim($_POST['countryCode'] ?? '');
$phone_input = preg_replace('/\D/', '', $_POST['phone'] ?? '');
$phone = $country . $phone_input; // +880XXXXXXXXXX
$otp   = trim($_POST['otp'] ?? '');

if (!$phone || !$otp) {
    echo json_encode(['status'=>'error','message'=>'Missing phone or OTP']);
    exit;
}

// --- Fetch pending user ---
$stmt = $conn->prepare("SELECT * FROM pending_users WHERE phone=? AND otp=?");
$stmt->bind_param('ss', $phone, $otp);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    echo json_encode(['status'=>'error','message'=>'Invalid OTP']);
    exit;
}

$user = $res->fetch_assoc();

// --- Landlord flow: mark phone verified, keep pending for admin ---
// Landlord
if ($user['role'] === 'landlord') {
    $upd = $conn->prepare("UPDATE pending_users SET phoneVerified=1, admin_review_status='pending' WHERE phone=?");
    $upd->bind_param('s', $phone);
    $upd->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'Phone verified! Your account request is pending admin approval.',
        'role' => 'landlord'
    ]);
    exit;
}

// Tenant
$ins = $conn->prepare("INSERT INTO users (name,email,password,role,phone) VALUES (?,?,?,?,?)");
$ins->bind_param('sssss', $user['name'], $user['email'], $user['password'], $user['role'], $user['phone']);
if ($ins->execute()) {
    $del = $conn->prepare("DELETE FROM pending_users WHERE id=?");
    $del->bind_param('i', $user['id']);
    $del->execute();

    echo json_encode([
        'status'=>'success',
        'message'=>'Account verified successfully!',
        'role'=>'tenant'
    ]);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to create user: '.$conn->error]);
}

?>

<?php
header('Content-Type: application/json');
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Invalid request']); exit;
}

$phone = trim($_POST['phone'] ?? '');
$otp   = trim($_POST['otp'] ?? '');
$phone = preg_replace('/[^\d\+]/','', $phone); // keep digits + plus


// Log incoming values
file_put_contents(__DIR__.'/otp-debug.log', date('c')." Incoming OTP: $otp, Phone: $phone\n", FILE_APPEND);

// Normalize phone (keep + and digits only)
$phone = preg_replace('/[^\d\+]/','', $phone);


if (!$phone || !$otp) {
    echo json_encode(['status'=>'error','message'=>'Missing phone or OTP']); exit;
}

// Check OTP validity
$stmt = $conn->prepare("SELECT * FROM pending_users WHERE phone=? AND otp=? AND otp_expires_at > NOW()");
$stmt->bind_param('ss', $phone, $otp);
$stmt->execute();
$res = $stmt->get_result();


file_put_contents(__DIR__.'/otp-debug.log', date('c')." Rows matched: ".$res->num_rows."\n", FILE_APPEND);


if ($res->num_rows !== 1) {
    echo json_encode(['status'=>'error','message'=>'Invalid or expired OTP']); exit;
}

$user = $res->fetch_assoc();

if ($user['role'] === 'landlord') {
    // Mark phone verified, wait for admin approval
    $upd = $conn->prepare("UPDATE pending_users SET phoneVerified=1 WHERE phone=?");
    $upd->bind_param('s', $phone);
    $upd->execute();
    echo json_encode(['status'=>'success','message'=>'Phone verified! Your landlord request is sent to admin.']);
    exit;
}

// Tenant → move to users
$ins = $conn->prepare("INSERT INTO users (name,email,password,role,phone) VALUES (?,?,?,?,?)");
$ins->bind_param('sssss', $user['name'], $user['email'], $user['password'], $user['role'], $user['phone']);
if ($ins->execute()) {
    $del = $conn->prepare("DELETE FROM pending_users WHERE id=?");
    $del->bind_param('i', $user['id']);
    $del->execute();
    echo json_encode(['status'=>'success','message'=>'Account verified successfully!']);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to create user: '.$conn->error]);
}
?>

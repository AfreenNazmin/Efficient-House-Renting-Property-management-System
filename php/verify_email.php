<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = $_POST['code'] ?? '';

    if ($input_code) {
        $stmt = $conn->prepare("SELECT verification_token FROM users WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($token);
        $stmt->fetch();
        $stmt->close();

        if ($token && $token == $input_code) {
            // Correct code
            $update = $conn->prepare("UPDATE users SET is_verified=1, verification_token=NULL WHERE id=?");
            $update->bind_param('i', $user_id);
            $update->execute();
            $update->close();
            $message = "Email verified successfully!";
        } else {
            $message = "Incorrect verification code.";
        }
    } else {
        $message = "Please enter the verification code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Email</title>
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
    <h2>Enter Verification Code</h2>
    <form method="POST">
        <label>Verification Code:</label>
        <input type="text" name="code" required>
        <button type="submit">Verify</button>
    </form>
    <?php if($message): ?><p class="message"><?= $message ?></p><?php endif; ?>
    <a href="tenant_profile.php"><button>Back to Profile</button></a>
</div>
</body>
</html>

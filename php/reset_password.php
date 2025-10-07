<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("SELECT id, reset_expiry FROM users WHERE reset_token=? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (strtotime($user['reset_expiry']) >= time()) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expiry=NULL WHERE id=?");
            $update->bind_param("si", $hashed, $user['id']);
            $update->execute();

            echo "<script>alert('Password reset successfully!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Reset link expired!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Invalid token!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
form {
  max-width: 400px;
  margin: 50px auto;
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  text-align: center;
  font-family: Arial, sans-serif;
}

form input[type="email"] {
  width: 100%;
  padding: 12px;
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 16px;
}

form button {
  background: #4CAF50;
  color: white;
  padding: 12px 25px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  transition: background 0.3s;
}

form button:hover {
  background: #45a049;
}
</style>


</head>
<body>
<form action="../php/reset_password.php" method="POST">
  <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
  <input type="password" name="new_password" placeholder="New Password" required>
  <input type="password" name="confirm_password" placeholder="Confirm Password" required>
  <button type="submit">Reset Password</button>
</form>
</body>
</html>
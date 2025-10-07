<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $token = bin2hex(random_bytes(50)); // secure reset token
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // token valid for 1 hour

        // Store token and expiry in DB (add columns reset_token, reset_expiry)
        $update = $conn->prepare("UPDATE users SET reset_token=?, reset_expiry=? WHERE email=?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        // Send reset link via email
        $resetLink = "http://yourdomain.com/html/reset_password.html?token=$token";
        $subject = "Password Reset Request";
        $message = "Click this link to reset your password: $resetLink \nValid for 1 hour.";
        $headers = "From: no-reply@yourdomain.com\r\n";

        mail($email, $subject, $message, $headers);

        echo "<script>alert('Reset link sent to your email!'); window.location.href='../html/login.html';</script>";
    } else {
        echo "<script>alert('No account found with this email.'); window.history.back();</script>";
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
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #0e0d0dff;
}
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
  background: #4a2506ff;
  color: white;
  padding: 12px 25px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  transition: background 0.3s;
}

form button:hover {
  background: #440b0bff;
}
a {
    display: inline-block;
    text-decoration: none;
    color: white;
    background: #d54f22ff;
    padding: 10px 20px;
    border-radius: 8px;
    text-align: center;
    
    font-weight: bold;
    transition: background 0.3s;
}

a:hover {
    background: #ae583eff;
}
.back
{
    text-align: center;
}

</style>


</head>
<body>
<h1 style="color: white; text-align: center;">Reset Password</h1>
 <form action="../php/forgot_password.php" method="POST">
  <input type="email" name="email" placeholder="Enter your registered email" required>
  <button type="submit">Send Reset Link</button>

</form>  
<div class="back">
  <a href="login.php">Back to login</a> </div>
</body>
</html>


<?php
session_start();
// If already logged in as admin, redirect to dashboard
if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'){
    header("Location: admin_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <style>
    /* Background */
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #1a1a1a, #111);
      font-family: 'Poppins', sans-serif;
      color: #fff;
    }

    /* Card */
    .login-box {
      background: #1f1f1f;
      padding: 40px 35px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0,0,0,0.6);
      width: 350px;
      text-align: center;
      animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      margin-bottom: 25px;
      color: #f8f8f8;
      font-size: 24px;
      letter-spacing: 1px;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px 14px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
      background: #2a2a2a;
      color: #fff;
      font-size: 15px;
    }

    input::placeholder {
      color: #bbb;
    }

    button {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border: none;
      border-radius: 8px;
      background: #00b894;
      color: #fff;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #019872;
    }

    .error {
      color: #ff6b6b;
      font-size: 14px;
      margin-bottom: 10px;
    }

    .footer {
      margin-top: 20px;
      font-size: 13px;
      color: #aaa;
    }

    .footer a {
      color: #00b894;
      text-decoration: none;
    }
    .footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <h2>Admin Login</h2>
    <form method="POST" action="admin_login_process.php">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <div class="footer">
      <p><a href="../index.php">← Back to Home</a></p>
    </div>
  </div>

</body>
</html>

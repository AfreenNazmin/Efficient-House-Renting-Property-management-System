<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    if (empty($email) || empty($password) || empty($role)) {
        echo "<script>alert('Please fill all fields!'); window.history.back();</script>";
        exit;
    }

    // Fetch user
    $stmt = $conn->prepare("SELECT id, name, email, password, role, is_verified FROM users WHERE email=? AND role=? LIMIT 1");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            if ($user['is_verified'] == 0) {
                echo "<script>alert('Please verify your email before logging in.'); window.location.href='../html/signup.html';</script>";
                exit;
            }

            // Remember Me cookie
            if (isset($_POST['remember'])) {
                setcookie('remember_email', $user['email'], time() + (30 * 24 * 60 * 60), "/");
            } else {
                setcookie('remember_email', '', time() - 3600, "/");
            }

            // Start session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];

            // Redirect based on role
            header("Location: ../php/" . ($user['role'] === 'tenant' ? "tenant.php" : "landlord.php"));
            exit;

        } else {
            echo "<script>alert('Invalid password!'); window.history.back();</script>";
        }

    } else {
        echo "<script>alert('No user found with this email or role.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Rentify</title>
  <link rel="stylesheet" href="../css/login.css" />
</head>
<body>
  <div class="overlay">
    <div class="login-wrapper">
      <!-- Role selection -->
      <div class="role-menu">
        <button type="button" onclick="setRole('tenant')" id="tenantBtn" class="active">Tenant</button>
        <button type="button" onclick="setRole('landlord')" id="landlordBtn">Landlord</button>
      </div>

      <!-- Login card -->
      <div class="login-card">
        <h2>Welcome Back</h2>
        <p class="sub-text">Sign in to continue to <strong>Rentify</strong></p>

        <form id="loginForm" action="" method="POST">
          <input type="text" name="email" placeholder="Email" value="<?php echo isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : ''; ?>" required>

          <div class="password-field">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <span class="toggle-password" onclick="togglePassword('password')">&#128065;</span>
          </div>

          <input type="hidden" name="role" id="roleInput" value="tenant">

          <div class="row-between">
            <label class="checkbox">
              <input type="checkbox" name="remember" <?php echo isset($_COOKIE['remember_email']) ? 'checked' : ''; ?>>
              <span>Remember me</span>
            </label>
            <a href="forgot_password.php" class="forgot">Forgot password?</a>
          </div>

          <button type="submit" class="primary-btn">Log in</button>
        </form>

        <p class="signup-link">Don't have an account? <a href="../html/signup.html">Sign up</a></p>
      </div>
    </div>
  </div>

  <script src="../js/login.js"></script>

  <!-- Auto-select landlord if ?role=landlord -->
  <script>
    <?php if (isset($_GET['role']) && $_GET['role'] === 'landlord'): ?>
      window.onload = () => setRole('landlord');
    <?php endif; ?>
  </script>
</body>
</html>

<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    // Check for empty fields
    if (empty($email) || empty($password) || empty($role)) {
        echo "<script>alert('Please fill all fields!'); window.history.back();</script>";
        exit;
    }

    // Fetch user securely
    $stmt = $conn->prepare("SELECT id, name, email, password, role, is_verified FROM users WHERE email=? AND role=? LIMIT 1");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {

            // Check email verification
            if ($user['is_verified'] == 0) {
                echo "<script>alert('Please verify your email before logging in.'); window.location.href='../html/signup.html';</script>";
                exit;
            }

            // Start session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'tenant') {
                header("Location: ../php/tenant.php");
            } else {
                header("Location: ../php/landlord.php");
            }
            exit;
        } else {
            echo "<script>alert('Invalid password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No user found with this email or role.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    // Block direct access
    header("Location: ../html/login.html");
    exit;
}
?>

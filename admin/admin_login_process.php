<?php
session_start();
include '../php/config.php'; 

$username = $_POST['username'];
$password = $_POST['password'];


if($username === 'SAdmin' && $password === 'admin1234'){
    $_SESSION['role'] = 'admin';
    header("Location: admin_dashboard.php");
} else {
    echo "<script>alert('❌ Invalid credentials'); window.location='admin_login.php';</script>";
}
?>

<?php
include 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT * FROM pending_users WHERE verification_token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Move to users table
        $insert = $conn->prepare("
            INSERT INTO users (name, email, password, role, is_verified)
            VALUES (?, ?, ?, ?, 1)
        ");
        $insert->bind_param("ssss", $user['name'], $user['email'], $user['password'], $user['role']);
        $insert->execute();
        $insert->close();

        // Delete from pending_users
        $delete = $conn->prepare("DELETE FROM pending_users WHERE email=?");
        $delete->bind_param("s", $user['email']);
        $delete->execute();
        $delete->close();

        echo "
        <html>
        <head>
            <title>Email Verified</title>
            <style>
                body {
                    background: #f4f6ff;
                    font-family: 'Poppins', sans-serif;
                    text-align: center;
                    padding-top: 120px;
                }
                .container {
                    display: inline-block;
                    background: white;
                    padding: 40px 60px;
                    border-radius: 16px;
                    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
                }
                h2 {
                    color: #28a745;
                }
                p {
                    color: #555;
                    margin-top: 10px;
                    margin-bottom: 25px;
                }
                a.button {
                    background: #5c67f2;
                    color: white;
                    padding: 12px 25px;
                    border-radius: 8px;
                    text-decoration: none;
                    font-weight: 500;
                    transition: background 0.3s ease;
                }
                a.button:hover {
                    background: #3d47e0;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>✅ Verification Successful!</h2>
                <p>Your email has been verified successfully.</p>
                <a href='../html/login.html' class='button'>Go to Login</a>
            </div>
        </body>
        </html>";
    } else {
        echo "<h3 style='color:red;text-align:center;margin-top:100px;font-family:sans-serif;'>Invalid or expired verification link.</h3>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<h3 style='color:red;text-align:center;margin-top:100px;font-family:sans-serif;'>No token provided!</h3>";
}
?>

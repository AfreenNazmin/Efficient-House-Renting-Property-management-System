<?php
include 'config.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role  = $_POST['role']; // tenant / landlord

    $sql = "INSERT INTO users (name, email, password, role) 
            VALUES ('$name', '$email', '$password', '$role')";
    if ($conn->query($sql) === TRUE) {
        echo "Signup successful as $role!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>



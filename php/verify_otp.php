<?php
session_start();
include 'config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $phone = preg_replace('/\D/', '', $_POST['phone']);
    $otp = $_POST['otp'];

    $stmt = $conn->prepare("SELECT * FROM pending_users WHERE phone=? AND otp=?");
    $stmt->bind_param("ss", $phone,$otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();

        // Insert into users table
        $stmt2 = $conn->prepare("INSERT INTO users (name,email,phone,password,role,email_verified,phone_verified) VALUES (?,?,?,?,?,0,1)");
        $stmt2->bind_param("sssss",$user['name'],$user['email'],$user['phone'],$user['password'],$user['role']);
        $stmt2->execute();

        // Delete from pending_users
        $stmt3 = $conn->prepare("DELETE FROM pending_users WHERE id=?");
        $stmt3->bind_param("i",$user['id']);
        $stmt3->execute();

        echo json_encode(['status'=>'success','message'=>'Phone verified! Signup complete.']);
    }else{
        echo json_encode(['status'=>'error','message'=>'Invalid OTP.']);
    }
}
?>

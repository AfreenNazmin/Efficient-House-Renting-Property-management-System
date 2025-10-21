<?php
session_start();
include 'config.php'; // DB connection

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = preg_replace('/\D/', '', $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $otp = rand(100000, 999999);
    $otp_time = date('Y-m-d H:i:s');

    // Insert into pending_users
    $stmt = $conn->prepare("INSERT INTO pending_users (name,email,phone,password,role,otp,otp_time) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssss", $name,$email,$phone,$password,$role,$otp,$otp_time);
    $stmt->execute();

    // Send OTP via SMS.net.bd
    $url = 'https://api.sms.net.bd/sendsms';
    $data = [
        'api_key' => '22FZ5mmiSajTZYdleN3HgnkYU8h373Uzn5VE87yE',
        'msg' => "Your OTP is: $otp",
        'to' => $phone,
        'sender_id' => 'YourSenderID'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if(curl_errno($ch)){
        echo json_encode(['status'=>'error','message'=>curl_error($ch)]);
        exit;
    }
    curl_close($ch);

    echo json_encode(['status'=>'success','message'=>'OTP sent! Please check your phone.']);
}
?>

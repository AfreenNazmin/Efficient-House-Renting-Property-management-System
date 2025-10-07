<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  
        $mail->SMTPAuth = true;
        $mail->Username = 'rentify.smtp@gmail.com'; 
        $mail->Password = 'smud otml tuix epmw';  
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

       
   $mail->addAddress('rentify.smtp@gmail.com', 'Support Team');
$mail->setFrom('rentify.smtp@gmail.com', 'Rentify');
$mail->addReplyTo($email, $name);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "New Message from $name";
        $mail->Body = "
            <h3>New Message Received</h3>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Message:</strong></p>
            <p>{$message}</p>
        ";

        $mail->send();
        echo "<script>alert('Message sent successfully!'); window.history.back();</script>";
    } catch (Exception $e) {
        echo "<script>alert('Error: {$mail->ErrorInfo}'); window.history.back();</script>";
    }
}
?>

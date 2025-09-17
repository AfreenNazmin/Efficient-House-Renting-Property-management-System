<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
$mail=new PHPMailer(true);
try
{
    $mail->isSMTP();
    $mail->Host ='smtp.gmail.com';
    $mail->SMTPAuth =true;
    $mail->Username ='mrk243719@gmail.com';
    $mail
}
?>
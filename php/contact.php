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
<?php 
include 'bar.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us</title>
  <link rel="stylesheet" href="../css/contact.css">
</head>
<body>

  <section class="properties-header">
    <div class="new-section">
      
      <!-- Contact Header -->
      <section class="contact-header">
        <div class="container">
          <h1>Get in Touch</h1>
          <p>We’d love to hear from you. Reach out anytime!</p>
        </div>
      </section>

      <!-- Contact Info -->
      <section class="contact-info">
        <div class="container">
          <div class="info-cards">
            <div class="card">
              <h3>📍 Address</h3>
              <p>123 Main Street, Cityville</p>
            </div>
            <div class="card">
              <h3>📞 Phone</h3>
              <p>+880 1234 567 890</p>
            </div>
            <div class="card">
              <h3>✉️ Email</h3>
              <p>rentify.smtp@gmail.com</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Contact Form -->
      <section class="contact-form">
        <div class="container">
          <h2>Send Us a Message</h2>
          <form action="../php/contact.php" method="post">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" rows="6" placeholder="Your Message" required></textarea>
            <button type="submit">Send Message</button>
          </form>
        </div>
      </section>

      <!-- Footer -->
      <div id="footer"></div>
    </div>
  </section>

  <!-- Footer Loader -->
  <script>
    fetch('../html/footer.html') 
      .then(res => res.text())
      .then(data => document.getElementById('footer').innerHTML = data);
  </script>

</body>
</html>

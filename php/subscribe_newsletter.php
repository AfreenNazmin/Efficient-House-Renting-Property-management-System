<?php
include 'config.php'; // adjust path if needed

header('Content-Type: text/plain'); // send plain text for AJAX

if(isset($_POST['email'])){
    $email = trim($_POST['email']);

    // Validate email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "Invalid email address.";
        exit;
    }

    // Check if already subscribed
    $stmt = $conn->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        echo "You are already subscribed!";
        exit;
    }

    // Insert new subscriber
    $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
    $stmt->bind_param("s", $email);

    if($stmt->execute()){
        echo "Thank you for subscribing!";
    } else {
        echo "Subscription failed. Please try again.";
    }

} else {
    echo "Email is required.";
}
?>

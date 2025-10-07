<?php
include '../php/config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $property_id = intval($_POST['property_id']);
    $tenant_name = $_POST['tenant_name'];
    $tenant_email = $_POST['tenant_email'];
    $tenant_phone = $_POST['tenant_phone'];
    $national_id = $_POST['national_id'];
    $move_in_date = $_POST['move_in_date'];
    $rental_period = $_POST['rental_period'];
    $payment_method = $_POST['payment_method'];
    $current_address = $_POST['current_address'];
    $emergency_contact = $_POST['emergency_contact'];
    $notes = $_POST['notes'];
    $terms = isset($_POST['terms']) ? 1 : 0;  // Ensure terms are agreed to (1 for yes, 0 for no)

    // SQL Query to insert rental request
    $sql = "INSERT INTO rental_requests (property_id, tenant_name, tenant_email, tenant_phone, national_id, move_in_date, rental_period, payment_method, current_address, emergency_contact, notes, terms) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters (adjust the types to match the number of arguments)
    $stmt->bind_param(
        'isssssssssss', 
        $property_id, 
        $tenant_name, 
        $tenant_email, 
        $tenant_phone, 
        $national_id, 
        $move_in_date, 
        $rental_period, 
        $payment_method, 
        $current_address, 
        $emergency_contact, 
        $notes, 
        $terms
    );

    // Execute the query
    if ($stmt->execute()) {
        // Optionally send an email notification to tenant and landlord
        $tenant_subject = "Rental Request Confirmation";
        $tenant_message = "Thank you for your rental request. We will review your details and contact you shortly.";
        mail($tenant_email, $tenant_subject, $tenant_message);

        // Redirect or show a confirmation message
        echo "Rental request submitted successfully.";
        // You can redirect to a "Thank You" page or the properties page
        header("Location: ../html/submitted.html");
        exit;
    } else {
        echo "Error: Could not submit your request.";
    }
}
?>

<?php
include 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Capture form data safely
    $property_id       = intval($_POST['property_id']);
    $tenant_name       = trim($_POST['tenant_name']);
    $tenant_email      = trim($_POST['tenant_email']);
    $tenant_phone      = trim($_POST['tenant_phone']);
    $national_id       = trim($_POST['national_id']);
    $move_in_date      = $_POST['move_in_date'];
    $payment_method    = $_POST['payment_method'];
    $current_address   = $_POST['current_address'] ?? '';
    $emergency_contact = trim($_POST['emergency_contact']);
    $notes             = $_POST['notes'] ?? '';
    $terms             = isset($_POST['terms']) ? 1 : 0;

// Prevent past or same-day move-in dates
$today = date('Y-m-d');
if ($move_in_date <= $today) {
    die("❌ Move-in date must be from tomorrow or later.");
}


    // Handle PDF upload
    if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        die("❌ PDF file is required.");
    }

    $tmpPath = $_FILES['pdf_file']['tmp_name'];
    $fileName = uniqid('tenant_') . '_' . basename($_FILES['pdf_file']['name']); // unique filename
    $targetDir = '../uploads/pdfs/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    $targetPath = $targetDir . $fileName;

    if (!move_uploaded_file($tmpPath, $targetPath)) {
        die("❌ Failed to upload PDF.");
    }

// Check if this tenant already submitted a request for this property
$check_sql = "SELECT COUNT(*) AS c FROM rental_requests WHERE property_id=? AND tenant_email=?";
$stmt_check = $conn->prepare($check_sql);
$stmt_check->bind_param("is", $property_id, $tenant_email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$count = $result_check->fetch_assoc()['c'];

if ($count > 0) {
    die("❌ You have already submitted a rental request for this property.");
}


    // Insert into DB (store only file name)
    $sql = "INSERT INTO rental_requests 
        (property_id, tenant_name, tenant_email, tenant_phone, national_id, move_in_date, payment_method, current_address, emergency_contact, notes, terms, pdf_file) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'isssssssssss',
        $property_id,
        $tenant_name,
        $tenant_email,
        $tenant_phone,
        $national_id,
        $move_in_date,
        $payment_method,
        $current_address,
        $emergency_contact,
        $notes,
        $terms,
        $fileName // store only the file name
    );

    if ($stmt->execute()) {
        // Optional email notification
        $tenant_subject = "Rental Request Confirmation";
        $tenant_message = "Thank you for your rental request. We will review your details and contact you shortly.";
        mail($tenant_email, $tenant_subject, $tenant_message);

        header("Location: ../html/submitted.html");
        exit;
    } else {
        die("❌ Error: Could not submit your rental request. " . $stmt->error);
    }
}
?>

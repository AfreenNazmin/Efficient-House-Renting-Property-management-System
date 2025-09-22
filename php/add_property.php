<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: ../php/login.php");
    exit();
}

include 'config.php';

// Check form submission
if(!isset($_POST['property_name'], $_POST['location'], $_POST['rent'])){
    echo "Form data missing!";
    exit;
}

$property_name = $_POST['property_name'];
$location = $_POST['location'];
$rent = $_POST['rent'];
$landlord = $_SESSION['username'];

// Handle image upload
$image_path = null;
if(isset($_FILES['property_image']) && $_FILES['property_image']['error'] == 0){
    $img_name = time() . '_' . basename($_FILES['property_image']['name']); // unique filename
    $img_folder = '../uploads/' . $img_name;

    // Create uploads folder if not exists
    if(!is_dir('../uploads')){
        mkdir('../uploads', 0777, true);
    }

    if(move_uploaded_file($_FILES['property_image']['tmp_name'], $img_folder)){
        $image_path = 'uploads/' . $img_name; // store relative path for dashboard
    }
}

// Insert property into database
$sql = "INSERT INTO properties (property_name, location, rent, landlord, image) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiss", $property_name, $location, $rent, $landlord, $image_path);

if($stmt->execute()){
    // redirect back to dashboard
    header("Location: landlord.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

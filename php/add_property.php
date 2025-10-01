<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: ../php/login.php");
    exit();
}

include 'config.php';

if(!isset($_POST['property_name'], $_POST['location'], $_POST['rent'], $_POST['property_type'], $_POST['bedrooms'])){
    echo "Form data missing!";
    exit;
}

$property_name = $_POST['property_name'];
$location = $_POST['location'];
$rent = $_POST['rent'];
$property_type = $_POST['property_type'];
$bedrooms = $_POST['bedrooms'];
$landlord = $_SESSION['username'];

$image_path = null;
if(isset($_FILES['property_image']) && $_FILES['property_image']['error'] == 0){
    $img_name = time() . '_' . basename($_FILES['property_image']['name']);
    $img_folder = '../uploads/' . $img_name;

    if(!is_dir('../uploads')){
        mkdir('../uploads', 0777, true);
    }

    if(move_uploaded_file($_FILES['property_image']['tmp_name'], $img_folder)){
        $image_path = 'uploads/' . $img_name;
    }
}

$sql = "INSERT INTO properties (property_name, location, rent, property_type, bedrooms, landlord, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssissis", $property_name, $location, $rent, $property_type, $bedrooms, $landlord, $image_path);

if($stmt->execute()){
    header("Location: landlord.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

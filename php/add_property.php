<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: ../php/login.php");
    exit();
}

include 'config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Required fields
    $required_fields = ['property_name', 'location', 'rent'];
    foreach($required_fields as $field){
        if(empty($_POST[$field])){
            echo "Form data missing for: " . $field;
            exit;
        }
    }

    $property_name = $_POST['property_name'];
    $location = $_POST['location'];
    $rent = $_POST['rent'];
    $property_type = $_POST['property_type'] ?? 'Not specified';
    $bedrooms = $_POST['bedrooms'] ?? 0;
    $landlord = $_SESSION['username'];

    // Image upload
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
    $stmt->bind_param("ssissss", $property_name, $location, $rent, $property_type, $bedrooms, $landlord, $image_path);

    if($stmt->execute()){
        header("Location: ../php/landlord.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: ../php/login.php");
    exit();
}

include 'config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $property_id = $_POST['property_id'];
    $name = $_POST['property_name'];
    $location = $_POST['location'];
    $rent = $_POST['rent'];
    $landlord = $_SESSION['username'];

    // Handle image upload
    $image_path = null;
    if(isset($_FILES['property_image']) && $_FILES['property_image']['error'] == 0){
        $img_name = time() . '_' . basename($_FILES['property_image']['name']);
        $img_folder = '../uploads/' . $img_name;
        if(move_uploaded_file($_FILES['property_image']['tmp_name'], $img_folder)){
            $image_path = 'uploads/' . $img_name;
        }
    }

    // Update property
    if($image_path){
        $sql = "UPDATE properties SET property_name=?, location=?, rent=?, image=? WHERE id=? AND landlord=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissi", $name, $location, $rent, $image_path, $property_id, $landlord);
    } else {
        $sql = "UPDATE properties SET property_name=?, location=?, rent=? WHERE id=? AND landlord=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisi", $name, $location, $rent, $property_id, $landlord);
    }

    if($stmt->execute()){
        header("Location: landlord.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

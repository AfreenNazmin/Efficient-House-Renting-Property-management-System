<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: ../php/login.php");
    exit();
}

include 'config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = $_POST['property_id'];
    $landlord = $_SESSION['username'];

    // Collect fields
    $name = $_POST['property_name'];
    $location = $_POST['location'];
    $rent = $_POST['rent'];
    $bedrooms = $_POST['bedrooms'] ?? 1;
    $property_type = $_POST['property_type'] ?? 'Apartment';
    $description = $_POST['description'] ?? null;
    $bathrooms = $_POST['bathrooms'] ?? 1;
    $size = $_POST['size'] ?? null;
    $floor = $_POST['floor'] ?? null;
    $parking = isset($_POST['parking']) ? 1 : 0;
    $furnished = isset($_POST['furnished']) ? 1 : 0;
    $available = isset($_POST['available']) ? 1 : 0;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $map_embed = $_POST['map_embed'] ?? null;

    // Handle main image
    $image_path = null;
    if(isset($_FILES['property_image']) && $_FILES['property_image']['error'] == 0){
        $img_name = time() . '_' . basename($_FILES['property_image']['name']);
        $img_folder = '../uploads/' . $img_name;
        if(move_uploaded_file($_FILES['property_image']['tmp_name'], $img_folder)){
            $image_path = 'uploads/' . $img_name;
        }
    }

    // Handle floor plan
    $floor_plan_path = null;
    if(isset($_FILES['floor_plan']) && $_FILES['floor_plan']['error'] == 0){
        $fp_name = time() . '_' . basename($_FILES['floor_plan']['name']);
        $fp_folder = '../uploads/' . $fp_name;
        if(move_uploaded_file($_FILES['floor_plan']['tmp_name'], $fp_folder)){
            $floor_plan_path = 'uploads/' . $fp_name;
        }
    }

    // Build SQL dynamically
    $fields = "property_name=?, location=?, rent=?, bedrooms=?, property_type=?, description=?, available=?, featured=?, size=?, bathrooms=?, floor=?, parking=?, furnished=?, map_embed=?";
    $types = "ssisssiiisiiss";
    $params = [$name, $location, $rent, $bedrooms, $property_type, $description, $available, $featured, $size, $bathrooms, $floor, $parking, $furnished, $map_embed];

    if($image_path){
        $fields .= ", image=?";
        $types .= "s";
        $params[] = $image_path;
    }
    if($floor_plan_path){
        $fields .= ", floor_plan=?";
        $types .= "s";
        $params[] = $floor_plan_path;
    }

    $sql = "UPDATE properties SET $fields WHERE id=? AND landlord=?";
    $types .= "is";
    $params[] = $id;
    $params[] = $landlord;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if($stmt->execute()){
        header("Location: landlord.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

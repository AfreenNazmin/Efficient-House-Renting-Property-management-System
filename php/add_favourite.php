<?php
session_start();
include 'config.php';
if(!isset($_SESSION['user_id'])) {
  echo "Please login as tenant first."; exit;
}

$tenant_id = $_SESSION['user_id'];
$property_id = (int)$_POST['property_id'];

$sql = "INSERT IGNORE INTO favourites (tenant_id, property_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $tenant_id, $property_id);
$stmt->execute();
echo "Added to favourites!";

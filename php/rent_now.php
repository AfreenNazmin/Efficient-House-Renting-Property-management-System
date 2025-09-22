<?php
session_start();
if(!isset($_SESSION['user_id']) && !isset($_SESSION['username'])){
    header("Location: ../php/login.php");
    exit();
}
include 'config.php';

// determine tenant id
if(isset($_SESSION['user_id'])) $tenant_id = (int)$_SESSION['user_id'];
else {
    $sql_tmp = "SELECT id FROM users WHERE username = ?";
    $stmp = $conn->prepare($sql_tmp);
    $stmp->bind_param("s", $_SESSION['username']);
    $stmp->execute();
    $res_tmp = $stmp->get_result()->fetch_assoc();
    $tenant_id = (int)$res_tmp['id'];
    $stmp->close();
}

// inputs
$property_id = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;

if(!$property_id || !$start_date || !$end_date){
    header("Location: ../php/tenant.php?err=" . urlencode("Missing required fields"));
    exit();
}

// check availability
$check = $conn->prepare("SELECT available FROM properties WHERE id = ?");
$check->bind_param("i", $property_id);
$check->execute();
$res = $check->get_result();
if($res->num_rows == 0){
    header("Location: ../php/tenant.php?err=" . urlencode("Property not found"));
    exit();
}
$row = $res->fetch_assoc();
if((int)$row['available'] === 0){
    header("Location: ../php/tenant.php?err=" . urlencode("Property already rented/unavailable"));
    exit();
}
$check->close();

// insert rental
$ins = $conn->prepare("INSERT INTO rentals (property_id, tenant_id, start_date, end_date, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())");
$ins->bind_param("iiss", $property_id, $tenant_id, $start_date, $end_date);
$ok = $ins->execute();
$ins->close();

if($ok){
    // mark property unavailable (optional)
    $upd = $conn->prepare("UPDATE properties SET available = 0 WHERE id = ?");
    $upd->bind_param("i", $property_id);
    $upd->execute();
    $upd->close();

    header("Location: ../php/tenant.php?msg=rented");
    exit();
} else {
    header("Location: ../php/tenant.php?err=" . urlencode("Could not create rental"));
    exit();
}
?>

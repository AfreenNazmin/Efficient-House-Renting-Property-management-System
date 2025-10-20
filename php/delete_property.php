<?php
session_start();
include 'config.php';

if(isset($_GET['id']) && isset($_SESSION['username'])){
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM properties WHERE id=? AND landlord=?");
    $stmt->bind_param("is", $id, $_SESSION['username']);
    if($stmt->execute()){
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>

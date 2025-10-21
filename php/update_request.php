<?php
include 'config.php';
$id = $_GET['id'];
$status = $_GET['status'];
$conn->query("UPDATE rental_requests SET status='$status' WHERE id=$id");
header("Location: manage_requests.php");
?>

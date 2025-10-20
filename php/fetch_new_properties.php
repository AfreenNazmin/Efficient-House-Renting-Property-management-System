<?php
// php/fetch_new_properties.php
session_start();
include 'config.php';

$lastCheck = $_GET['lastCheck'] ?? null;
// Validate timestamp format quickly — fallback
if(!$lastCheck || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $lastCheck)){
    $lastCheck = '1970-01-01 00:00:00';
}

// Only return a small subset (limit) for perf
$limit = 5;

$sql = "SELECT id, property_name, thumbnail, description, created_at FROM properties WHERE created_at > ? ORDER BY created_at DESC LIMIT ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $lastCheck, $limit);
$stmt->execute();
$result = $stmt->get_result();

$properties = [];
while($r = $result->fetch_assoc()){
    // Ensure thumbnail fallback
    if(empty($r['thumbnail'])) $r['thumbnail'] = 'assets/default_thumb.png';
    $properties[] = $r;
}

header('Content-Type: application/json');
echo json_encode($properties);
exit();
?>

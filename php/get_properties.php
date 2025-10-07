<?php
session_start();
include 'config.php';

$where = ["available = 1"];
$params = [];
$types = "";

if(isset($_GET['location']) && trim($_GET['location']) !== ''){
    $where[] = "location LIKE ?";
    $types .= "s";
    $params[] = "%".trim($_GET['location'])."%";
}

if(isset($_GET['max_rent']) && is_numeric($_GET['max_rent'])){
    $where[] = "rent <= ?";
    $types .= "i";
    $params[] = (int)$_GET['max_rent'];
}

$sql = "SELECT id, property_name, location, rent, image, bedrooms, property_type, latitude, longitude 
        FROM properties 
        WHERE ".implode(" AND ", $where)." 
        ORDER BY rent ASC";

$stmt = $conn->prepare($sql);
if(!empty($params)){
    $bind_names[] = $types;
    for($i=0;$i<count($params);$i++){
        $bind_names[] = &$params[$i];
    }
    call_user_func_array([$stmt,'bind_param'],$bind_names);
}

$stmt->execute();
$result = $stmt->get_result();
$properties = [];
while($row = $result->fetch_assoc()){
    $properties[] = $row;
}

header('Content-Type: application/json');
echo json_encode($properties);

$stmt->close();
$conn->close();
?>

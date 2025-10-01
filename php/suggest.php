<?php
include 'config.php';
if(isset($_GET['term'])){
    $term = $_GET['term']."%";
    $stmt = $conn->prepare("SELECT DISTINCT city FROM properties WHERE city LIKE ? LIMIT 10");
    $stmt->bind_param("s",$term);
    $stmt->execute();
    $result = $stmt->get_result();
    $suggestions = [];
    while($row = $result->fetch_assoc()){
        $suggestions[] = $row['city'];
    }
    echo json_encode($suggestions);
}
?>

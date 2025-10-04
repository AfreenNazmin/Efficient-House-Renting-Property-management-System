<?php
include 'config.php'; 

if (isset($_GET['query'])) {
    $search = $_GET['query'];

    $stmt = $conn->prepare("SELECT * FROM properties WHERE city LIKE ? OR neighborhood LIKE ? OR address LIKE ?");
    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "<div class='property-card'>";
        echo "<h3>".$row['title']."</h3>";
        echo "<p>".$row['price']." - ".$row['city']."</p>";
        echo "</div>";
    }
}
?>

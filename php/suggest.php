<?php
include "config.php";

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($conn, $_POST['query']);
    $sql = "SELECT DISTINCT property_name, location 
            FROM properties 
            WHERE property_name LIKE '%$query%' OR location LIKE '%$query%' 
            LIMIT 5";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="suggestion-item">'.$row['property_name'].' - '.$row['location'].'</div>';
        }
    } else {
        echo '<div class="suggestion-item">No match found</div>';
}
}
?>
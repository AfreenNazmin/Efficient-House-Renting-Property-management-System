<?php
include "config.php";

 
    $search = isset($_GET['query'])?mysqli_real_escape_string($conn,$_GET['query']):'';
    $sql = "SELECT * FROM properties 
            WHERE property_name LIKE '%$search%' 
            OR location LIKE '%$search%' 
            OR property_type LIKE '%$search%'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="result">';
            echo '<h3>'.$row['property_name'].' - '.$row['location'].'</h3>';
            echo '<p><strong>Rent:</strong> '.$row['rent'].' | <strong>Bedrooms:</strong> '.$row['bedrooms'].' | <strong>Type:</strong> '.$row['property_type'].'</p>';
            echo '<img src="'.$row['image'].'" alt="'.$row['property_name'].'">';
            echo '</div>';
        }
    } else {
        echo '<div class="result">No results found</div>';
    }     

?>
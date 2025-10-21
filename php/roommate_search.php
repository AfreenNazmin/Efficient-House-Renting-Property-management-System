<?php
include('config.php'); 


$result = null;
$searchClicked = false;


if (isset($_GET['search']) && (
    !empty($_GET['location']) ||
    !empty($_GET['budget']) ||
    !empty($_GET['gender']) ||
    !empty($_GET['smoking']) ||
    !empty($_GET['pets']) ||
    !empty($_GET['cleanliness'])
)) {
    $searchClicked = true;
    $query = "SELECT * FROM roommates WHERE 1=1";

    if (!empty($_GET['location'])) {
        $location = $_GET['location'];
        $query .= " AND location LIKE '%$location%'";
    }

    if (!empty($_GET['budget'])) {
        $budget = $_GET['budget'];
        $query .= " AND budget <= $budget";
    }

    if (!empty($_GET['gender'])) {
        $gender = $_GET['gender'];
        $query .= " AND gender = '$gender'";
    }

    if (!empty($_GET['smoking'])) {
        $smoking = $_GET['smoking'];
        $query .= " AND smoking = '$smoking'";
    }

    if (!empty($_GET['pets'])) {
        $pets = $_GET['pets'];
        $query .= " AND pets = '$pets'";
    }

    if (!empty($_GET['cleanliness'])) {
        $cleanliness = $_GET['cleanliness'];
        $query .= " AND cleanliness = '$cleanliness'";
    }

    $result = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Find a Compatible Roommate</title>
  <style>
    body { font-family: Arial; background: #000; color: #fff; }
    .container { width: 85%; margin: auto; padding: 20px; }
    .roommate-card { 
        background: #1e1e1e; 
        padding: 15px; 
        margin-bottom: 15px; 
        border-radius: 8px; 
        box-shadow: 0 2px 5px rgba(255,255,255,0.2); 
    }
    input, select, button { padding: 8px; margin: 5px; border-radius: 6px; border: none; }
    input, select { background: #333; color: white; }
    button { background: #555; color: white; cursor: pointer; }
    button:hover { background: #777; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Find a Compatible Roommate</h2>

    <form method="GET">
      <input type="text" name="location" placeholder="Location">
      <input type="number" name="budget" placeholder="Max Budget">
      <select name="gender">
        <option value="">Any Gender</option>
        <option>Male</option>
        <option>Female</option>
        <option>Other</option>
      </select>
      <select name="smoking">
        <option value="">Smoking?</option>
        <option>Yes</option>
        <option>No</option>
      </select>
      <select name="pets">
        <option value="">Pets?</option>
        <option>Yes</option>
        <option>No</option>
      </select>
      <select name="cleanliness">
        <option value="">Cleanliness</option>
        <option>High</option>
        <option>Medium</option>
        <option>Low</option>
      </select>
      <button type="submit" name="search">Search</button>
    </form>

    <hr>

    <?php
    
    if ($searchClicked) {
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='roommate-card'>
                        <h3>{$row['name']} ({$row['gender']}, {$row['age']})</h3>
                        <p><b>Location:</b> {$row['location']}</p>
                        <p><b>Budget:</b> {$row['budget']} BDT</p>
                        <p><b>Occupation:</b> {$row['occupation']}</p>
                        <p><b>Smoking:</b> {$row['smoking']} | <b>Pets:</b> {$row['pets']} | <b>Cleanliness:</b> {$row['cleanliness']}</p>
                        <p><b>About:</b> {$row['about']}</p>
                      </div>";
            }
        } else {
            echo "<p>No matching roommates found. Try adjusting filters!</p>";
        }
    } else {
        echo "<p style='color:gray;'>Please enter search filters and click Search.</p>";
    }
    ?>
  </div>
</body>
</html>

<?php
include('db_connect.php'); 


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
?>

<!DOCTYPE html>
<html>
<head>
  <title>Find a Roommate</title>
  <style>
    
    body {
        background-color: #000000; 
        color: #ffffff; 
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    h2 {
        text-align: center;
        color: #ffffff;
        margin-top: 20px;
    }

    form {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
        margin: 20px auto;
        max-width: 800px;
    }

    input, select, button {
        padding: 10px;
        border-radius: 8px;
        border: none;
        outline: none;
    }

    input, select {
        background-color: #333333; 
        color: white;
    }

    button {
        background-color: #444444;
        color: white;
        cursor: pointer;
    }

    button:hover {
        background-color: #666666;
    }

    .roommate-card {
        background-color: #111111; 
        border: 1px solid #333333;
        border-radius: 10px;
        padding: 15px;
        margin: 10px auto;
        max-width: 600px;
        box-shadow: 0px 0px 10px rgba(255,255,255,0.1);
    }

    .roommate-card h3 {
        color: #ffffff; 
    }

    hr  {
        border: 0.5px solid #333;
        margin: 20px 0;
    }

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
      <button type="submit">Search</button>
    </form>

    <hr>

    <?php
    if (mysqli_num_rows($result) > 0) {
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
    ?>
  </div>
</body>
</html>
<?php
session_start();
include 'config.php';

// ✅ Only process when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $landlord = $_SESSION['username'] ?? '';

    // Basic property info
    $property_name = $_POST['property_name'] ?? '';
    $location = $_POST['location'] ?? '';
    $rent = $_POST['rent'] ?? 0;
    $property_type = $_POST['property_type'] ?? 'Apartment';
  $rental_type = isset($_POST['rental_type']) ? implode(',', $_POST['rental_type']) : 'both';

    $bedrooms = $_POST['bedrooms'] ?? 1;
    $bathrooms = $_POST['bathrooms'] ?? 1;
    $size = $_POST['size'] ?? NULL;
    $floor = $_POST['floor'] ?? NULL;
    $parking = isset($_POST['parking']) ? 1 : 0;
    $furnished = isset($_POST['furnished']) ? 1 : 0;
    $description = $_POST['description'] ?? NULL;
    $status = $_POST['status'] ?? 'Rent';
    $map_embed = $_POST['map_embed'] ?? NULL;
    $latitude = $_POST['latitude'] ?? NULL;
    $longitude = $_POST['longitude'] ?? NULL;
    $featured = isset($_POST['featured']) ? 1 : 0;

    // ✅ Upload main image
    $image = null;
    if (isset($_FILES['property_image']) && $_FILES['property_image']['error'] == 0) {
        $image = "uploads/" . basename($_FILES['property_image']['name']);
        move_uploaded_file($_FILES['property_image']['tmp_name'], "../" . $image);
    }

    // ✅ Upload floor plan
    $floor_plan = null;
    if (isset($_FILES['floor_plan']) && $_FILES['floor_plan']['error'] == 0) {
        $floor_plan = "uploads/" . basename($_FILES['floor_plan']['name']);
        move_uploaded_file($_FILES['floor_plan']['tmp_name'], "../" . $floor_plan);
    }

    // ✅ Insert property
    $sql = "INSERT INTO properties (
        property_name, location, rent, landlord, image,
        bedrooms, property_type, description, available,
        posted_date, featured, size, bathrooms, floor,
        parking, furnished, map_embed, floor_plan,
        latitude, longitude, status, rental_type
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssdssissisisiissddss",
        $property_name, $location, $rent, $landlord, $image,
        $bedrooms, $property_type, $description, $featured,
        $size, $bathrooms, $floor, $parking, $furnished, $map_embed,
        $floor_plan, $latitude, $longitude, $status, $rental_type
    );

    if (!$stmt->execute()) {
        die("❌ Property insert error: " . $stmt->error);
    }
    $property_id = $stmt->insert_id;

    // ✅ Rent settings
    $include_electricity = isset($_POST['include_electricity']) ? 1 : 0;
    $electricity_bill = $_POST['electricity_bill'] ?? 0;
    $include_water = isset($_POST['include_water']) ? 1 : 0;
    $water_bill = $_POST['water_bill'] ?? 0;
    $include_gas = isset($_POST['include_gas']) ? 1 : 0;
    $gas_bill = $_POST['gas_bill'] ?? 0;
    $include_service = isset($_POST['include_service']) ? 1 : 0;
    $service_charge = $_POST['service_charge'] ?? 0;
    $include_other = isset($_POST['include_other']) ? 1 : 0;
    $other_charges = $_POST['other_charges'] ?? 0;

    $sql2 = "INSERT INTO rent_settings (
        property_id, base_rent,
        include_electricity, electricity_bill,
        include_water, water_bill,
        include_gas, gas_bill,
        include_service, service_charge,
        include_other, other_charges
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param(
        "idididididid",
        $property_id, $rent,
        $include_electricity, $electricity_bill,
        $include_water, $water_bill,
        $include_gas, $gas_bill,
        $include_service, $service_charge,
        $include_other, $other_charges
    );

    if (!$stmt2->execute()) {
        die("❌ Rent settings insert error: " . $stmt2->error);
    }

    // ✅ Multiple property images
    if (isset($_FILES['property_images'])) {
        foreach ($_FILES['property_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['property_images']['error'][$key] == 0) {
                $img_name = "uploads/" . basename($_FILES['property_images']['name'][$key]);
                move_uploaded_file($tmp_name, "../" . $img_name);
                $sql3 = "INSERT INTO property_gallery (property_id, image) VALUES (?, ?)";
                $stmt3 = $conn->prepare($sql3);
                $stmt3->bind_param("is", $property_id, $img_name);
                $stmt3->execute();
            }
        }
    }

    // ✅ Redirect after success
    header("Location: landlord.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Property</title>
  <style>
    body {
  background-color: #333; 
}

.modal {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
}
.modal-content {
  background: #fff;
  border-radius: 14px;
  padding: 30px 25px;
  width: 95%;
  max-width: 520px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.25);
  border-top: 5px solid #ff7f50;
  position: relative;
}
.close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  background: #ff7f50;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 6px 10px;
  cursor: pointer;
  font-weight: 600;
}
.close-btn:hover { background: #e26b3f; }
.modal-content h2 { text-align:center; margin-bottom:15px; }
.modal-content form { display:flex; flex-direction:column; gap:10px; }
.modal-content input, .modal-content select, .modal-content textarea {
  padding:10px; border-radius:8px; border:1px solid #ccc;
}
.modal-content button {
  background:#ff7f50; color:#fff; border:none; padding:10px;
  border-radius:8px; cursor:pointer; font-size:1rem;
}
.modal-content button:hover { background:#e26b3f; }
  </style>
</head>
<body>
<div class="modal">
  <div class="modal-content">
    <!--  Close button -->
    <button class="close-btn" type="button" onclick="window.location.href='landlord.php'">×</button>

    <h2>Add New Property</h2>
    <form action="add_property.php" method="POST" enctype="multipart/form-data">
      <input type="text" name="property_name" placeholder="Property Name" required>
      <input type="text" name="location" placeholder="Location" required>
      <input type="number" name="rent" placeholder="Base Rent" required>
     
      <select name="property_type" required>
  <option value="Apartment" selected>Apartment</option>
  <option value="House">House</option>
  <option value="Studio">Studio</option>
  <option value="Duplex">Duplex</option>
  <option value="Room">Room</option>
  <option value="Office">Office</option>
  <option value="Shop">Shop</option>
</select>


     <h3>Rental Type</h3>
<label><input type="checkbox" name="rental_type[]" value="bachelor"> Bachelor</label>
<label><input type="checkbox" name="rental_type[]" value="family"> Family</label>
<label><input type="checkbox" name="rental_type[]" value="roommate"> Roommate</label>


      <input type="number" name="bedrooms" placeholder="Bedrooms" >
      <input type="number" name="bathrooms" placeholder="Bathrooms" >
      <input type="text" name="size" placeholder="Size (e.g., 1200 sq ft)">
      <input type="text" name="floor" placeholder="Floor (e.g., 2nd)">
      <label><input type="checkbox" name="parking"> Parking Available</label>
      <label><input type="checkbox" name="furnished"> Furnished</label>

      <h3>Included Bills</h3>
      <label><input type="checkbox" name="include_electricity"> Electricity Bill</label>
      <input type="number" name="electricity_bill" placeholder="Amount">
      <label><input type="checkbox" name="include_water"> Water Bill</label>
      <input type="number" name="water_bill" placeholder="Amount">
      <label><input type="checkbox" name="include_gas"> Gas Bill</label>
      <input type="number" name="gas_bill" placeholder="Amount">
      <label><input type="checkbox" name="include_service"> Service Charge</label>
      <input type="number" name="service_charge" placeholder="Amount">
      <label><input type="checkbox" name="include_other"> Other Charges</label>
      <input type="number" name="other_charges" placeholder="Amount">

      <textarea name="description" placeholder="Property Description"></textarea>

      <h3>Status</h3>
      <select name="status" required>
        <option value="Rent" selected>Rent</option>
        <option value="Sell">Sell</option>
      </select>

      <textarea name="map_embed" placeholder="Google Map Embed Code"></textarea>
      <input type="text" name="latitude" placeholder="Latitude">
      <input type="text" name="longitude" placeholder="Longitude">

      <input type="file" name="property_image" accept="image/*" required>
      <input type="file" name="property_images[]" accept="image/*" multiple>
      <input type="file" name="floor_plan" accept="image/*">

      <label><input type="checkbox" name="featured"> Mark as Featured</label>

      <button type="submit">Add Property</button>
    </form>
  </div>
</div>
</body>
</html>

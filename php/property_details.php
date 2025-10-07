<?php
include '../php/config.php';

if (!isset($_GET['id'])) {
    header("Location: properties.php");
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM properties WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Property not found!";
    exit;
}

$property = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($property['property_name']); ?></title>
<link rel="stylesheet" href="../css/property_details.css">
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="bar">
</div>


<div class="det-container">
  <!-- Title -->
  <h1><i class="fa-solid fa-building"></i> <?php echo htmlspecialchars($property['property_name']); ?></h1>

  <!-- Property Main Image -->
  <img src="<?php echo !empty($property['image']) ? '../' . htmlspecialchars($property['image']) : '../uploads/placeholder.jpg'; ?>" alt="Property Image">
 <i class="far fa-heart fav-icon" data-id="<?php echo $id; ?>"></i>
  <!-- Rent Info -->
  <p><i class="fa-solid fa-dollar-sign"></i> <strong>Rent:</strong> $<?php echo htmlspecialchars($property['rent']); ?> / month</p>

  <!-- Buttons -->
  <div class="rent-row">
   <!-- Rent Now Button Trigger -->
<button class="rent-btn" onclick="showRentalForm()"><i class="fa-solid fa-key"></i> Rent Now</button>

<!-- Rental Form (Initially Hidden) -->
<div id="rentalForm" style="display: none;">
  <h3>Rental Application Form</h3>
  <form action="submit_rental.php" method="POST">
    <!-- Hidden Inputs -->
    <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($property['id']); ?>">
    <input type="hidden" name="property_name" value="<?php echo htmlspecialchars($property['property_name']); ?>">

    <!-- Tenant Information -->
    <label for="tenant_name">Full Name</label>
    <input type="text" name="tenant_name" required placeholder="Enter your full name">
    
    <label for="tenant_email">Email Address</label>
    <input type="email" name="tenant_email" required placeholder="Enter your email address">
    
    <label for="tenant_phone">Phone Number</label>
    <input type="tel" name="tenant_phone" required placeholder="Enter your phone number">
    
    <label for="national_id">National ID or Passport Number (Optional)</label>
    <input type="text" name="national_id" placeholder="Enter ID or Passport number">

    <!-- Rental Details -->
    <label for="move_in_date">Desired Move-in Date</label>
    <input type="date" name="move_in_date" required>

    <label for="rental_period">Rental Period (in months)</label>
    <input type="number" name="rental_period" required min="1">

    <label for="payment_method">Preferred Payment Method</label>
    <select name="payment_method">
      <option value="bank_transfer">Bank Transfer</option>
      <option value="cash">Cash</option>
      <option value="mobile_payment">Mobile Payment</option>
    </select>

    <!-- Address Confirmation -->
    <label for="current_address">Current Address (Optional)</label>
    <input type="text" name="current_address" placeholder="Enter your current address">

    <label for="emergency_contact">Emergency Contact Name & Phone</label>
    <input type="text" name="emergency_contact" required placeholder="Enter emergency contact details">

    <!-- Additional Notes -->
    <label for="notes">Additional Notes (Optional)</label>
    <textarea name="notes" placeholder="Any special requests or requirements"></textarea>

    <!-- Agreement Checkbox -->
    <label>
      <input type="checkbox" name="terms" required> I agree to the <a href="terms.html" target="_blank">terms and conditions</a>.
    </label>

    <!-- Submit Button -->
    <button type="submit" class="rent-btn"><i class="fa-solid fa-paper-plane"></i> Submit Rental Request</button>
  </form>
</div>

    <a href="properties.php"><button class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Listings</button></a>
  </div>

  <!-- Property Info Section -->
  <div class="details-grid">

    <p><i class="fa-solid fa-location-dot"></i> <strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>

    <p><i class="fa-solid fa-user"></i> <strong>Landlord:</strong> <?php echo htmlspecialchars($property['landlord']); ?></p>

    <p><i class="fa-solid fa-bed"></i> <strong>Bedrooms:</strong> <?php echo htmlspecialchars($property['bedrooms']); ?></p>

    <p><i class="fa-solid fa-bath"></i> <strong>Bathrooms:</strong> <?php echo htmlspecialchars($property['bathrooms']); ?></p>

    <p><i class="fa-solid fa-house"></i> <strong>Property Type:</strong> <?php echo htmlspecialchars($property['property_type']); ?></p>

    <p><i class="fa-solid fa-ruler-combined"></i> <strong>Size:</strong> <?php echo htmlspecialchars($property['size']); ?></p>

    <p><i class="fa-solid fa-layer-group"></i> <strong>Floor:</strong> <?php echo htmlspecialchars($property['floor']); ?></p>

    <p><i class="fa-solid fa-square-parking"></i> <strong>Parking:</strong> <?php echo $property['parking'] ? 'Yes' : 'No'; ?></p>

    <p><i class="fa-solid fa-couch"></i> <strong>Furnished:</strong> <?php echo $property['furnished'] ? 'Yes' : 'No'; ?></p>

    <p><i class="fa-solid fa-star"></i> <strong>Featured:</strong> <?php echo $property['featured'] ? 'Yes' : 'No'; ?></p>

    <p><i class="fa-solid fa-circle-check"></i> <strong>Available:</strong> <?php echo $property['available'] ? 'Yes' : 'No'; ?></p>

    <p><i class="fa-solid fa-calendar-days"></i> <strong>Posted Date:</strong> <?php echo htmlspecialchars($property['posted_date']); ?></p>

    

  </div>

  <!-- Description -->
  <div class="description">
    <h3><i class="fa-solid fa-info-circle"></i> Overview</h3>
    <p><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
  </div>

  <!-- Floor Plan -->
  <?php if (!empty($property['floor_plan'])): ?>
    <h3><i class="fa-solid fa-diagram-project"></i> Floor Plan</h3>
    <img src="<?php echo '../' . htmlspecialchars($property['floor_plan']); ?>" alt="Floor Plan">
  <?php endif; ?>

  <!-- Map -->
  <?php if (!empty($property['map_embed'])): ?>
    <h3><i class="fa-solid fa-map-location-dot"></i> Location Map</h3>
    <div class="map-container">
      <?php echo $property['map_embed']; ?>
    </div>
  <?php endif; ?>
</div></div>

<!-- Footer -->
<div id="footer"></div>
<script>
  fetch('../html/footer.html')
    .then(res => res.text())
    .then(data => document.getElementById('footer').innerHTML = data);
    fetch('../html/bar.html')
    .then(res => res.text())
    .then(data => document.getElementById('bar').innerHTML = data);
    function showRentalForm() {
 
  const form = document.getElementById('rentalForm');
  form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>

</body>
</html>

<?php
include 'config.php';
include 'bar.php';

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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

.pending-requests {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: rgba(240, 240, 240, 0.25);
  color: #444;
  border: 1px solid rgba(180, 180, 180, 0.3);
  border-radius: 25px;
  padding: 8px 16px;
  width: fit-content;
  margin: 15px auto 0;
  font-size: 15px;
  font-weight: 500;
  backdrop-filter: blur(6px);
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.pending-requests i {
  color: #777;
  animation: rotateClock 2s linear infinite;
}

@keyframes rotateClock {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

  </style>

</head>
<body>
<div class="detcon">

  <!-- Title -->
  <h1 style="color:#333;"><i class="fa-solid fa-building"></i> <?php echo htmlspecialchars($property['property_name']); ?></h1>

  <!-- Main Image -->
  <img src="<?php echo !empty($property['image']) ? '../' . htmlspecialchars($property['image']) : '../uploads/placeholder.jpg'; ?>" alt="Property Image">
  <i class="far fa-heart fav-icon" data-id="<?php echo $id; ?>"></i>

  <!-- Rent -->
  <p><i class="fa-solid fa-dollar-sign"></i> <strong>Rent:</strong> $<?php echo htmlspecialchars($property['rent']); ?> / month</p>

  <!-- Buttons -->
  <div class="rent-row">
    <button class="rent-btn" onclick="showRentalForm()"><i class="fa-solid fa-key"></i> Rent Now</button>
    <a href="properties.php"><button class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Listings</button></a>

    <!-- Rental Form -->
    <div id="rentalForm" style="display:none;">
      <h3>Rental Application Form</h3>
     <form action="submit_rental.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
    <input type="hidden" name="property_name" value="<?php echo htmlspecialchars($property['property_name']); ?>">

    <label for="tenant_name">Full Name</label>
    <input type="text" name="tenant_name" required>

    <label for="tenant_email">Email</label>
    <input type="email" name="tenant_email" required>

    <label for="national_id">National ID</label>
    <input type="text" name="national_id" required>

    <label for="tenant_phone">Phone</label>
    <input type="tel" name="tenant_phone" required>

    <label for="current_address">Current Address</label>
    <textarea name="current_address" rows="2"></textarea>

    <label for="emergency_contact">Emergency Contact</label>
    <input type="text" name="emergency_contact" required>

    <label for="notes">Notes</label>
    <textarea name="notes" rows="2"></textarea>

    <label for="move_in_date">Desired Move-in Date</label>
    <input type="date" name="move_in_date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>


    <label for="payment_method">Payment Method</label>
    <select name="payment_method">
        <option value="bank_transfer">Bank Transfer</option>
        <option value="cash">Cash</option>
        <option value="mobile_payment">Mobile Payment</option>
    </select>

    <label for="pdf_file">Upload PDF</label>
    <input type="file" name="pdf_file" accept="application/pdf" required>

    <label>
        <input type="checkbox" name="terms" required> I agree to <a href="terms.html" target="_blank">terms and conditions</a>
    </label>

    <button type="submit" class="rent-btn">
        <i class="fa-solid fa-paper-plane"></i> Submit Rental Request
    </button>
</form>

    </div>
  </div>


  <!-- Property Info Grid -->
  <div class="details-grid">
<div class="pending-requests">
  <i class="fa-regular fa-clock"></i>
  <?php
  $pid = $property['id'];
  $q = $conn->prepare("SELECT COUNT(*) AS c FROM rental_requests WHERE property_id=? AND status='pending'");
  $q->bind_param("i", $pid);
  $q->execute();
  $pending = $q->get_result()->fetch_assoc()['c'];
  echo "<span>$pending Pending Requests</span>";
  ?>
</div>


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
    <p><i class="fa-solid fa-person"></i> <strong>Rental Type:</strong> <?php echo htmlspecialchars($property['rental_type']); ?></p>
  </div>

  <!-- Description -->
  <div class="description">
    <h3><i class="fa-solid fa-info-circle"></i> Overview</h3>
    <p><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
  </div>

  <!-- Floor Plan -->
  <?php if(!empty($property['floor_plan'])): ?>
    <h3><i class="fa-solid fa-diagram-project"></i> Floor Plan</h3>
    <img src="<?php echo '../' . htmlspecialchars($property['floor_plan']); ?>" alt="Floor Plan">
  <?php endif; ?>

  <!-- Map -->
  <?php if(!empty($property['map_embed'])): ?>
    <h3><i class="fa-solid fa-map-location-dot"></i> Location Map</h3>
    <div class="map-container"><?php echo $property['map_embed']; ?></div>
  <?php endif; ?>

 <a href="rentcalculator.php?id=<?php echo $property['id']; ?>" style="text-decoration:none">View Your Total Rent!</a>

</div>

<!-- Footer -->
<div id="footer"></div>
<script>
fetch('../html/footer.html')
  .then(res => res.text())
  .then(data => document.getElementById('footer').innerHTML = data);

function showRentalForm() {
    const form = document.getElementById('rentalForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>

</body>
</html>

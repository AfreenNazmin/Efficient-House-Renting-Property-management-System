<?php
include 'config.php';
include 'bar.php';
include 'property_card.php'; 

// Fetch all properties (available or not)
$sql = "SELECT * FROM properties ORDER BY posted_date DESC";
$result = $conn->query($sql);

// Fetch favourite property ids for the logged-in user
$fav_ids = [];
if (isset($_SESSION['user_id'])) {
    $res = $conn->query("SELECT property_id FROM favourites WHERE tenant_id=" . $_SESSION['user_id']);
    while ($r = $res->fetch_assoc()) $fav_ids[] = $r['property_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Properties</title>
  <link rel="stylesheet" href="../css/properties.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<div id="bar"></div>

<section class="properties-header">
  <div class="container">
    <h1>All Properties</h1>
    <p>Browse all our property listings.</p>
  </div>
</section>

<section class="properties-list">
  <div class="container">
    <div class="property-grid">
      <?php
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              renderPropertyCard($row, $fav_ids); // call reusable card
          }
      } else {
          echo '<p>No properties found.</p>';
      }
      ?>
    </div>
  </div>
</section>

<div id="footer"></div>

<!-- Navbar & Footer JS -->
<script>
  

  fetch('../html/footer.html')
    .then(res => res.text())
    .then(data => document.getElementById('footer').innerHTML = data);

document.querySelectorAll('.fav-icon').forEach(icon => {
  icon.addEventListener('click', () => {
    const id = icon.dataset.id;
    <?php if (!isset($_SESSION['user_id'])): ?>
      alert('Please login first to add favourites!');
    <?php else: ?>
      fetch('../php/add_favourite.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'property_id=' + id
      })
      .then(res => res.text())
      .then(msg => {
        alert(msg);
        icon.classList.toggle('fas');
        icon.classList.toggle('far');
      });
    <?php endif; ?>
  });
});

</script>
</body>
</html>

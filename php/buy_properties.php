<?php
include 'config.php';

$sql = "SELECT * FROM properties WHERE status='Buy'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Properties for Sale</title>
  <link rel="stylesheet" href="../css/buy_property.css">
</head>
<body>
  
   <div class="bar">
</div>

  <div class="det-container">
    <h1>🏠 Properties for Sale</h1>
    <div class="properties-grid">
     <?php if ($result && $result->num_rows > 0): ?>
  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="property-card">
      <img src="<?php echo '../' . htmlspecialchars($row['image']); ?>" alt="">
      <h3><?php echo htmlspecialchars($row['property_name']); ?></h3>
      <p>$<?php echo htmlspecialchars($row['price']); ?></p>
      <p><?php echo htmlspecialchars($row['location']); ?></p>
      <a href="property_details.php?id=<?php echo $row['id']; ?>">View Details</a>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p style="text-align:center; color:#555; font-size:1.1rem;">No properties available for sale.</p>
<?php endif; ?>

    </div>
  </div>

  <div id="footer"></div>
  <script>
    fetch('../html/footer.html')
      .then(res => res.text())
      .then(data => document.getElementById('footer').innerHTML = data);
      fetch('../html/bar.html')
      .then(res => res.text())
      .then(data => document.getElementById('bar').innerHTML = data);
  </script>
</body>
</html>

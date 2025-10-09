<?php
include 'config.php';
include 'bar.php';
include 'property_card.php';

session_start();

$sql = "SELECT * FROM properties WHERE status='Buy'";
$result = $conn->query($sql);

// Get user's favourite property IDs if logged in
$fav_ids = [];
if(isset($_SESSION['user_id'])){
    $res = $conn->query("SELECT property_id FROM favourites WHERE tenant_id=" . $_SESSION['user_id']);
    while($r = $res->fetch_assoc()) $fav_ids[] = $r['property_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Properties for Sale</title>
  <link rel="stylesheet" href="../css/buy_property.css">
</head>
<body>
  
  <div class="bar"></div>

  <div class="det-container">
    <h1>🏠 Properties for Sale</h1>
    <div class="properties-grid">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="cards">
            <?php renderPropertyCard($row, $fav_ids); ?>
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
  </script>
</body>
</html>

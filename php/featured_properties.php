<?php
include 'config.php';

// Fetch only featured properties
$sql = "SELECT * FROM properties WHERE featured = 1 ORDER BY id DESC LIMIT 6";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Corrected CSS link -->
    <link rel="stylesheet" href="../css/buy_property.css">
    <title>Featured Properties</title>
</head>
<body>
 
        <!-- Navigation Bar -->
        <div class="bar">
</div>
   

   <section class="featured-properties">
        <h2>🌟 Featured Properties</h2>
        <div class="properties-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="property-card">
                        <img src="<?php echo '../' . htmlspecialchars($row['image']); ?>" alt="Property Image">
                         <i class="far fa-heart fav-icon" data-id="<?php echo $id; ?>"></i>
                        <h3><?php echo htmlspecialchars($row['property_name']); ?></h3>
                        <p>$<?php echo htmlspecialchars($row['rent']); ?> / month</p>
                        <p><?php echo htmlspecialchars($row['location']); ?></p>
                        <a href="property_details.php?id=<?php echo $row['id']; ?>">View Details</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; color:#555;">No featured properties found.</p>
            <?php endif; ?>
        </div>
    </section>

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
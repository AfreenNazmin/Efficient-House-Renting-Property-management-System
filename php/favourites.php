<?php
session_start();
include 'config.php';

$sql = "SELECT p.* FROM properties p 
        JOIN favourites f ON p.id=f.property_id 
        WHERE f.tenant_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$favourites = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favourite Properties</title>
    <link rel="stylesheet" href="../css/buy_property.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <!-- Top Bar -->
   <div id="bar"></div>

    <section class="featured-properties">
        <h2><i class="far fa-heart"></i>  Your Favourite Properties</h2>
        <div class="properties-grid">
            <?php if (!empty($favourites)): ?>
                <?php foreach ($favourites as $row): ?>
                    <div class="property-card">
                        <img src="<?php echo '../' . htmlspecialchars($row['image']); ?>" alt="Property Image">
                        <i class="fa<?php echo in_array($row['id'], $fav_ids) ? 's' : 'r'; ?> fa-heart"></i>

                        <h3><?php echo htmlspecialchars($row['property_name']); ?></h3>
                        <p>$<?php echo htmlspecialchars($row['rent']); ?> / month</p>
                        <p><?php echo htmlspecialchars($row['location']); ?></p>
                        <a href="property_details.php?id=<?php echo $row['id']; ?>">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; color:#555; margin-top:20px;">You have no favourite properties yet.</p>
            <?php endif; ?>
        </div>
    </section>

    <div id="footer"></div>
    <script>
          fetch('../html/bar.html')
            .then(res => res.text())
            .then(data => document.getElementById('bar').innerHTML = data);
        fetch('../html/footer.html')
            .then(res => res.text())
            .then(data => document.getElementById('footer').innerHTML = data);
    </script>
</body>
</html>

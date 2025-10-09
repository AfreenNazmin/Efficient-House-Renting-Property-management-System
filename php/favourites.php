<?php
session_start();
include 'config.php';
include 'bar.php';
include 'property_card.php';

$sql = "SELECT p.* FROM properties p 
        JOIN favourites f ON p.id=f.property_id 
        WHERE f.tenant_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$favourites = $stmt->get_result();

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
    <title>Favourite Properties</title>
    <link rel="stylesheet" href="../css/buy_property.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<section class="featured-properties">
    <h2><i class="far fa-heart"></i> Your Favourite Properties</h2>
    <div class="properties-grid">
        <?php if ($favourites->num_rows > 0): ?>
            <?php while ($row = $favourites->fetch_assoc()): ?>
                <?php renderPropertyCard($row, $fav_ids); ?>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; color:#555;">You have no favourite property yet.</p>
        <?php endif; ?>
    </div>
</section>

<div id="footer"></div>
<script>
fetch('../html/footer.html')
    .then(res => res.text())
    .then(data => document.getElementById('footer').innerHTML = data);
</script>

</body>
</html>

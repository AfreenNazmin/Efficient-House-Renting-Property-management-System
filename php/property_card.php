<?php
function renderPropertyCard($property, $fav_ids = []) {
    $image = !empty($property['image']) ? '../' . htmlspecialchars($property['image']) : '../uploads/default.jpg';
    $property_name = !empty($property['property_name']) ? htmlspecialchars($property['property_name']) : 'Unnamed';
    $rent = !empty($property['rent']) ? htmlspecialchars($property['rent']) : '0';
    $location = !empty($property['location']) ? htmlspecialchars($property['location']) : 'Unknown';
    $bedrooms = !empty($property['bedrooms']) ? htmlspecialchars($property['bedrooms']) : '0';
    $bathrooms = !empty($property['bathrooms']) ? htmlspecialchars($property['bathrooms']) : '0';
    $property_type = !empty($property['property_type']) ? htmlspecialchars($property['property_type']) : 'N/A';
    $id = (int)$property['id'];
    $is_fav = in_array($id, $fav_ids);
    ?>
    <div class="property-card">
        <img src="<?= $image ?>" alt="<?= $property_name ?>">
        <i class="fa<?= $is_fav ? 's' : 'r'; ?> fa-heart fav-icon" data-id="<?= $id ?>"></i>
        <h3><?= $property_name ?></h3>
        <p><strong>$<?= $rent ?> / month</strong></p>
        <p><i class="fa-solid fa-location-dot"></i> <?= $location ?></p>
        <p>
            <i class="fa-solid fa-bed"></i> <?= $bedrooms ?> 
            <i class="fa-solid fa-bath"></i> <?= $bathrooms ?> 
            <i class="fa-solid fa-building"></i> <?= $property_type ?>
        </p>
        <div class="card-buttons">
            <a href="property_details.php?id=<?= $id ?>"><button class="rent-btn">Rent Now</button></a>
            <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($location) ?>" target="_blank">
                <button class="view-map">View on Map</button>
            </a>
        </div>
    </div>
    <?php
}
?>

<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<style>
.property-card {
  position: relative;
  background: #2b2b2b;
  border-radius: 10px;
  overflow: hidden;
  width: 300px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.4);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.property-card:hover { transform: translateY(-5px); box-shadow:0 8px 25px rgba(0,0,0,0.6);}
.property-card img { width: 100%; height:200px; object-fit:cover; border-bottom:1px solid #444; }
.fav-icon { position:absolute; top:210px; right:12px; font-size:1.4rem; color:#f0a500; cursor:pointer; transition: transform 0.3s; }
.fav-icon:hover { transform:scale(1.2); }
.property-card h3 { margin:15px; font-size:1.3rem; color:#fff; }
.property-card p { margin:0 15px 10px 15px; color:#ccc; font-size:0.95rem; }
.property-card i { color:#ffdd57; margin-right:5px; }
.property-card button { margin:10px 15px 15px 15px; padding:10px 15px; border:none; border-radius:6px; background:#ff5722; color:#fff; font-weight:bold; cursor:pointer; transition:background 0.3s; }
.property-card button:hover { background:#e64a19; }
</style>

<script>
// Wait until DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.fav-icon').forEach(icon => {
        icon.addEventListener('click', () => {
            const id = icon.dataset.id;

            <?php if (!isset($_SESSION['user_id'])): ?>
                alert('Please login first to add favourites!');
                return;
            <?php else: ?>
                fetch('../php/add_favourite.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'property_id=' + id
                })
                .then(res => res.text())
                .then(msg => {
                    alert(msg);
                    // Toggle heart class
                    icon.classList.toggle('fas');
                    icon.classList.toggle('far');
                });
            <?php endif; ?>
        });
    });
});
</script>

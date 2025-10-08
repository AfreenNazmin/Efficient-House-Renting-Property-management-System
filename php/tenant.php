
<?php
session_start();
if(!isset($_SESSION['user_id']) && !isset($_SESSION['username'])){
    header("Location: ../php/login.php");
    exit();
}

include 'config.php';


if(isset($_SESSION['user_id'])){
    $tenant_id = (int)$_SESSION['user_id'];
} else {
    $sql_tmp = "SELECT id FROM users WHERE username = ?";
    $stmp = $conn->prepare($sql_tmp);
    $stmp->bind_param("s", $_SESSION['username']);
    $stmp->execute();
    $res_tmp = $stmp->get_result()->fetch_assoc();
    $tenant_id = (int)$res_tmp['id'];
    $stmp->close();
}

// fetch tenant info
$sql_user = "SELECT id, name, email FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $tenant_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();


$where = ["available = 1"]; 
$types = "";
$params = [];


$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$min_rent = isset($_GET['min_rent']) ? (int)$_GET['min_rent'] : 0;
$max_rent = isset($_GET['max_rent']) ? (int)$_GET['max_rent'] : 0;
$bedrooms = isset($_GET['bedrooms']) ? (int)$_GET['bedrooms'] : 0;
$ptype = isset($_GET['ptype']) ? trim($_GET['ptype']) : '';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if($location !== ""){
    $where[] = "location LIKE ?";
    $types .= "s"; $params[] = "%$location%";
}
if($q !== ""){
    $where[] = "(property_name LIKE ? OR description LIKE ?)";
    $types .= "ss"; $params[] = "%$q%"; $params[] = "%$q%";
}
if($min_rent > 0){
    $where[] = "rent >= ?";
    $types .= "i"; $params[] = $min_rent;
}
if($max_rent > 0){
    $where[] = "rent <= ?";
    $types .= "i"; $params[] = $max_rent;
}
if($bedrooms > 0){
    $where[] = "bedrooms = ?";
    $types .= "i"; $params[] = $bedrooms;
}
if($ptype !== ""){
    $where[] = "property_type = ?";
    $types .= "s"; $params[] = $ptype;
}

$sql_props = "SELECT id, property_name, location, rent, image, bedrooms, property_type, description FROM properties WHERE " . implode(" AND ", $where) . " ORDER BY rent ASC";
$stmt_props = $conn->prepare($sql_props);

if($types !== ""){
   
    $bind_names[] = $types;
    for ($i=0; $i<count($params); $i++) {
        $bind_names[] = &$params[$i];
    }
    call_user_func_array([$stmt_props, 'bind_param'], $bind_names);
}
$stmt_props->execute();
$properties = $stmt_props->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tenant Dashboard</title>
  
  <link rel="stylesheet" href="../css/landlord.css">
  <style>

    .search-bar { display:flex; gap:8px; margin-bottom:18px; align-items:center; flex-wrap:wrap; }
    .search-bar input, .search-bar select { padding:8px 10px; border-radius:6px; border:1px solid #ccc; }
    .rent-btn { background:#2d9cdb; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
    .rent-btn:hover { background:#2389c4; }
    .msg-success { background:#e6ffed; border-left:4px solid #35b36b; padding:10px; margin-bottom:12px; color:#0b6b3a; }
    .msg-error { background:#ffecec; border-left:4px solid #d9534f; padding:10px; margin-bottom:12px; color:#7a2020; }
  </style>
</head>
<body>

<header class="header">
  <div class="logo">HouseRent</div>
  <nav class="nav">
    <a href="index.php">Home</a>
    <a href="tenant.php" class="active">Dashboard</a>
    <a href="logout.php" onclick="return confirmLogout();">Logout</a>
  </nav>
</header>

<div class="dashboard-container">
  <aside class="sidebar">
  <ul>
    <li><a href="#explore">Explore</a></li>
    <li><a href="#my-rentals">My Rentals</a></li>
    <li><a href="#messages">Messages</a></li>
    <li><a href="#my-reviews">My Reviews</a></li>
    <li><a href="#profile">Profile</a></li>
    <li><a href="map.php">Map</a></li> <!-- নতুন Map option -->
  </ul>
</aside>


  <main class="dashboard-content">
    <h1>Welcome, <?php echo htmlspecialchars($user['name'] ?? 'Tenant'); ?>!</h1>

    <?php if(isset($_GET['msg']) && $_GET['msg'] === 'rented'): ?>
      <div class="msg-success">Property rented successfully. Check <a href="#my-rentals">My Rentals</a>.</div>
    <?php endif; ?>
    <?php if(isset($_GET['err'])): ?>
      <div class="msg-error"><?php echo htmlspecialchars($_GET['err']); ?></div>
    <?php endif; ?>

    

  <h2>Search Properties</h2>
  <form method="GET" class="search-bar" id="searchForm">
    <input name="q" placeholder="Keyword (name, feature)..." value="<?php echo htmlspecialchars($q); ?>">
    <input name="location" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
    <input type="number" name="min_rent" placeholder="Min rent" min="0" value="<?php echo $min_rent ? $min_rent : ''; ?>">
    <input type="number" name="max_rent" placeholder="Max rent" min="0" value="<?php echo $max_rent ? $max_rent : ''; ?>">
    <select name="bedrooms">
      <option value="">Bedrooms</option>
      <option value="1" <?php if($bedrooms===1) echo 'selected'; ?>>1</option>
      <option value="2" <?php if($bedrooms===2) echo 'selected'; ?>>2</option>
      <option value="3" <?php if($bedrooms===3) echo 'selected'; ?>>3</option>
      <option value="4" <?php if($bedrooms===4) echo 'selected'; ?>>4</option>
    </select>
    <select name="ptype">
      <option value="">Type</option>
      <option value="Apartment" <?php if($ptype==='Apartment') echo 'selected'; ?>>Apartment</option>
      <option value="House" <?php if($ptype==='House') echo 'selected'; ?>>House</option>
      <option value="Studio" <?php if($ptype==='Studio') echo 'selected'; ?>>Studio</option>
    </select>
    <button type="submit" class="rent-btn">Search</button>
    <a href="tenant.php" class="rent-btn" style="background:#aaa">Reset</a>
  </form>

  <div class="cards">
    <?php if($properties->num_rows > 0): ?>
      <?php while($p = $properties->fetch_assoc()): ?>
        <div class="card">
          <?php if(!empty($p['image'])): ?>
            <img src="../<?php echo htmlspecialchars($p['image']); ?>" alt="img">
          <?php endif; ?>
          <h3><?php echo htmlspecialchars($p['property_name']); ?></h3>
          <p><?php echo htmlspecialchars($p['location']); ?></p>
          <p><strong>$<?php echo htmlspecialchars($p['rent']); ?> / month</strong></p>
          <p><?php echo htmlspecialchars($p['bedrooms']); ?> bed — <?php echo htmlspecialchars($p['property_type']); ?></p>
          <p style="min-height:36px;"><?php echo htmlspecialchars(mb_strimwidth($p['description'], 0, 120, '...')); ?></p>

          <!-- Rent Now Button -->
          <button class="rent-btn rentNowBtn" 
                  data-id="<?php echo (int)$p['id']; ?>" 
                  data-name="<?php echo htmlspecialchars($p['property_name']); ?>" 
                  data-rent="<?php echo htmlspecialchars($p['rent']); ?>">
            Rent Now
          </button>

    

<a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($p['location']); ?>" 
   target="_blank" 
   class="rent-btn" 
   style="background:#27ae60; margin-top:4px; display:inline-block;">
   View on Map
</a>


        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No properties found for your filters.</p>
    <?php endif; ?>
  </div>
</section>

    
    <section id="my-rentals" class="cards-section">
      <h2>My Rentals</h2>
      <div class="cards">
        <?php
        $sql_my = "SELECT r.id, p.property_name, p.location, p.rent, r.start_date, r.end_date, r.status FROM rentals r JOIN properties p ON r.property_id = p.id WHERE r.tenant_id = ? ORDER BY r.created_at DESC";
        $st = $conn->prepare($sql_my);
        $st->bind_param("i", $tenant_id);
        $st->execute();
        $myr = $st->get_result();
        if($myr->num_rows > 0):
          while($mr = $myr->fetch_assoc()): ?>
            <div class="card">
              <h3><?php echo htmlspecialchars($mr['property_name']); ?></h3>
              <p><?php echo htmlspecialchars($mr['location']); ?></p>
              <p>Rent: $<?php echo htmlspecialchars($mr['rent']); ?>/month</p>
              <p>Status: <?php echo htmlspecialchars($mr['status']); ?></p>
              <p>From: <?php echo htmlspecialchars($mr['start_date']); ?> To: <?php echo htmlspecialchars($mr['end_date']); ?></p>
            </div>
        <?php
          endwhile;
        else: ?>
          <p>No rentals yet.</p>
        <?php endif; $st->close(); ?>
      </div>
    </section>

   
  </main>
</div>

<!-- Rent Modal -->
<div id="rentModal" class="modal">
  <div class="modal-content">
    <span class="closeRent">&times;</span>
    <h2>Confirm Rent</h2>
    <form action="../php/rent_now.php" method="POST" id="rentForm">
      <input type="hidden" name="property_id" id="rent_property_id">
      <p id="rent_property_name"></p>
      <label>Start Date
        <input type="date" name="start_date" id="rent_start" required>
      </label>
      <label>End Date
        <input type="date" name="end_date" id="rent_end" required>
      </label>
      <button type="submit" style="margin-top:12px;" class="rent-btn">Confirm & Pay (simulate)</button>
    </form>
  </div>
</div>

<script>
function confirmLogout(){ return confirm("Logout?"); }

/* Rent modal logic */
const rentModal = document.getElementById('rentModal');
const rentBtns = document.querySelectorAll('.rentNowBtn');
const closeRent = document.querySelector('.closeRent');
const rentIdInput = document.getElementById('rent_property_id');
const rentName = document.getElementById('rent_property_name');
const rentStart = document.getElementById('rent_start');
const rentEnd = document.getElementById('rent_end');

function datePlusDays(d, days){
  const dt = new Date(d);
  dt.setDate(dt.getDate() + days);
  return dt.toISOString().split('T')[0];
}

rentBtns.forEach(b=>{
  b.addEventListener('click', ()=>{
    const id = b.dataset.id;
    const name = b.dataset.name;
    rentIdInput.value = id;
    rentName.innerHTML = `<strong>${name}</strong> — $${b.dataset.rent}/month`;
    // default dates: today -> +30
    const today = new Date().toISOString().split('T')[0];
    rentStart.value = today;
    rentEnd.value = datePlusDays(today, 30);
    rentModal.style.display = 'block';
  });
});

closeRent.onclick = ()=> rentModal.style.display = 'none';
window.onclick = (e)=> { if(e.target == rentModal) rentModal.style.display = 'none'; }
</script>

</body>
</html>

<?php
// close statements & connection
$stmt_user->close();
$stmt_props->close();
$conn->close();
?>

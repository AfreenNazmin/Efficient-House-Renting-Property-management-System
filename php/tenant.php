<?php
session_start();
if(!isset($_SESSION['user_id']) && !isset($_SESSION['username'])){
    header("Location: ../php/login.php");
    exit();
}

include 'config.php';
$allowed_links = ['Home','About','Favourites','Contact','Logout'];

include 'bar.php';
include 'property_card.php';
// fetch tenant id
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

// fetch property types
$sql_types = "SELECT DISTINCT property_type FROM properties ORDER BY property_type ASC";
$res_types = $conn->query($sql_types);
$property_types = [];
while($row = $res_types->fetch_assoc()) {
    $property_types[] = $row['property_type'];
}

// filters
$where = ["available = 1"];
$types = "";
$params = [];

$location = $_GET['location'] ?? '';
$min_rent = (int)($_GET['min_rent'] ?? 0);
$max_rent = (int)($_GET['max_rent'] ?? 0);
$bedrooms = (int)($_GET['bedrooms'] ?? 0);
$ptype = $_GET['ptype'] ?? '';
$q = $_GET['q'] ?? '';

if($location !== "") { $where[] = "location LIKE ?"; $types.="s"; $params[]="%$location%"; }
if($q !== "") { $where[] = "(property_name LIKE ? OR description LIKE ?)"; $types.="ss"; $params[]="%$q%"; $params[]="%$q%"; }
if($min_rent>0) { $where[]="rent >= ?"; $types.="i"; $params[]=$min_rent; }
if($max_rent>0) { $where[]="rent <= ?"; $types.="i"; $params[]=$max_rent; }
if($bedrooms>0) { $where[]="bedrooms = ?"; $types.="i"; $params[]=$bedrooms; }
if($ptype!=="") { $where[]="property_type = ?"; $types.="s"; $params[]=$ptype; }

// fetch properties
$sql_props = "SELECT id, property_name, location, rent, image, bedrooms, property_type, description 
              FROM properties 
              WHERE ".implode(" AND ", $where)." 
              ORDER BY rent ASC";
$stmt_props = $conn->prepare($sql_props);
if($types!==""){
    $bind_names[] = $types;
    for($i=0;$i<count($params);$i++) $bind_names[]=&$params[$i];
    call_user_func_array([$stmt_props,'bind_param'],$bind_names);
}
$stmt_props->execute();
$properties = $stmt_props->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Tenant Dashboard</title>
<link rel="stylesheet" href="../css/landlord.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  .ten-name{

  
    background-color: #333;
    padding: 10px 20px;
    margin: 0;
}

.ten-name h1 {
    margin: 0;
    text-align: right;
}
#advancedFilters {
    display: none;
    flex-direction: column; 
}


</style>
</head>
<body>

<header class="header">
    <div class="logo">HouseRent</div>
  
 </header>
 <div class="ten-name">
 <h1 style="color: #fff; background-color: #333; text-align: right; margin-top: 0px; ">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
</div>
<div class="dashboard-container">
    <aside class="sidebar">
        <ul>
            <li><a href="#explore">Explore</a></li>
            <li><a href="#my-rentals">My Rentals</a></li>
            <li><a href="#messages">Messages</a></li>
            <li><a href="#my-reviews">My Reviews</a></li>
            <li><a href="#profile">Profile</a></li>
            <li><a href="map.php">Map</a></li>
        </ul>
    </aside>

    <main class="dashboard-content">
        <!-- Dashboard Search -->
       <!-- Dashboard Search + Filter -->
<div class="dashboard-search-container" style="display:flex;align-items:center;gap:10px;">
  <form method="GET" class="dashboard-search" style="display:flex;align-items:center;">
      <input type="text" name="q" placeholder="Search..." value="<?php echo htmlspecialchars($q); ?>" style="padding:10px 300px;border-radius:20px;border:1px solid #ccc;">
      <button type="submit" style="margin-left:5px;border:none;background:#2d9cdb;color:#fff;padding:6px 10px;border-radius:5px;"><i class="fa-solid fa-magnifying-glass"></i></button>
  </form>
  <button id="toggleFilter" class="rent-btn" style="padding:6px 10px;"><i class="fa-solid fa-filter"></i></button>
</div>

<div id="advancedFilters" class="filter-panel">
    <form method="GET" class="search-bar">
        <input name="location" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
        <input type="number" name="min_rent" placeholder="Min rent" min="0" value="<?php echo $min_rent ?: ''; ?>">
        <input type="number" name="max_rent" placeholder="Max rent" min="0" value="<?php echo $max_rent ?: ''; ?>">
        <select name="bedrooms">
            <option value="">Bedrooms</option>
            <?php for($i=1;$i<=4;$i++): ?>
                <option value="<?php echo $i;?>" <?php if($bedrooms==$i) echo 'selected'; ?>><?php echo $i;?></option>
            <?php endfor; ?>
        </select>
        <select name="ptype">
            <option value="">Type</option>
            <?php foreach($property_types as $type): ?>
                <option value="<?php echo $type;?>" <?php if($ptype==$type) echo 'selected'; ?>><?php echo $type;?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="rent-btn">Apply</button>
        <a href="tenant.php" class="rent-btn reset-btn">Reset</a>
    </form>
</div>
<?php $fav_ids = [];
if(isset($_SESSION['user_id'])){
    $res = $conn->query("SELECT property_id FROM favourites WHERE tenant_id=" . $_SESSION['user_id']);
    while($r = $res->fetch_assoc()) $fav_ids[] = $r['property_id'];
} ?>
<div class="cards">
    <?php if($properties->num_rows > 0): 
        while($p = $properties->fetch_assoc()): 
            renderPropertyCard($p, $fav_ids); // call property_card.php function
        endwhile; 
    else: ?>
        <p>No properties found.</p>
    <?php endif; ?>
</div>



        <section id="my-rentals">
            <h2>My Rentals</h2>
            <div class="cards">
                <?php
                $sql_my="SELECT r.id, p.property_name, p.location, p.rent, r.start_date, r.end_date, r.status
                         FROM rentals r JOIN properties p ON r.property_id=p.id
                         WHERE r.tenant_id=? ORDER BY r.start_date DESC";
                $st=$conn->prepare($sql_my);
                $st->bind_param("i",$tenant_id);
                $st->execute();
                $myr=$st->get_result();
                if($myr->num_rows>0):
                    while($mr=$myr->fetch_assoc()): ?>
                        <div class="card">
                            <h3><?php echo $mr['property_name'];?></h3>
                            <p><?php echo $mr['location'];?></p>
                            <p>Rent: $<?php echo $mr['rent'];?></p>
                            <p>Status: <?php echo $mr['status'];?></p>
                            <p>From: <?php echo $mr['start_date'];?> To: <?php echo $mr['end_date'];?></p>
                        </div>
                    <?php endwhile;
                else: ?>
                    <p>No rentals yet.</p>
                <?php endif; $st->close(); ?>
            </div>
        </section>

    </main>
</div>

<!-- JS -->
<script>
const filterBtn = document.getElementById('toggleFilter');
const filterPanel = document.getElementById('advancedFilters');

filterBtn.addEventListener('click', ()=>{
    // toggle between none and flex
    filterPanel.style.display = filterPanel.style.display === 'flex' ? 'none' : 'flex';
});




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
include '../html/footer.html';
// close statements & connection
$stmt_user->close();
$stmt_props->close();
$conn->close();
?>

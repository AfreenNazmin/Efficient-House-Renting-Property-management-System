<?php
session_start();
if(!isset($_SESSION['user_id']) && !isset($_SESSION['username'])){
    header("Location: ../php/login.php");
    exit();
}
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Property Map</title>

<link rel="stylesheet" href="../css/landlord.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:0; }
header.header { background:#2d9cdb; color:#fff; padding:12px 20px; display:flex; justify-content:space-between; align-items:center; }
header .logo { font-weight:bold; font-size:20px; }
header .nav a { color:#fff; margin-left:16px; text-decoration:none; font-weight:500; }
header .nav a.active { text-decoration:underline; }
.dashboard-container { display:flex; flex-wrap:wrap; margin:20px; }
.sidebar { flex:1 1 200px; background:#fff; padding:15px; border-radius:8px; box-shadow:0 0 8px rgba(0,0,0,0.1); height:min-content; }
.sidebar ul { list-style:none; padding:0; }
.sidebar ul li { margin-bottom:10px; }
.sidebar ul li a { text-decoration:none; color:#333; font-weight:500; }
.sidebar ul li a.active { color:#2d9cdb; font-weight:bold; }
.dashboard-content { flex:3 1 700px; margin-left:20px; background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
#map { height:500px; width:100%; border-radius:8px; margin-top:12px; }
.search-bar { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:12px; align-items:center; }
.search-bar input { padding:8px 10px; border-radius:6px; border:1px solid #ccc; flex:1; min-width:120px; }
.rent-btn { background:#2d9cdb; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
.rent-btn:hover { background:#2389c4; }
</style>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>

<header class="header">
  <div class="logo">HouseRent</div>
  <nav class="nav">
    <a href="tenant.php">Dashboard</a>
    <a href="logout.php" onclick="return confirm('Logout?')">Logout</a>
  </nav>
</header>

<div class="dashboard-container">
  <aside class="sidebar">
    <ul>
      <li><a href="tenant.php">Dashboard</a></li>
      <li><a href="map.php" class="active">Map</a></li>
    </ul>
  </aside>

  <main class="dashboard-content">
    <h1>Find Properties on Map</h1>

    <div class="search-bar">
      <input type="text" id="destination" placeholder="Enter destination (e.g., Chandpur)">
      <button id="searchBtn" class="rent-btn">Search</button>
    </div>

    <div id="map"></div>
  </main>
</div>

<script>
let map, userLat, userLng, allProperties = [];
let propertyMarkers = [];
let destMarker = null;

// Initialize map centered on Bangladesh
userLat = 23.6850;
userLng = 90.3563;
map = L.map('map').setView([userLat, userLng], 7);

// Map tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Current location marker
if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(pos=>{
        userLat = pos.coords.latitude;
        userLng = pos.coords.longitude;
        L.marker([userLat, userLng]).addTo(map)
         .bindPopup("You are here").openPopup();
    });
}

// Fetch properties from server
function loadProperties(){
    fetch('get_properties.php')
    .then(res=>res.json())
    .then(data=>{
        allProperties = data;
        showPropertyMarkers();
    });
}

// Show property markers
function showPropertyMarkers(){
    // Remove previous property markers
    propertyMarkers.forEach(m => map.removeLayer(m));
    propertyMarkers = [];

    allProperties.forEach(p=>{
        let m = L.marker([p.latitude, p.longitude])
            .addTo(map)
            .bindPopup(`<b>${p.property_name}</b><br>
                        ${p.image ? '<img src="../'+p.image+'" style="width:120px;height:80px;object-fit:cover;"><br>': ''}
                        Rent: ${p.rent} TK<br>
                        Bedrooms: ${p.bedrooms}<br>
                        Type: ${p.property_type}<br>
                        Location: ${p.location}`);
        propertyMarkers.push(m);
    });
}

// Search destination
document.getElementById('searchBtn').addEventListener('click', ()=>{
    const dest = document.getElementById('destination').value.trim();
    if(!dest) return alert('Please enter a location');

    // Remove previous destination marker
    if(destMarker) map.removeLayer(destMarker);

    // Use OpenStreetMap Nominatim API
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(dest)},Bangladesh`)
    .then(res=>res.json())
    .then(data=>{
        if(data.length === 0) return alert('Destination not found in Bangladesh');

        let lat = parseFloat(data[0].lat);
        let lng = parseFloat(data[0].lon);

        // Add red marker for destination
        destMarker = L.marker([lat, lng], {
            icon: L.icon({
                iconUrl:'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                iconSize:[30,30],
                iconAnchor:[15,30]
            })
        }).addTo(map).bindPopup(`<b>${dest}</b>`).openPopup();

        // Fit map to show user + destination
        let bounds = L.latLngBounds([
            [userLat, userLng],
            [lat, lng]
        ]);
        map.fitBounds(bounds, {padding:[50,50]});
    });
});

// Initial load
loadProperties();
</script>

</body>
</html>

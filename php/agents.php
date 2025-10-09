<?php
include 'bar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/buy_property.css">
</head>
<body>
    
    <section class="agents-section">
    <h2>🏡 Meet Our Agents</h2>
    <div class="agents-grid">
        <!-- Example Agent Card -->
        <div class="agent-card">
            <img src="../images/agent1.jpg" alt="Agent Photo">
            <h3>John Doe</h3>
            <p>Rental Specialist</p>
            <p>📞 +123 456 7890</p>
            <p>✉ john@example.com</p>
            <a href="agent_profile.php?id=1">View Profile</a>
        </div>

        <div class="agent-card">
            <img src="../images/agent2.jpg" alt="Agent Photo">
            <h3>Jane Smith</h3>
            <p>Sales Expert</p>
            <p>📞 +123 987 6540</p>
            <p>✉ jane@example.com</p>
            <a href="agent_profile.php?id=2">View Profile</a>
        </div>

        <!-- Add more agent cards as needed -->
    </div>
</section>
 <div id="footer">

    </div>
<script>
    fetch('footer.html')
  .then(res => res.text())
  .then(data => document.getElementById('footer').innerHTML = data);
fetch('navbar.html')  
  .then(res => res.text())
  .then(data => document.getElementById('navbar').innerHTML = data);
</script>
</body>
</html>
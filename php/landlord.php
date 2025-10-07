<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: ../php/login.php");
    exit();
}

include 'config.php';

// Fetch landlord's properties
$landlord = $_SESSION['username'];
$sql = "SELECT * FROM properties WHERE landlord = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $landlord);
$stmt->execute();
$result = $stmt->get_result();

// Fetch rentals
$sql_rentals = "SELECT r.id AS rental_id, p.property_name, p.location, p.rent, u.name AS tenant_name, r.start_date, r.end_date, r.status
                FROM rentals r
                JOIN properties p ON r.property_id = p.id
                JOIN users u ON r.tenant_id = u.id
                WHERE p.landlord = ?";
$stmt2 = $conn->prepare($sql_rentals);
$stmt2->bind_param("s", $landlord);
$stmt2->execute();
$rentals = $stmt2->get_result();

// Fetch reviews
$sql_reviews = "SELECT r.id AS review_id, r.rating, r.comment, r.created_at, p.property_name, u.name AS tenant_name
                FROM reviews r
                JOIN properties p ON r.property_id = p.id
                JOIN users u ON r.tenant_id = u.id
                WHERE p.landlord = ?
                ORDER BY r.created_at DESC";
$stmt3 = $conn->prepare($sql_reviews);
$stmt3->bind_param("s", $landlord);
$stmt3->execute();
$reviews = $stmt3->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Landlord Dashboard</title>
<link rel="stylesheet" href="../css/landlord.css">
</head>
<body>

<header class="header">
  <div class="logo">HouseRent</div>
  <nav class="nav">
    <a href="index.php">Home</a>
    <a href="landlord.php" class="active">Dashboard</a>
    <a href="tenants.php">Tenants</a>
    <a href="logout.php" onclick="return confirm('Are you really sure you want to logout?');">Logout</a>
  </nav>
</header>

<div class="dashboard-container">
  <aside class="sidebar">
    <ul>
      <li><a href="#my-properties">My Properties</a></li>
      <li><a href="#" id="openModal">Add Property</a></li>
      <li><a href="#rentals">Rentals</a></li>
      <li><a href="#messages">Messages</a></li>
      <li><a href="#reviews">Reviews</a></li>
    </ul>
  </aside>

  <main class="dashboard-content">
    <h1>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

    <!-- My Properties Section -->
    <section id="my-properties" class="cards-section">
      <h2>My Properties</h2>
      <div class="cards">
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="card">
            <?php if(!empty($row['image'])): ?>
              <img src="../<?php echo htmlspecialchars($row['image']); ?>" alt="Property Image">
            <?php else: ?>
              <img src="../images/placeholder.jpg" alt="No Image">
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($row['property_name']); ?></h3>
            <p>Location: <?php echo htmlspecialchars($row['location']); ?></p>
            <p>Rent: $<?php echo htmlspecialchars($row['rent']); ?>/month</p>
            <p>Bedrooms: <?php echo htmlspecialchars($row['bedrooms']); ?></p>
            <p>Type: <?php echo htmlspecialchars($row['property_type']); ?></p>
          </div>
        <?php endwhile; ?>
      </div>
    </section>

<!-- Add Property Modal -->
<div id="propertyModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Add New Property</h2>
    <form action="../php/add_property.php" method="POST" enctype="multipart/form-data">
      <input type="text" name="property_name" placeholder="Property Name" required>
      <input type="text" name="location" placeholder="Location" required>
      <input type="number" name="rent" placeholder="Rent Amount" required>
      <input type="file" name="property_image" accept="image/*" required>
      <button type="submit">Add Property</button>
    </form>
  </div>
</div>

    <!-- Rentals Section -->
    <section id="rentals" class="cards-section">
      <h2>My Rentals</h2>
      <div class="cards">
        <?php if($rentals->num_rows > 0): ?>
          <?php while($row = $rentals->fetch_assoc()): ?>
            <div class="card">
              <h3><?php echo htmlspecialchars($row['property_name']); ?></h3>
              <p>Location: <?php echo htmlspecialchars($row['location']); ?></p>
              <p>Tenant: <?php echo htmlspecialchars($row['tenant_name']); ?></p>
              <p>Rent: $<?php echo htmlspecialchars($row['rent']); ?>/month</p>
              <p>Status: <?php echo htmlspecialchars($row['status']); ?></p>
              <p>From: <?php echo $row['start_date']; ?> To: <?php echo $row['end_date']; ?></p>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No rentals found.</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- Messages Section -->
    <section id="messages" class="cards-section">
      <h2>Messages</h2>
      <div id="conversationList"></div>
      <div id="conversationView" style="display:none;">
        <div id="messagesContainer"></div>
        <form id="replyForm">
          <input type="hidden" name="conversation_id" id="convId">
          <textarea name="body" placeholder="Type your reply..."></textarea>
          <button type="submit">Send</button>
        </form>
      </div>
    </section>

    <!-- Reviews Section -->
    <section id="reviews" class="cards-section">
      <h2>Reviews</h2>
      <div class="cards">
        <?php if($reviews->num_rows > 0): ?>
          <?php while($row = $reviews->fetch_assoc()): ?>
            <div class="card">
              <h3><?php echo htmlspecialchars($row['property_name']); ?></h3>
              <p><strong>Tenant:</strong> <?php echo htmlspecialchars($row['tenant_name']); ?></p>
              <p><strong>Rating:</strong> <?php echo str_repeat('⭐', (int)$row['rating']); ?></p>
              <p><?php echo nl2br(htmlspecialchars($row['comment'])); ?></p>
              <p class="review-date"><?php echo date('d M Y', strtotime($row['created_at'])); ?></p>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No reviews yet.</p>
        <?php endif; ?>
      </div>
    </section>

  </main>
</div>

<script>
// Modal JS
const propertyModal = document.getElementById('propertyModal');
const openModalBtn = document.getElementById('openModal');
const closeModalBtn = propertyModal.querySelector('.close');

// Open Add Property Modal
openPropertyBtn.onclick = () => propertyModal.style.display = 'block';
closePropertyBtn.onclick = () => propertyModal.style.display = 'none';

// Open Edit Property Modal
editBtns.forEach(btn => {
    btn.onclick = () => {
        editModal.style.display = 'block';
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_name').value = btn.dataset.name;
        document.getElementById('edit_location').value = btn.dataset.location;
        document.getElementById('edit_rent').value = btn.dataset.rent;
    }
});
closeEditBtn.onclick = () => editModal.style.display = 'none';

// Close modals when clicking outside
window.onclick = (e) => {
    if(e.target == propertyModal) propertyModal.style.display = 'none';
    if(e.target == editModal) editModal.style.display = 'none';
};

// --- Unread Messages Badge ---
async function updateUnreadCount() {
    try {
        const resp = await fetch('../php/unread_count.php');
        const data = await resp.json();
        const msgCountSpan = document.getElementById('msgCount');
        if(msgCountSpan) msgCountSpan.textContent = `(${data.unread})`;
    } catch(err) {
        console.error('Error fetching unread messages:', err);
    }
}
updateUnreadCount(); // initial call
setInterval(updateUnreadCount, 7000);

// --- Reply Form (optional enhancement) ---
const replyForm = document.getElementById('replyForm');
if(replyForm){
    replyForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(replyForm);
        try {
            const resp = await fetch('../php/send_message.php', {
                method: 'POST',
                body: formData
            });
            const data = await resp.json();
            if(data.success){
                document.getElementById('messagesContainer').innerHTML += `<p><strong>You:</strong> ${formData.get('body')}</p>`;
                replyForm.body.value = '';
            } else {
                alert('Failed to send message.');
            }
        } catch(err){
            console.error('Error sending message:', err);
        }
    });
}
</script>

</body>
</html>

<?php 
$stmt->close(); 
$stmt2->close(); 
$stmt3->close(); 
$conn->close(); 
?>

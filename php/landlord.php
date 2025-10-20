<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: ../php/login.php");
    exit();
}

include 'config.php';

$landlord = $_SESSION['username'];

// Fetch landlord's properties
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
<style>
.card .btn-edit,
.card .btn-delete {
    padding: 8px 16px;
    border-radius: 5px;
    font-size: 14px;
    border: none;
    cursor: pointer;
    color: #fff;
    transition: 0.3s;
    margin-right: 8px;
}

.card .btn-edit {
    background-color: #4CAF50;
}

.card .btn-edit:hover {
    background-color: #45a049;
}

.card .btn-delete {
    background-color: #f44336;
}

.card .btn-delete:hover {
    background-color: #da190b;
}

.card .btn-edit { text-decoration: none; }

.toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #4CAF50;
    color: #fff;
    padding: 12px 20px;
    border-radius: 5px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    z-index: 9999;
    opacity: 0.95;
    transition: 0.3s;
}
.toast.error { background: #f44336; }

.cards {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}
.card {
  width: calc(33.33% - 20px);
  background: #fff;
  border-radius: 10px;
  padding: 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
@media (max-width: 900px) {
  .card { width: calc(50% - 20px); }
}
@media (max-width: 600px) {
  .card { width: 100%; }
}
</style>
</head>
<body>

<?php
$allowed_links = ['Home','About','Contact','Logout'];
include 'bar.php';
?>

<div class="dashboard-container">
  <aside class="sidebar">
    <ul>
      <li><a href="#my-properties">My Properties</a></li>
      <li><a href="add_property.php">Add Property</a></li>
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
          <div class="card" id="property-<?php echo $row['id']; ?>">
            <img src="../<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'images/placeholder.jpg'; ?>" alt="Property Image">
            <h3><?php echo htmlspecialchars($row['property_name']); ?></h3>
            <p>Location: <?php echo htmlspecialchars($row['location']); ?></p>
            <p>Rent: $<?php echo htmlspecialchars($row['rent']); ?>/month</p>
            <p>Bedrooms: <?php echo htmlspecialchars($row['bedrooms']); ?></p>
            <p>Type: <?php echo htmlspecialchars($row['property_type']); ?></p>
            <div class="card-buttons" style="display:flex;">
              <a href="edit_property.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
              <button onclick="deleteProperty(<?php echo $row['id']; ?>)" class="btn-delete">Delete</button>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </section>

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
function deleteProperty(id){
  if(confirm("Really want to delete this property?")){
    fetch('delete_property.php?id=' + id)
      .then(res => res.json())
      .then(data => {
        if(data.success){
          const card = document.getElementById('property-' + id);
          if(card) card.remove();
          showToast("Property deleted successfully");
        } else {
          showToast("Error deleting property", true);
        }
      });
  }
}

function showToast(message, isError = false){
  const toast = document.createElement('div');
  toast.className = 'toast' + (isError ? ' error' : '');
  toast.innerText = message;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

// Optional modal code - only if modal exists
const propertyModal = document.getElementById('propertyModal');
if(propertyModal){
  const openModalBtn = document.getElementById('openModal');
  const closeModalBtn = propertyModal.querySelector('.close');
  openModalBtn?.addEventListener('click', ()=> propertyModal.style.display='block');
  closeModalBtn?.addEventListener('click', ()=> propertyModal.style.display='none');
  window.addEventListener('click', e => { if(e.target==propertyModal) propertyModal.style.display='none'; });
}

// Unread Messages Badge
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
updateUnreadCount();
setInterval(updateUnreadCount, 7000);

// Reply Form
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

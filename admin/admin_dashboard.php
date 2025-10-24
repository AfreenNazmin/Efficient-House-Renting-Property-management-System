<?php
include '../php/config.php';

// Query to count total properties
$sql = "SELECT COUNT(*) AS total FROM properties";
$result = $conn->query($sql);
$total = 0;

if ($result && $row = $result->fetch_assoc()) {
    $total = $row['total'];
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">

    <style>
      /* General Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
}

/* Header Styles */
.header {
    background: #333;
    color: #fff;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header .logo h1 {
    margin: 0;
}

.nav a {
    color: #fff;
    margin-left: 15px;
    text-decoration: none;
}

.nav a.active {
    text-decoration: underline;
}

/* Sidebar Styles */
.dashboard-container {
    display: flex;
}

.sidebar {
    width: 220px;
    background: #222;
    color: #fff;
    padding: 20px;
    height: 100vh;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    margin-bottom: 15px;
}

.sidebar ul li a {
    color: #fff;
    text-decoration: none;
}

.sidebar ul li a:hover {
    background-color: #444;
    padding-left: 10px;
    border-radius: 4px;
}

/* Main Content Styles */
.dashboard-content {
    flex: 1;
    padding: 20px;
}

.cards-section .cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.card {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.card h3 {
    font-size: 18px;
    margin-bottom: 10px;
}

.card p {
    font-size: 24px;
    font-weight: bold;
}

.card ul {
    font-size: 16px;
    padding-left: 20px;
}

/* Listings Management Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th, table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

button {
    padding: 8px 12px;
    margin: 5px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #2980b9;
}

h2 {
    font-size: 28px;
    margin-bottom: 20px;
    color: #333;
}

/* Footer Styles */
.footer {
    background-color: #333;
    color: #fff;
    padding: 20px;
    text-align: center;
    margin-top: 40px;
}

.footer .footer-content {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.footer .footer-content p {
    margin-bottom: 10px;
    font-size: 16px;
}

.footer .social-links {
    margin-top: 10px;
}

.footer .social-links a {
    color: #fff;
    margin: 0 10px;
    text-decoration: none;
    font-size: 18px;
}

.footer .social-links a:hover {
    color: #3498db;
}

  
        </style>

</head>
<body>
    <!-- Header -->
    <header class="header">
    <div class="logo">
        <h1>Admin Dashboard</h1>
    </div>
    <nav class="nav">
        <a href="#" class="active">Dashboard Overview</a>
        <a href="#">Active Listings</a>
        <a href="#">Pending Listings</a>
        <a href="../php/featured_prooperties.php">Featured Listings</a>
        <a href="#">Reports</a>
        <a href="#">Notifications</a>
         <a href="add_user.php">add users</a>
    </nav>
</header>


    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Sidebar -->
      <aside class="sidebar">
    <ul>
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Listings Management</a></li>
        <li><a href="#">User Management</a></li>
        <li><a href="#">Messages</a></li>
         <li><a href="pending_users.php">Pwnding Users</a></li>
        <li><a href="#">Settings</a></li>
    </ul>
</aside>


        <!-- Main Content -->
        <div class="dashboard-content">
            <!-- Cards Section -->
            <section class="cards-section">
                <div class="cards">
                    <!-- Stats Cards -->
                    <div class="card">
        <h3>Total Houses Listed</h3>
        <p><?php echo $total; ?></p>
    </div>
                    <div class="card">
                        <h3>Total Users</h3>
                        <p>300</p>
                    </div>
                    <div class="card">
                        <h3>Pending Requests</h3>
                        <p>5</p>
                    </div>
                    <div class="card">
                        <h3>Recent Activities</h3>
                        <ul>
                            <li>New Listing: House 101</li>
                            <li>Message from John</li>
                            <li>Maintenance Request</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Listings Management Table -->
            <section class="listings-management">
                <h2>Listings Management</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Listing ID</th>
                            <th>Property Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>001</td>
                            <td>Luxury Apartment</td>
                            <td>Pending Approval</td>
                            <td>
                                <button>Approve</button>
                                <button>Reject</button>
                            </td>
                        </tr>
                        <tr>
                            <td>002</td>
                            <td>Modern Villa</td>
                            <td>Active</td>
                            <td>
                                <button>Edit</button>
                                <button>Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
    <!-- Footer Section -->
<footer class="footer">
    <div class="footer-content">
        <p>&copy; 2025 Admin Dashboard. All rights reserved.</p>
        <div class="social-links">
            <a href="#" target="_blank">Facebook</a>
            <a href="#" target="_blank">Twitter</a>
            <a href="#" target="_blank">LinkedIn</a>
            <a href="#" target="_blank">Instagram</a>
        </div>
    </div>
</footer>

</body>
</html>

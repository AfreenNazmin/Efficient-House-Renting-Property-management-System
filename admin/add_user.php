<?php
// ================== Database Connection ==================
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "your_database_name"; // change this!

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ================== Handle Form Submission ==================
if (isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $nid_number = $_POST['nid_number'];
    $status = $_POST['status'];

    // Optional defaults
    $is_verified = 0;
    $is_landlord_verified = 0;
    $phoneVerified = 0;

    // Prepare statement (prevents SQL injection)
    $stmt = $conn->prepare("INSERT INTO user (name, email, password, phone, phoneVerified, is_verified, role, nid_number, is_landlord_verified, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiiisis", $name, $email, $password, $phone, $phoneVerified, $is_verified, $role, $nid_number, $is_landlord_verified, $status);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ User added successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

$conn->close();
?>

<!-- ================== HTML Form ================== -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New User</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; display: flex; justify-content: center; padding: 40px; }
        form { background: white; padding: 30px; border-radius: 12px; width: 400px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-top: 10px; font-weight: 600; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; margin-top: 5px; }
        button { margin-top: 15px; width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <form method="POST" action="">
        <h2>Add New User</h2>

        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Phone:</label>
        <input type="text" name="phone">

        <label>Role:</label>
        <select name="role" required>
            <option value="tenant">Tenant</option>
            <option value="landlord">Landlord</option>
            <option value="admin">Admin</option>
        </select>

        <label>NID Number:</label>
        <input type="text" name="nid_number">

        <label>Status:</label>
        <select name="status">
            <option value="pending">Pending</option>
            <option value="active">Active</option>
            <option value="banned">Banned</option>
        </select>

        <button type="submit" name="add_user">Add User</button>
    </form>
</body>
</html>

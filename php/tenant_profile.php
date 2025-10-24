<?php
include 'config.php';
include 'bar.php';
session_start();

$tenant_id = 1; // logged-in tenant example

// Fetch tenant + email, phone, username, is_verified
$sql = "SELECT t.*, u.email, u.phone, u.name AS username, u.is_verified
        FROM tenants t
        JOIN users u ON t.id = u.id
        WHERE t.id = $tenant_id";
$result = $conn->query($sql);
$tenant = null;
if ($result->num_rows > 0) {
    $tenant = $result->fetch_assoc();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $age = $_POST['age'] ?? '';
    $occupation = $_POST['occupation'] ?? '';
    $hobby = $_POST['hobby'] ?? '';
    $pet = $_POST['pet'] ?? '';

    $profile_pic = '';
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $profile_pic = $target_dir . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic);
    }

    $check = $conn->query("SELECT * FROM tenants WHERE id = $tenant_id");
    if ($check->num_rows > 0) {
        $sql = "UPDATE tenants SET 
                full_name='$full_name',
                gender='$gender',
                age='$age',
                occupation='$occupation',
                hobby='$hobby',
                pet='$pet'";
        if ($profile_pic) $sql .= ", profile_pic='$profile_pic'";
        $sql .= " WHERE id=$tenant_id";
        $conn->query($sql);
    } else {
        $sql = "INSERT INTO tenants (id, full_name, gender, age, occupation, hobby, pet, profile_pic) 
                VALUES ($tenant_id,'$full_name','$gender','$age','$occupation','$hobby','$pet','$profile_pic')";
        $conn->query($sql);
    }

    header("Location: tenant_profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tenant Profile</title>
<style>
/* Existing design styles (unchanged) */
body { margin:0; font-family:'Segoe UI',sans-serif; background:#f4f4f4; }
.dashboard-container { display:flex; min-height:100vh; }
.sidebar { width:230px; background:#1f1f1f; color:#fff; padding:20px; flex-shrink:0; }
.sidebar h2 { text-align:center; margin-bottom:20px; }
.sidebar ul { list-style:none; padding:0; margin:0; }
.sidebar ul li { margin:15px 0; }
.sidebar ul li a { color:white; text-decoration:none; display:block; padding:8px 0; }
.sidebar ul li a:hover { color:#ff7f50; }
.dashboard-content { flex:1; padding:40px; display:flex; justify-content:center; align-items:flex-start; }
.profile-card { background:#fff; padding:30px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1); width:100%; max-width:700px; }
.profile-card h2 { margin-top:0; margin-bottom:20px; text-align:center; }
.profile-card img { display:block; margin:0 auto 20px; width:140px; height:140px; border-radius:50%; object-fit:cover; }
.profile-card p { font-size:15px; margin:8px 0; }
.profile-card strong { color:#333; }
form label { display:block; margin-top:10px; font-weight:bold; }
form input, form select { width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:5px; }
button { margin-top:15px; padding:10px 16px; background:#007bff; border:none; color:white; border-radius:5px; cursor:pointer; }
button:hover { background:#0056b3; }
.edit-btn { background:#28a745; }
.edit-btn:hover { background:#1e7e34; }
.email-notice { font-size:14px; color:#a00; margin-bottom:10px; text-align:center; }
.email-notice a { color:#007bff; text-decoration:none; }
.email-notice a:hover { text-decoration:underline; }
</style>
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <h2>Tenant Menu</h2>
        <ul>
            <li><a href="tenant.php">Explore</a></li>
            <li><a href="#rentals">My Rentals</a></li>
            <li><a href="#messages">Messages</a></li>
            <li><a href="#reviews">My Reviews</a></li>
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="profile-card">
            <?php if (isset($_GET['edit']) || !$tenant): ?>
                <h2>Edit / Setup Tenant Profile</h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <label>Profile Picture</label>
                    <input type="file" name="profile_pic" accept="image/*">

                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?= $tenant['full_name'] ?? $tenant['username'] ?>" required>

                    <label>Gender</label>
                    <select name="gender">
                        <option value="">Select</option>
                        <option value="Male" <?= ($tenant['gender'] ?? '')=='Male'?'selected':'' ?>>Male</option>
                        <option value="Female" <?= ($tenant['gender'] ?? '')=='Female'?'selected':'' ?>>Female</option>
                        <option value="Other" <?= ($tenant['gender'] ?? '')=='Other'?'selected':'' ?>>Other</option>
                    </select>

                    <label>Age</label>
                    <input type="number" name="age" value="<?= $tenant['age'] ?? '' ?>">

                    <label>Occupation</label>
                    <input type="text" name="occupation" value="<?= $tenant['occupation'] ?? '' ?>">

                    <label>Hobby</label>
                    <input type="text" name="hobby" value="<?= $tenant['hobby'] ?? '' ?>">

                    <label>Pet</label>
                    <input type="text" name="pet" value="<?= $tenant['pet'] ?? '' ?>">

                    <button type="submit">Save Profile</button>
                </form>
            <?php else: ?>
                <h2>Tenant Profile</h2>
                <?php if (!empty($tenant['profile_pic'])): ?>
                    <img src="<?= $tenant['profile_pic'] ?>" alt="Profile Picture">
                <?php else: ?>
                    <div style="width:140px; height:140px; background:#ccc; border-radius:50%; margin:auto; display:flex; align-items:center; justify-content:center;">No Photo</div>
                <?php endif; ?>

                <p><strong>Full Name:</strong> <?= $tenant['full_name'] ?? $tenant['username'] ?></p>
                <p><strong>Email:</strong> <?= $tenant['email'] ?> 
                    <?php if (!$tenant['is_verified'] ?? 0): ?>
                        <span style="color:#a00;">(Pending Verification) <a href="verify_email.php">Verify Now</a></span>
                    <?php else: ?>
                        <span style="color:green;">(Verified)</span>
                    <?php endif; ?>
                </p>
                <p><strong>Phone:</strong> <?= $tenant['phone'] ?></p>
                <p><strong>Gender:</strong> <?= $tenant['gender'] ?></p>
                <p><strong>Age:</strong> <?= $tenant['age'] ?></p>
                <p><strong>Occupation:</strong> <?= $tenant['occupation'] ?></p>
                <p><strong>Hobby:</strong> <?= $tenant['hobby'] ?></p>
                <p><strong>Pet:</strong> <?= $tenant['pet'] ?></p>

                <a href="?edit=1"><button class="edit-btn">Edit Profile</button></a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../html/footer.html'; ?>
</body>
</html>

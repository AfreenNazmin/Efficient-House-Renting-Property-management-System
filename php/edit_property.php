<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: ../php/login.php");
    exit();
}

include 'config.php';

$landlord = $_SESSION['username'];
$property = null;

// Fetch property data if id is provided
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM properties WHERE id=? AND landlord=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id, $landlord);
    $stmt->execute();
    $property = $stmt->get_result()->fetch_assoc();

    // Fetch rent settings
    $sql2 = "SELECT * FROM rent_settings WHERE property_id=?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $rent_settings = $stmt2->get_result()->fetch_assoc();

    // Fetch gallery images
    $sql3 = "SELECT * FROM property_gallery WHERE property_id=?";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("i", $id);
    $stmt3->execute();
    $gallery = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = $_POST['property_id'];

    // Collect fields
    $property_name = $_POST['property_name'];
    $location = $_POST['location'];
    $rent = $_POST['rent'];
    $bedrooms = $_POST['bedrooms'] ?? 1;
    $bathrooms = $_POST['bathrooms'] ?? 1;
    $size = $_POST['size'] ?? null;
    $floor = $_POST['floor'] ?? null;
    $property_type = $_POST['property_type'] ?? 'Apartment';
    $rental_type = isset($_POST['rental_type']) ? implode(',', $_POST['rental_type']) : 'both';
    $description = $_POST['description'] ?? null;
    $status = $_POST['status'] ?? 'Rent';
    $map_embed = $_POST['map_embed'] ?? null;
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    $available = isset($_POST['available']) ? 1 : 0;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $parking = isset($_POST['parking']) ? 1 : 0;
    $furnished = isset($_POST['furnished']) ? 1 : 0;

    // Rent settings
    $include_electricity = isset($_POST['include_electricity']) ? 1 : 0;
    $electricity_bill = $_POST['electricity_bill'] ?? 0;
    $include_water = isset($_POST['include_water']) ? 1 : 0;
    $water_bill = $_POST['water_bill'] ?? 0;
    $include_gas = isset($_POST['include_gas']) ? 1 : 0;
    $gas_bill = $_POST['gas_bill'] ?? 0;
    $include_service = isset($_POST['include_service']) ? 1 : 0;
    $service_charge = $_POST['service_charge'] ?? 0;
    $include_other = isset($_POST['include_other']) ? 1 : 0;
    $other_charges = $_POST['other_charges'] ?? 0;

    // Handle main image
    if(isset($_POST['remove_image']) && $_POST['remove_image'] == 1 && !empty($property['image'])){
        unlink('../'.$property['image']);
        $image_path = null;
    } else {
        $image_path = $property['image'] ?? null;
    }
    if(isset($_FILES['property_image']) && $_FILES['property_image']['error'] == 0){
        $img_name = time().'_'.basename($_FILES['property_image']['name']);
        $img_folder = '../uploads/'.$img_name;
        if(move_uploaded_file($_FILES['property_image']['tmp_name'], $img_folder)){
            $image_path = 'uploads/'.$img_name;
        }
    }

    // Handle floor plan
    $floor_plan_path = $property['floor_plan'] ?? null;
    if(isset($_FILES['floor_plan']) && $_FILES['floor_plan']['error'] == 0){
        $fp_name = time().'_'.basename($_FILES['floor_plan']['name']);
        $fp_folder = '../uploads/'.$fp_name;
        if(move_uploaded_file($_FILES['floor_plan']['tmp_name'], $fp_folder)){
            $floor_plan_path = 'uploads/'.$fp_name;
        }
    }

    // Update property
    $sql = "UPDATE properties SET
        property_name=?, location=?, rent=?, bedrooms=?, bathrooms=?, size=?,
        floor=?, property_type=?, description=?, available=?, featured=?,
        parking=?, furnished=?, map_embed=?, image=?, floor_plan=?, status=?, rental_type=?, latitude=?, longitude=?
        WHERE id=? AND landlord=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiissssiiiiissssdi", 
        $property_name, $location, $rent, $bedrooms, $bathrooms, $size,
        $floor, $property_type, $description, $available, $featured,
        $parking, $furnished, $map_embed, $image_path, $floor_plan_path, $status, $rental_type, $latitude, $longitude,
        $id, $landlord
    );
    $stmt->execute();

    // Update rent settings
    $sql2 = "UPDATE rent_settings SET
        base_rent=?, include_electricity=?, electricity_bill=?, include_water=?, water_bill=?,
        include_gas=?, gas_bill=?, include_service=?, service_charge=?, include_other=?, other_charges=?
        WHERE property_id=?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("didididididi",
        $rent, $include_electricity, $electricity_bill, $include_water, $water_bill,
        $include_gas, $gas_bill, $include_service, $service_charge, $include_other, $other_charges, $id
    );
    $stmt2->execute();

    // Handle multiple gallery images
    if(isset($_FILES['property_images'])){
        foreach($_FILES['property_images']['tmp_name'] as $key => $tmp_name){
            if($_FILES['property_images']['error'][$key] == 0){
                $img_name = time().'_'.basename($_FILES['property_images']['name'][$key]);
                $img_folder = '../uploads/'.$img_name;
                move_uploaded_file($tmp_name, $img_folder);
                $sql3 = "INSERT INTO property_gallery (property_id, image) VALUES (?, ?)";
                $stmt3 = $conn->prepare($sql3);
                $stmt3->bind_param("is", $id, $img_name);
                $stmt3->execute();
            }
        }
    }

    header("Location: landlord.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Property</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
.container { max-width:800px; margin:40px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); position:relative; }
h2 { text-align:center; margin-bottom:20px; }
form input[type="text"], form input[type="number"], form textarea, form select, form input[type="file"] { width:100%; padding:10px; margin:8px 0 16px 0; border:1px solid #ccc; border-radius:5px; }
form label { font-weight:bold; }
.checkbox-group { display:flex; gap:20px; margin-bottom:16px; }
.checkbox-group input { margin-right:5px; }
.btn-submit { display:inline-block; background-color:#4CAF50; color:#fff; border:none; padding:12px 25px; border-radius:5px; cursor:pointer; font-size:16px; transition:0.3s; }
.btn-submit:hover { background-color:#45a049; }
.preview-img { max-width:150px; margin-bottom:10px; border-radius:5px; position:relative; }
#closeBtn { position:absolute; top:10px; right:15px; font-size:24px; cursor:pointer; background:none; border:none; }
.image-wrapper { position:relative; display:inline-block; margin-bottom:10px; }
.remove-img-btn { position:absolute; top:5px; right:5px; background:rgba(0,0,0,0.6); color:#fff; font-weight:bold; padding:2px 6px; border-radius:50%; cursor:pointer; }
</style>
</head>
<body>
<div class="container">
    <span id="closeBtn">&times;</span>
    <h2>Edit Property</h2>
    <form action="edit_property.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="property_id" value="<?php echo $property['id'] ?? ''; ?>">
        <input type="hidden" name="remove_image" id="removeImageFlag" value="0">

        <label>Property Name</label>
        <input type="text" name="property_name" value="<?php echo htmlspecialchars($property['property_name'] ?? ''); ?>" required>

        <label>Location</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($property['location'] ?? ''); ?>" required>

        <label>Rent ($)</label>
        <input type="number" name="rent" value="<?php echo htmlspecialchars($property['rent'] ?? ''); ?>" required>

        <label>Bedrooms</label>
        <input type="number" name="bedrooms" value="<?php echo htmlspecialchars($property['bedrooms'] ?? 1); ?>">

        <label>Bathrooms</label>
        <input type="number" name="bathrooms" value="<?php echo htmlspecialchars($property['bathrooms'] ?? 1); ?>">

        <label>Size (sqft)</label>
        <input type="text" name="size" value="<?php echo htmlspecialchars($property['size'] ?? ''); ?>">

        <label>Floor</label>
        <input type="text" name="floor" value="<?php echo htmlspecialchars($property['floor'] ?? ''); ?>">

        <label>Property Type</label>
        <select name="property_type">
            <option value="Apartment" <?php if(($property['property_type'] ?? '')=='Apartment') echo 'selected'; ?>>Apartment</option>
            <option value="House" <?php if(($property['property_type'] ?? '')=='House') echo 'selected'; ?>>House</option>
            <option value="Villa" <?php if(($property['property_type'] ?? '')=='Villa') echo 'selected'; ?>>Villa</option>
        </select>

        <label>Description</label>
        <textarea name="description"><?php echo htmlspecialchars($property['description'] ?? ''); ?></textarea>

        <label>Map Embed Code</label>
        <textarea name="map_embed"><?php echo htmlspecialchars($property['map_embed'] ?? ''); ?></textarea>

        <div class="checkbox-group">
            <label><input type="checkbox" name="available" <?php if(!empty($property['available'])) echo 'checked'; ?>> Available</label>
            <label><input type="checkbox" name="featured" <?php if(!empty($property['featured'])) echo 'checked'; ?>> Featured</label>
            <label><input type="checkbox" name="parking" <?php if(!empty($property['parking'])) echo 'checked'; ?>> Parking</label>
            <label><input type="checkbox" name="furnished" <?php if(!empty($property['furnished'])) echo 'checked'; ?>> Furnished</label>
        </div>

        <label>Main Image</label>
        <?php if(!empty($property['image'])): ?>
            <div class="image-wrapper">
                <img src="../<?php echo $property['image']; ?>" class="preview-img">
                <span class="remove-img-btn">&times;</span>
            </div>
        <?php endif; ?>
        <input type="file" name="property_image" accept="image/*">

        <label>Floor Plan</label>
        <?php if(!empty($property['floor_plan'])): ?>
            <img src="../<?php echo $property['floor_plan']; ?>" class="preview-img">
        <?php endif; ?>
        <input type="file" name="floor_plan" accept="image/*">

        <button type="submit" class="btn-submit">Update Property</button>
    </form>
</div>

<script>
document.getElementById('closeBtn').addEventListener('click', function(){
    window.location.href = 'landlord.php';
});

// Remove uploaded main image
document.querySelectorAll('.remove-img-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        btn.parentElement.remove();
        document.getElementById('removeImageFlag').value = 1;
    });
});

// Preview main image before upload
const imageInput = document.querySelector('input[name="property_image"]');
imageInput?.addEventListener('change', function(){
    if(this.files && this.files[0]){
        const reader = new FileReader();
        reader.onload = e => {
            let img = document.querySelector('.preview-img') || document.createElement('img');
            img.src = e.target.result;
            img.className = 'preview-img';
            this.parentNode.insertBefore(img, this);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

// Preview floor plan before upload
const floorInput = document.querySelector('input[name="floor_plan"]');
floorInput?.addEventListener('change', function(){
    if(this.files && this.files[0]){
        const reader = new FileReader();
        reader.onload = e => {
            let img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'preview-img';
            this.parentNode.insertBefore(img, this);
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
</body>
</html>

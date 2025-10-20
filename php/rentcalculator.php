<?php
if(!isset($_GET['id'])) {
    die("Property not selected!");
}
$property_id = intval($_GET['id']);
?>
<?php
include 'config.php';

if(!isset($_GET['id'])){
    echo "Property not selected!";
    exit;
}

$property_id = intval($_GET['id']);

// Fetch property info
$sql = "SELECT * FROM properties WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$property = $stmt->get_result()->fetch_assoc();
if(!$property){
    echo "Property not found!";
    exit;
}

// Fetch rent settings
$sql2 = "SELECT * FROM rent_settings WHERE property_id=?";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $property_id);
$stmt2->execute();
$rentSettings = $stmt2->get_result()->fetch_assoc() ?? [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rent Calculator</title>
<style>
  /* Your previous styling here */
</style>
</head>
<body>
<div class="rent-calculator">
<h2>Rent Calculator</h2>

<p><strong>Property:</strong> <?= htmlspecialchars($property['property_name']) ?></p>
<p><strong>Rental Type:</strong> <?= htmlspecialchars($property['rental_type']) ?></p>

<div id="roommatesDiv" style="display:none;">
  <label>Number of Roommates:</label>
  <input type="number" id="roommates" min="1" value="1">
</div>

<div id="rentDetails"></div>
<h3>Total Rent: <span id="totalRent">0</span> ৳</h3>
<h3 id="perHead"></h3>
</div>

<script>
const property = <?= json_encode($property) ?>;
const rentSettings = <?= json_encode($rentSettings) ?>;

const rentDetailsEl = document.getElementById('rentDetails');
const totalRentEl = document.getElementById('totalRent');
const perHeadEl = document.getElementById('perHead');
const roommatesDiv = document.getElementById('roommatesDiv');
const roommatesInput = document.getElementById('roommates');

let total = 0;
let breakdown = '';

// Check if property is for Rent or Sell
if(property.status === 'Rent'){
    total = parseFloat(rentSettings.base_rent) || 0;
    breakdown += `<p>Base Rent: ${rentSettings.base_rent || 0} ৳</p>`;
    
    if(rentSettings.include_electricity) breakdown += `<p>Electricity: ${rentSettings.electricity_bill || 0} ৳</p>`, total += parseFloat(rentSettings.electricity_bill || 0);
    if(rentSettings.include_water) breakdown += `<p>Water: ${rentSettings.water_bill || 0} ৳</p>`, total += parseFloat(rentSettings.water_bill || 0);
    if(rentSettings.include_gas) breakdown += `<p>Gas: ${rentSettings.gas_bill || 0} ৳</p>`, total += parseFloat(rentSettings.gas_bill || 0);
    if(rentSettings.include_service) breakdown += `<p>Service Charge: ${rentSettings.service_charge || 0} ৳</p>`, total += parseFloat(rentSettings.service_charge || 0);
    if(rentSettings.include_other) breakdown += `<p>Other Charges: ${rentSettings.other_charges || 0} ৳</p>`, total += parseFloat(rentSettings.other_charges || 0);

    // Show roommates input only if rental type is bachelor or both
    if(property.rental_type === 'bachelor' || property.rental_type === 'both'){
        roommatesDiv.style.display = 'block';
        function updateSplit(){
            const n = Number(roommatesInput.value) || 1;
            perHeadEl.textContent = `Per Person: ${(total/n).toFixed(2)} ৳`;
        }
        roommatesInput.addEventListener('input', updateSplit);
        updateSplit();
    }

} else if(property.status === 'Sell'){
    // Calculate total price + fees
    const registration = 5000;
    const legal = 10000;
    const agent = 20000;
    const taxes = 15000;
    const loanInterest = 10000;

    total = parseFloat(property.rent) || 0; // using rent field as sale price
    breakdown = `<p>Property Price: ${total} ৳</p>
                 <p>Registration Fees: ${registration} ৳</p>
                 <p>Legal Fees: ${legal} ৳</p>
                 <p>Agent Commission: ${agent} ৳</p>
                 <p>Taxes: ${taxes} ৳</p>
                 <p>Loan Interest: ${loanInterest} ৳</p>`;
    total += registration + legal + agent + taxes + loanInterest;
}

rentDetailsEl.innerHTML = breakdown;
totalRentEl.textContent = total;
</script>
</body>
</html>

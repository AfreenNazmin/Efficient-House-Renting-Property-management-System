<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Settings</title>
</head>
<body>
   <div class="rent-calculator">
  <h2>Rent Calculator</h2>
  <label>Rent Type:</label>
  <select id="rentType">
    <option value="family">Family</option>
    <option value="roommates">Roommates</option>
  </select>

  <label>Roommates (if any):</label>
  <input type="number" id="roommates" min="1" value="1">

  <div id="rentDetails"></div>

  <h3>Total Rent: <span id="totalRent">0</span> ৳</h3>
  <h3 id="perHead"></h3>
</div>

<script>
const propertyId = 1; // dynamic later

fetch(`get_rent_settings.php?property_id=${propertyId}`)
  .then(res => res.json())
  .then(data => {
    let total = parseFloat(data.base_rent);
    let breakdown = `<p>Base Rent: ${data.base_rent}৳</p>`;

    if (data.include_electricity == 1) {
      total += parseFloat(data.electricity_bill);
      breakdown += `<p>Electricity Bill: ${data.electricity_bill}৳</p>`;
    }
    if (data.include_water == 1) {
      total += parseFloat(data.water_bill);
      breakdown += `<p>Water Bill: ${data.water_bill}৳</p>`;
    }
    if (data.include_gas == 1) {
      total += parseFloat(data.gas_bill);
      breakdown += `<p>Gas Bill: ${data.gas_bill}৳</p>`;
    }
    if (data.include_service == 1) {
      total += parseFloat(data.service_charge);
      breakdown += `<p>Service Charge: ${data.service_charge}৳</p>`;
    }
    if (data.include_other == 1) {
      total += parseFloat(data.other_charges);
      breakdown += `<p>Other Charges: ${data.other_charges}৳</p>`;
    }

    document.getElementById('rentDetails').innerHTML = breakdown;
    document.getElementById('totalRent').textContent = total;

    const rentType = document.getElementById('rentType');
    const roommates = document.getElementById('roommates');
    const perHead = document.getElementById('perHead');

    function updateSplit() {
      if (rentType.value === 'roommates') {
        const n = Number(roommates.value) || 1;
        perHead.textContent = `Per Person: ${(total / n).toFixed(2)}৳`;
      } else {
        perHead.textContent = '';
      }
    }

    rentType.addEventListener('change', updateSplit);
    roommates.addEventListener('input', updateSplit);
  });
</script>
 
</body>
</html>
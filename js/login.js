// Role selection
function setRole(role) {
  document.getElementById("roleInput").value = role;
  document.getElementById("tenantBtn").classList.remove("active");
  document.getElementById("landlordBtn").classList.remove("active");
  document.getElementById(role + "Btn").classList.add("active");
}

// Show/hide password
function togglePassword(fieldId) {
  const input = document.getElementById(fieldId);
  if (input.type === "password") {
    input.type = "text";
  } else {
    input.type = "password";
  }
}

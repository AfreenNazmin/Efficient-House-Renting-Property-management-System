<?php
include 'bar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FAQ - Frequently Asked Questions</title>
  <link rel="stylesheet" href="../css/faq.css">
</head>
<body>

  <!-- Header -->
  <header class="faq-header">
    <div class="container">
      <h1>Frequently Asked Questions</h1>
      <p>Your questions answered in one place.</p>
    </div>
  </header>

  <!-- FAQ Section -->
  <main class="faq-section">
    <div class="container">

      <div class="faq-item">
        <h3>How do I list my property?</h3>
        <p>You can create an account as a landlord and add your property details in the “Manage Listings” section.</p>
      </div>

      <div class="faq-item">
        <h3>Can tenants book a property online?</h3>
        <p>Yes, tenants can request a property visit or send a rent application through the booking feature.</p>
      </div>

      <div class="faq-item">
        <h3>Is there any cost for using the platform?</h3>
        <p>Our basic services are free for students and demo use. Premium features can be added later if required.</p>
      </div>

      <div class="faq-item">
        <h3>How is my data protected?</h3>
        <p>We use secure connections and follow privacy best practices to protect user information.</p>
      </div>

      <div class="faq-item">
        <h3>Who can I contact for support?</h3>
        <p>You can reach out to us via the <a href="contact.html">Contact Page</a> for assistance.</p>
      </div>

    </div>
  </main>

    <!-- Footer -->
      <div id="footer"></div>
    </div>
  </section>

  <!-- Footer Loader -->
  <script>
    fetch('../html/footer.html') 
      .then(res => res.text())
      .then(data => document.getElementById('footer').innerHTML = data);
  </script>

</body>
</html>

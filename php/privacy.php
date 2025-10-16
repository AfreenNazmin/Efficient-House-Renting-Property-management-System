<?php
include 'bar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Privacy Policy</title>
  <link rel="stylesheet" href="../css/terms.css">
</head>
<body>

  <section class="properties-header">

    <header class="page-header">
      <div class="container">
        <h1>Privacy Policy</h1>
        <p class="lead">Last updated: <time datetime="2025-09-13">September 13, 2025</time></p>
      </div>
    </header>

    <main class="content">
      <div class="container two-column">

        <!-- Table of Contents -->
        <nav class="toc" aria-label="Table of contents">
          <h2>On this page</h2>
          <ul>
            <li><a href="#data-collection">1. Data We Collect</a></li>
            <li><a href="#cookies">2. Cookies & Tracking</a></li>
            <li><a href="#data-security">3. Data Security & Retention</a></li>
            <li><a href="#third-parties">4. Third-Party Services</a></li>
            <li><a href="#user-rights">5. Your Rights</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="#changes">Changes to This Policy</a></li>
          </ul>
        </nav>

        <!-- Main content -->
        <article class="legal">

          <section id="data-collection">
            <h3>1. Data We Collect</h3>
            <ul>
              <li>Personal information you provide, such as name, email, phone number, and property details.</li>
              <li>Information from your account activity, listings, and profile.</li>
              <li>Usage and device information, including page visits, search queries, and IP addresses.</li>
            </ul>
          </section>

          <section id="cookies">
            <h3>2. Cookies & Tracking</h3>
            <p>We use cookies and similar technologies to enhance your experience. You can control cookie preferences through your browser settings.</p>
          </section>

          <section id="data-security">
            <h3>3. Data Security & Retention</h3>
            <p>We implement reasonable technical and organizational measures to protect your data from unauthorized access. Personal data is retained only as long as necessary for service provision or legal compliance.</p>
          </section>

          <section id="third-parties">
            <h3>4. Third-Party Services</h3>
            <p>Rentify may share data with trusted third-party service providers for operational purposes, marketing, or analytics. We ensure these providers comply with strict privacy standards.</p>
          </section>

          <section id="user-rights">
            <h3>5. Your Rights</h3>
            <ul>
              <li>You may request access to your personal data or request corrections.</li>
              <li>You can request deletion of your personal data, subject to legal obligations.</li>
              <li>You may opt-out of marketing communications at any time.</li>
            </ul>
          </section>

          <section id="contact">
            <h3>Contact Us</h3>
            <p>If you have questions regarding this Privacy Policy, please contact us at:  
            <a href="mailto:support@rentify.com">support@rentify.com</a></p>
          </section>

          <section id="changes">
            <h3>Changes to This Policy</h3>
            <p>We may update this Privacy Policy from time to time. Updates will appear on this page with a revised “Last updated” date.</p>
          </section>

        </article>
      </div>
    </main>

    <div id="footer"></div>

    <script>
      fetch('footer.html')
        .then(res => res.text())
        .then(data => document.getElementById('footer').innerHTML = data);
    </script>

</body>
</html>

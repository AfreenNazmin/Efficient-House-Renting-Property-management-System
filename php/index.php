<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Find Your Perfect Home</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <!-- Menu -->
  <div class="menu-container">
    <div class="hamburger-menu">☰</div>
    <div class="signup-button">
      <a href="../html/signup.html"><button>Sign Up</button></a>
    </div>
    <nav class="mobile-menu">
      <a href="index.php">Home</a>
      <a href="../html/about.html">About</a>
      <a href="../html/services.html">Services</a>
      <a href="../html/properties.html">Properties</a>
      <a href="../html/contact.html">Contact</a>
      <a href="../html/login.html">Login</a>
    </nav>
  </div>

  <!-- Hero Section -->
  <div class="hero">
    <div class="overlay"></div>
    <div class="hero-content">
      <h1>FIND YOUR<br>PERFECT HOME</h1>
      <p>Search by city, neighborhood, or address</p>

      <!-- Search Box with Autocomplete -->
      <div class="search-box">
        <form action="search.php" method="GET" autocomplete="off">
          <input type="text" name="query" placeholder="Search by city, neighborhood, or address" id="search-input" required />
          <button type="submit">Search</button>
          <div id="suggestions"></div> <!-- Dropdown container -->
        </form>
      </div>

      <!-- Hero Buttons -->
      <div class="buttons">
        <button class="btn-outline" onclick="location.href='properties.html'">BROWSE PROPERTIES</button>
        <button class="btn-outline" onclick="document.querySelector('.services').scrollIntoView({behavior:'smooth'})">LEARN MORE</button>
      </div>
    </div>
  </div>

  <!-- Services & Featured Properties Section -->
  <div class="new-section">
    <section class="bar">
      <a href="index.php">Home</a>
      <a href="../html/about.html">About</a>
      <a href="../html/services.html">Services</a>
      <a href="../html/properties.html">Properties</a>
      <a href="../html/contact.html">Contact</a>
      <a href="../html/login.html">Login</a>
      <input type="text" placeholder="Search...">
      <span>🔍</span>
    </section>

   <section class="popular-properties">
  <h2>Popular Properties</h2>
  <div class="properties-grid">
    <?php
      // Include database connection
      include 'config.php';

      // Fetch 3 popular properties (latest added)
      $query = "SELECT * FROM properties ORDER BY id DESC LIMIT 3";
      $result = mysqli_query($conn, $query);

      if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
    ?>
          <div class="property-card">
            <img src="../uploads/<?php echo $row['image_file']; ?>" alt="<?php echo $row['property_name']; ?>">
            <h3><?php echo $row['property_name']; ?></h3>
            <p>$<?php echo $row['rent']; ?>/month - <?php echo $row['location']; ?></p>
          </div>
    <?php
        }
      } else {
        echo '<p>No properties found.</p>';
      }
    ?>
  </div>
  <div class="view-more-btn">
    <button onclick="location.href='properties.html'">See More Properties</button>
  </div>
</section>


    <section class="featured-properties">
      <h2>Featured Properties</h2>
      <div class="properties-grid">
        <div class="property-card">
          <img src="images/property1.jpg" alt="Property 1">
          <h3>Modern Apartment</h3>
          <p>$1200/month - New York</p>
        </div>
        <div class="property-card">
          <img src="images/property2.jpg" alt="Property 2">
          <h3>Luxury Villa</h3>
          <p>$2500/month - Los Angeles</p>
        </div>
        <div class="property-card">
          <img src="images/property3.jpg" alt="Property 3">
          <h3>Cozy Studio</h3>
          <p>$800/month - Chicago</p>
        </div>
      </div>
    </section>
  </div>

  <!-- Footer (unchanged) -->
  <footer class="footer">
        <div class="container">
      <!-- Brand / About -->
      <div class="footer-section about">
        <h2 class="logo">Title+logo</h2>
        <p class="tagline">Finding your perfect home made simple</p>
        <p class="mission">Our mission is to make property search fast, easy, and reliable.</p>
      </div>

      <!-- Quick Links -->
      <div class="footer-section links">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="../html/about.html">About Us</a></li>
          <li><a href="../html/contact.html">Contact Us</a></li>
          <li><a href="../html/terms.html">Terms & Privacy Policy</a></li>
          <li><a href="../html/faq.html">FAQ</a></li>
        </ul>
      </div>

      <!-- Services / Categories -->
      <div class="footer-section_services">
        <h3>Services</h3>
        <ul>
          <li><a href="#">Rent</a></li>
          <li><a href="#">Buy</a></li>
          <li><a href="#">Sell</a></li>
          <li><a href="#">Featured Properties</a></li>
          <li><a href="#">Agents</a></li>
        </ul>
      </div>

      <!-- Contact Information -->
      <div class="footer-section contact">
        <h3>Contact</h3>
        <p>Phone: +123 456 7890</p>
        <p>Email: info@.com</p>
        <p>Address: 123 Main Street, City</p>
        <p>Customer Care: +123 456 7891</p>
      </div>

      <!-- Social Media -->
      <div class="footer-section social">
        <h3>Follow Us</h3>
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-linkedin-in"></i></a>
          <a href="#"><i class="fab fa-youtube"></i></a>
          <a href="#"><i class="fab fa-tiktok"></i></a>
        </div>
      </div>

      <!-- Newsletter / Signup -->
      <div class="footer-section newsletter">
        <h3>Subscribe</h3>
        <form>
          <input type="email" placeholder="Enter your email">
          <button type="submit">Subscribe</button>
        </form>
      </div>
    </div>

    <!-- Legal Info -->
    <div class="legal">
      <p>© 2025 Page name. All Rights Reserved.</p>
      <p><a href="../html/terms.html">Terms of Service</a> | <a href="#">Privacy Policy</a></p>
    </div>
  </footer>
  

  <!-- Scripts -->
  <script>
    // Hamburger menu toggle
    const hamburger = document.querySelector(".hamburger-menu");
    const mobileMenu = document.querySelector(".mobile-menu");
    hamburger.addEventListener("click", () => {
      mobileMenu.classList.toggle("active");
    });

    // Autocomplete search suggestions
    const input = document.getElementById('search-input');
    const suggestionsBox = document.getElementById('suggestions');

    input.addEventListener('input', function(){
      const term = this.value;
      if(term.length > 1){
        fetch(`suggest.php?term=${term}`)
          .then(res => res.json())
          .then(data => {
            // Clear previous suggestions
            suggestionsBox.innerHTML = '';
            if(data.length){
              data.forEach(item => {
                const div = document.createElement('div');
                div.classList.add('suggestion-item');
                div.textContent = item;
                div.addEventListener('click', () => {
                  input.value = item;
                  suggestionsBox.innerHTML = '';
                });
                suggestionsBox.appendChild(div);
              });
              suggestionsBox.style.display = 'block';
            } else {
              suggestionsBox.style.display = 'none';
            }
          });
      } else {
        suggestionsBox.innerHTML = '';
        suggestionsBox.style.display = 'none';
      }
    });

    // Hide suggestions on click outside
    document.addEventListener('click', function(e){
      if(!input.contains(e.target) && !suggestionsBox.contains(e.target)){
        suggestionsBox.style.display = 'none';
      }
    });
  </script>
</body>
</html>

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
  <!-- Menu container signup and ham-->
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

  <!-- Hero Section first view -->
  <div class="hero">
    <div class="overlay"></div>
    <div class="hero-content">
      <h1>FIND YOUR<br>PERFECT HOME</h1>
      <p>Search by city, neighborhood, or address</p>

      <!-- Search Box -->
      <div class="search-box">
        <form action="search.php" method="GET" autocomplete="off">
          <input type="text" name="query" placeholder="Search by city, neighborhood, or address" id="search-input" required />
          <button type="submit">Search</button>
          <div id="suggestions"></div> 
        </form>
      </div>

      <!-- Hero section Buttons -->
      <div class="buttons">
       <a href="../html/properties.html" class="btn-outline">BROWSE PROPERTIES</a>
        <a href="../html/services.html" class="btn-outline">LEARN MORE</a>
      </div>
    </div>
  </div>

  <!-- Services & Featured Properties Section -->
  <div class="new-section">
    <section class="bar">
      <a href="index.php">Home</a>
      <a href="../html/about.html">About</a>
      <a href="../html/services.html">Favourite</a>
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
      
      include 'config.php';

      $query = "SELECT * FROM properties ORDER BY id DESC LIMIT 3";
      $result = mysqli_query($conn, $query);

      if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
    ?>
         <div class="listing-card">
  <img src="../uploads/<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'default.jpg'; ?>" 
     alt="<?php echo !empty($row['property_name']) ? htmlspecialchars($row['property_name']) : 'Property'; ?>">


  <h3><?php echo isset($row['property_name']) ? htmlspecialchars($row['property_name']) : 'Unnamed'; ?></h3>
  <p>
    $<?php echo isset($row['rent']) ? $row['rent'] : '0'; ?>/month - 
    <?php echo isset($row['location']) ? htmlspecialchars($row['location']) : 'Unknown'; ?>
  </p>
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
    </section>
  </div>

  <!-- Footer section-->
  <footer class="footer">
        <div class="container">
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

      <!-- Newsletter -->
      <div class="footer-section newsletter">
        <h3>Subscribe</h3>
        <form>
          <input type="email" placeholder="Enter your email">
          <button type="submit">Subscribe</button>
        </form>
      </div>
    </div>

    <!-- right Info -->
    <div class="cr">
      <p>© 2025 Page name. All Rights Reserved.</p>
      <p><a href="../html/terms.html">Terms of Service</a> | <a href="#">Privacy Policy</a></p>
    </div>
  </footer>
  

  <!--js-->
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

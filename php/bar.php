<?php
// Default allowed links if not set
if(!isset($allowed_links)) {
    $allowed_links = array_keys([
        'Home' => 'index.php',
        'About' => '../html/about.html',
        
        'Properties' => 'properties.php',
        'Contact' => '../html/contact.html',
        'Login' => 'login.php',
        'Search' => '#'

    ]);
}

// All possible links
$all_links = [
    'Home' => 'index.php',
    'About' => 'about.php',
    'Favourites' => 'favourites.php',
    'Properties' => 'properties.php',
    'Contact' => 'contact.php',
    'Login' => 'login.php',
    'Logout' => 'logout.php',
     'Services' => 'Services.php',
    'Search' => '#'
   
];
?>

<section class="bar">
    <?php foreach($all_links as $name => $url): 
        if(!in_array($name, $allowed_links)) continue;
        $active = basename($_SERVER['PHP_SELF']) === basename($url) ? 'class="active"' : '';
        ?>
        <?php if($name === 'Favourites'): ?>
            <a href="<?php echo $url; ?>" class="fav-link" <?php echo $active; ?>><i class="far fa-heart"></i> <?php echo $name; ?></a>
        <?php else: ?>
            <a href="<?php echo $url; ?>" <?php echo $active; ?>><?php echo $name; ?></a>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- Search input -->
    <?php if(in_array('Search', $allowed_links)): ?>
<form method="GET" action="tenant.php" class="bar-search">
    <input type="text" name="q" placeholder="Search..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
</form>
<?php endif; ?>
</section>

<style>
.bar {
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #333;
}
.bar a {
  text-decoration: none;
  color: #fafafa;
  font-weight: bold;
  margin: 25px;
  padding: 5px 15px;
}
.bar a:hover {
  color: #C0C0C0;
}
.bar input[type="text"] {
  padding: 6px 10px;
  height: 28px;
  border: 1px solid #ccc;
  border-radius: 20px;
  font-size: 14px;
  outline: none;
}
.bar span {
  margin-left: -25px;
  cursor: pointer;
  color: #555;
}
.bar .fav-link i {
    color: #f0a500; 
    font-size: 1.2rem;
    margin-right: 5px;
}

.bar .fav-link:hover i {
    transform: scale(1.2);
}

@media (max-width: 768px) {
    .bar {
        flex-direction: column;
        align-items: flex-start;
    }
    .bar-search {
        width: 100%;
        margin-left: 0;
    }
    .bar-search input {
        width: 100%;
    }
}
</style>

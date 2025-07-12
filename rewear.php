<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ReWear – Community Clothing Exchange</title>
  <link rel="stylesheet" href="rewear.css">
</head>
<body>

<!-- Top Nav -->
<!-- Top Nav -->
<header class="top-bar">
  <!-- wrap the logo in a link that always reloads rewear.php -->
  <h1><a href="rewear.php" class="logo-link">ReWear</a></h1>
  <div class="user-info">
    <?php if ($isLoggedIn): ?>
      <a href="user_dashboard.php"><?= htmlspecialchars($userName) ?></a> |
      <a href="logout.php" class="logout">Logout</a>
    <?php else: ?>
      <a href="auth.php?action=login">Login</a> |
      <a href="auth.php?action=signup" class="logout">Signup</a>
    <?php endif; ?>
  </div>
</header>



<!-- Hero Section (only after login) -->
<?php if ($isLoggedIn): ?>
  <section class="hero-section">
    <div class="hero-text">
      <h2>Swap Smarter. Save Earth. Wear ReWear.</h2>
      <p>Join our sustainable fashion movement by reusing instead of buying new.</p>
      <div class="cta-buttons">
        <a href="add_item.php" class="cta">List New Product</a>
        <a href="" class="cta">Start Swapping</a>
        <a href="browse_items.php" class="cta">Browse Items</a>
      </div>
    </div>
  </section>
<?php endif; ?>

<!-- Search Bar -->
<div class="search-container">
  <form action="#" method="GET">
    <input type="text" name="search" placeholder="Search for clothes..." <?= $isLoggedIn ? '' : 'disabled' ?>>
    <button <?= $isLoggedIn ? '' : 'disabled' ?>>🔍</button>
  </form>
</div>
<!-- Banner -->
<section class="banner">
  <img src="assets/images/sample_banner.jpg" alt="Banner">
</section>

<!-- Categories -->
<section class="categories">
  <h2>Categories</h2>
  <div class="category-grid">
    <div class="category-card">Tops</div>
    <div class="category-card">Bottoms</div>
    <div class="category-card">Footwear</div>
    <div class="category-card">Accessories</div>
    <div class="category-card">Outerwear</div>
  </div>
</section>

<!-- Listings -->
<section class="listings">
  <h2>Featured Products</h2>
  <div class="product-grid">
    <?php if (!$isLoggedIn): ?>
      <div class="product-card">Login to view items</div>
      <div class="product-card">Login to view items</div>
      <div class="product-card">Login to view items</div>
    <?php else: ?>
      <?php
      require 'db_connect.php';
      $stmt = $conn->prepare("SELECT * FROM items ORDER BY id DESC LIMIT 6");
      $stmt->execute();
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()):
      ?>
        <div class="product-card">
          <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
          <h3><?= htmlspecialchars($row['title']) ?></h3>
          <p><?= htmlspecialchars($row['category']) ?> - <?= htmlspecialchars($row['size']) ?></p>
          <a href="item_details.php?id=<?= $row['id'] ?>">View</a>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</section>

</body>
</html>

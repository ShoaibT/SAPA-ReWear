<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}
require 'db_connect.php';

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT name, email, phone, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Fetch My Listings
$stmtListings = $conn->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC");
$stmtListings->bind_param("i", $user_id);
$stmtListings->execute();
$listings = $stmtListings->get_result();

// Fetch My Purchases (swap requests accepted)
$stmtPurchases = $conn->prepare("
  SELECT i.*
  FROM swap_requests sr
  JOIN items i ON sr.item_id = i.id
  WHERE sr.requester_id = ? AND sr.status = 'accepted'
");
$stmtPurchases->bind_param("i", $user_id);
$stmtPurchases->execute();
$purchases = $stmtPurchases->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard – ReWear</title>
  <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
<style>/* General Layout */
body {
  font-family: 'Segoe UI', sans-serif;
  margin: 0;
  padding: 0;
  background: #f5f5f5;
  color: #333;
}

.top-bar {
  background-color: #1e8e3e;
  color: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.top-bar h1 {
  margin: 0;
}

.user-info a {
  color: #fff;
  text-decoration: none;
  margin-left: 10px;
  font-weight: 500;
}

.user-info a:hover {
  text-decoration: underline;
}

/* Profile Summary */
.profile-summary {
  background-color: white;
  margin: 2rem;
  padding: 2rem;
  display: flex;
  align-items: center;
  gap: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.profile-pic {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: url('../../assets/images/default_avatar.png') center/cover no-repeat;
  border: 2px solid #1e8e3e;
}

.profile-details p {
  margin: 0.4rem 0;
  font-size: 1rem;
  color: #444;
}

/* Section Headings */
section h2 {
  padding-left: 2rem;
  font-size: 1.3rem;
  margin-top: 2rem;
  color: #1e8e3e;
}

/* Product Grid */
.product-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
  gap: 1rem;
  padding: 1rem 2rem;
}

/* Product Cards */
.product-card {
  background: white;
  width: 200px;
  height: 320px;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,.1);
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem;
  text-align: center;
  transition: transform 0.2s;
}

.product-card:hover {
  transform: scale(1.02);
}

.product-card img {
  width: 100%;
  height: 150px;
  object-fit: cover;
  border-radius: 5px;
}

.product-card h3 {
  font-size: 1.1rem;
  margin: 0.5rem 0 0.3rem;
}

.product-card p {
  font-size: 0.9rem;
  color: #777;
}

/* Responsive */
@media (max-width: 768px) {
  .profile-summary {
    flex-direction: column;
    align-items: flex-start;
  }

  .product-grid {
    justify-content: center;
  }

  .product-card {
    width: 100%;
    max-width: 300px;
  }
}
</style>
<header class="top-bar">
  <h1>ReWear</h1>
  <div class="user-info">
    Hello, <?php echo htmlspecialchars($user['name']); ?> |
    <a href="logout.php">Logout</a>
  </div>
</header>

<!-- Profile Summary -->
<section class="profile-summary">
  <div class="profile-pic"></div>
  <div class="profile-details">
    <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? 'N/A') ?></p>
    <p><strong>Joined:</strong> <?= date('M d, Y', strtotime($user['created_at'])) ?></p>
  </div>
</section>

<!-- My Listings -->
<section class="my-listings">
  <h2>My Listings</h2>
  <div class="product-grid">
    <?php if ($listings->num_rows > 0): ?>
      <?php while ($item = $listings->fetch_assoc()): ?>
        <div class="product-card">
          <img src="uploads/<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
          <h3><?= htmlspecialchars($item['title']) ?></h3>
          <p><?= htmlspecialchars($item['category']) ?> - <?= htmlspecialchars($item['size']) ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>You have not listed any items yet.</p>
    <?php endif; ?>
  </div>
</section>

<!-- My Purchases -->
<section class="my-purchases">
  <h2>My Purchases</h2>
  <div class="product-grid">
    <?php if ($purchases->num_rows > 0): ?>
      <?php while ($item = $purchases->fetch_assoc()): ?>
        <div class="product-card">
          <img src="uploads/<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
          <h3><?= htmlspecialchars($item['title']) ?></h3>
          <p><?= htmlspecialchars($item['category']) ?> - <?= htmlspecialchars($item['size']) ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No completed purchases/swaps found.</p>
    <?php endif; ?>
  </div>
</section>

</body>
</html>

<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: auth.php?action=login");
  exit();
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT items.*, users.name AS uploader_name 
                        FROM items 
                        JOIN users ON items.user_id = users.id 
                        WHERE items.user_id != ? 
                        ORDER BY items.created_at DESC");

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Browse Items – ReWear</title>
  <link rel="stylesheet" href="css/browse_items.css">
</head>
<body>

<header class="top-bar">
  <h1><a href="rewear.php" class="logo-link">ReWear</a></h1>
  <div class="user-info">
    <a href="user_dashboard.php"><?= htmlspecialchars($_SESSION['user_name']) ?></a> |
    <a href="logout.php" class="logout">Logout</a>
  </div>
</header>

<div class="page-container">
  <h2>Available Clothing from Other Users</h2>

  <?php if ($result->num_rows > 0): ?>
    <div class="product-grid">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product-card">
            
          <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
          <h3><?= htmlspecialchars($row['title']) ?></h3>
          <p><?= htmlspecialchars($row['category']) ?> - <?= htmlspecialchars($row['size']) ?></p>
          <p class="uploader">From: <?= htmlspecialchars($row['uploader_name']) ?></p>

          <span class="availability-tag"><?= htmlspecialchars($row['availability']) ?></span>
          <a href="item_details.php?id=<?= $row['id'] ?>">View</a>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p>No items available at the moment.</p>
  <?php endif; ?>

</div>
</body>
</html>

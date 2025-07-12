<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: auth.php?action=login");
  exit();
}

$userId = $_SESSION['user_id'];
$cat = $_GET['cat'] ?? '';

// Prepare the query based on category selection
if ($cat !== '') {
  $stmt = $conn->prepare(
    "SELECT items.*, users.name AS uploader_name
     FROM items
     JOIN users ON users.id = items.user_id
     WHERE items.user_id != ? AND items.category = ? 
     ORDER BY items.created_at DESC"
  );
  $stmt->bind_param("is", $userId, $cat);
} else {
  $stmt = $conn->prepare(
    "SELECT items.*, users.name AS uploader_name
     FROM items
     JOIN users ON users.id = items.user_id
     WHERE items.user_id != ? 
     ORDER BY items.created_at DESC"
  );
  $stmt->bind_param("i", $userId);
}
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
<h2>
  <?= $cat ? "Showing: " . htmlspecialchars($cat) : "All Available Items" ?>
</h2>

<div class="search-container" style="margin-bottom: 20px; text-align: center;">
  <input type="text" id="searchBox" placeholder="Search for items..." style="padding: 8px; width: 60%; max-width: 400px;">
</div>


  <?php if ($result->num_rows > 0): ?>
    <div class="product-grid">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product-card">
        
            
          <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
          <h3><?= htmlspecialchars($row['title']) ?></h3>
          <p><?= htmlspecialchars($row['category']) ?> - <?= htmlspecialchars($row['size']) ?></p>
          <p class="uploader">From: <?= htmlspecialchars($row['uploader_name']) ?></p>

          <span class="availability-tag"><?= htmlspecialchars($row['availability']) ?></span>
<p class="listed-date">Listed on: <?= date('d F, Y', strtotime($row['created_at'])) ?></p>

          <a href="item_details.php?id=<?= $row['id'] ?>">View</a>
        </div>
        <p id="noResults" style="text-align:center; color:#888; display:none;">No matching items found.</p>

      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p>No items available at the moment.</p>
  <?php endif; ?>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const searchBox = document.getElementById('searchBox');
  const cards = document.querySelectorAll('.product-card');
  const noResults = document.getElementById('noResults');

  searchBox.addEventListener('keyup', function () {
    const q = searchBox.value.trim().toLowerCase();
    let matchFound = false;

    cards.forEach(card => {
      const title = card.querySelector('h3').innerText.toLowerCase();
      if (title.includes(q)) {
        card.style.display = "";
        matchFound = true;
      } else {
        card.style.display = "none";
      }
    });

    noResults.style.display = matchFound ? "none" : "block";
  });
});
</script>

</body>
</html>

<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: auth.php?action=login");
  exit();
}

$itemId = $_GET['id'] ?? null;

if (!$itemId || !is_numeric($itemId)) {
  die("Invalid item ID.");
}

// Fetch item info
$stmt = $conn->prepare(
  "SELECT items.*, users.name AS uploader_name, users.email 
   FROM items 
   JOIN users ON users.id = items.user_id 
   WHERE items.id = ?"
);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  die("Item not found.");
}

$item = $result->fetch_assoc();
$isOwner = $item['user_id'] == $_SESSION['user_id'];
$isUnavailable = strtolower($item['availability']) !== 'available';

// Check if already requested or redeemed
$alreadyRequested = false;
$alreadyRedeemed  = false;

$checkSwap = $conn->prepare("SELECT id FROM swap_requests WHERE item_id = ? AND requester_id = ?");
$checkSwap->bind_param("ii", $itemId, $_SESSION['user_id']);
$checkSwap->execute();
$checkSwap->store_result();
if ($checkSwap->num_rows > 0) $alreadyRequested = true;
$checkSwap->close();

$checkRedeem = $conn->prepare("SELECT id FROM redeem_logs WHERE item_id = ? AND redeemed_by = ?");
$checkRedeem->bind_param("ii", $itemId, $_SESSION['user_id']);
$checkRedeem->execute();
$checkRedeem->store_result();
if ($checkRedeem->num_rows > 0) $alreadyRedeemed = true;
$checkRedeem->close();



// Fetch gallery images
$imgStmt = $conn->prepare("SELECT image_path FROM item_images WHERE item_id = ?");
$imgStmt->bind_param("i", $itemId);
$imgStmt->execute();
$imgResult = $imgStmt->get_result();
$images = [];
while ($img = $imgResult->fetch_assoc()) {
  $images[] = $img['image_path'];
}
$imgStmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
      $action = $_POST['action'];
  
      if ($action === 'swap') {
        $insertSwap = $conn->prepare("INSERT INTO swap_requests (item_id, requester_id) VALUES (?, ?)");
        $insertSwap->bind_param("ii", $itemId, $_SESSION['user_id']);
        $insertSwap->execute();
        $insertSwap->close();
  
        // Optionally mark item unavailable or keep as-is until approved
        $update = $conn->prepare("UPDATE items SET availability='Swap Only' WHERE id = ?");
        $update->bind_param("i", $itemId);
        $update->execute();
        $update->close();
  
        header("Location: item_details.php?id=$itemId");
        exit;
      }
  
      if ($action === 'redeem') {
        $pointsUsed = 10; // hardcoded for now
        $insertRedeem = $conn->prepare("INSERT INTO redeem_logs (item_id, redeemed_by, points_used) VALUES (?, ?, ?)");
        $insertRedeem->bind_param("iii", $itemId, $_SESSION['user_id'], $pointsUsed);
        $insertRedeem->execute();
        $insertRedeem->close();
  
        $update = $conn->prepare("UPDATE items SET availability='Redeemed' WHERE id = ?");
        $update->bind_param("i", $itemId);
        $update->execute();
        $update->close();
  
        header("Location: item_details.php?id=$itemId");
        exit;
      }
    }
  }
  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($item['title']) ?> – ReWear</title>
  <link rel="stylesheet" href="css/item_details.css">
  <style>
    .gallery-main img { width: 100%; max-height: 400px; object-fit: contain; border: 1px solid #ccc; border-radius: 8px; }
    .gallery-thumbs { display: flex; gap: 10px; margin-top: 10px; }
    .gallery-thumbs img { width: 60px; height: 60px; object-fit: cover; cursor: pointer; border-radius: 5px; border: 1px solid #aaa; }
    .info { margin-top: 20px; }
    .actions button { padding: 10px 20px; margin-right: 10px; font-weight: bold; cursor: pointer; }
    .availability { font-weight: bold; color: #008000; }
  </style>
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

  <h2><?= htmlspecialchars($item['title']) ?></h2>

  <!-- Image Gallery -->
  <div class="gallery-main">
    <img id="mainImage" src="<?= htmlspecialchars($item['image_path']) ?>" alt="Main Image">
    <div class="gallery-thumbs">
      <?php foreach ($images as $img): ?>
        <img src="<?= htmlspecialchars($img) ?>" onclick="document.getElementById('mainImage').src=this.src;">
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Item Info -->
  <div class="info">
    <p><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?></p>
    <p><strong>Size:</strong> <?= htmlspecialchars($item['size']) ?></p>
    <p><strong>Condition:</strong> <?= htmlspecialchars($item['condition_state']) ?></p>
    <p><strong>Tags:</strong> <?= htmlspecialchars($item['tags']) ?></p>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($item['description'])) ?></p>
    <p class="availability"><strong>Status:</strong> <?= htmlspecialchars($item['availability']) ?></p>
  </div>

  <!-- Uploader -->
  <div class="info">
    <h4>Uploader Info</h4>
    <p><strong>Name:</strong> <?= htmlspecialchars($item['uploader_name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($item['email']) ?></p>
  </div>

  <div class="actions">
  <?php if ($isOwner): ?>
    <button disabled style="background:#ccc; cursor: not-allowed;">Your Item</button>
  <?php elseif ($isUnavailable || $alreadyRequested || $alreadyRedeemed): ?>
    <button disabled style="background:#ccc; cursor: not-allowed;">
      <?= $alreadyRequested ? 'Swap Requested' : ($alreadyRedeemed ? 'Already Redeemed' : 'Unavailable') ?>
    </button>
  <?php else: ?>
    <form method="POST" style="display:inline;">
      <input type="hidden" name="action" value="swap">
      <button type="submit">Request Swap</button>
    </form>
    <form method="POST" style="display:inline;">
      <input type="hidden" name="action" value="redeem">
      <button type="submit">Redeem via Points</button>
    </form>
  <?php endif; ?>
</div>



</div>

</body>
</html>

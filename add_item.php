<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: auth.php?action=login");
  exit();
}

$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title       = trim($_POST['title']);
  $description = trim($_POST['description']);
  $category    = $_POST['category'];
  $type        = $_POST['type'];
  $size        = $_POST['size'];
  $condition   = $_POST['condition'];
  $tags        = trim($_POST['tags']);
  $availability = $_POST['availability'];
  $user_id     = $_SESSION['user_id'];

  if ($title === '' || $category === '' || $type === '' || $size === '' || $condition === '' || $availability === '') {
    $errors[] = "Please fill all required fields.";
  }

  if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = "Please upload a valid image.";
  } else {
    $imgName = basename($_FILES['image']['name']);
    $imgTmp  = $_FILES['image']['tmp_name'];
    $targetPath = 'uploads/' . time() . '_' . $imgName;

    if (!move_uploaded_file($imgTmp, $targetPath)) {
      $errors[] = "Failed to upload image.";
    }
  }

  if (empty($errors)) {
    $stmt = $conn->prepare("INSERT INTO items 
      (title, description, category, type, size, condition_state, tags, image_path, status, user_id) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssi", $title, $description, $category, $type, $size, $condition, $tags, $targetPath, $availability, $user_id);
    if ($stmt->execute()) {
      $success = true;
    } else {
      $errors[] = "Database error: " . $conn->error;
    }
    $stmt->close();
  }
}

// Fetch previous listings for logged-in user
$previousStmt = $conn->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
$previousStmt->bind_param("i", $_SESSION['user_id']);
$previousStmt->execute();
$previousItems = $previousStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Item – ReWear</title>
  <link rel="stylesheet" href="css/add_item.css">

</head>
<body>
<header class="top-bar">
  <h1><a href="rewear.php" class="logo-link">ReWear</a></h1>
  <div class="user-info">
    <a href="user_dashboard.php"><?= htmlspecialchars($_SESSION['user_name']) ?></a> |
    <a href="logout.php" class="logout">Logout</a>
  </div>
</header>

<div class="form-container">
  <h2>Add a New Clothing Item</h2>

  <?php if ($success): ?>
    <p class="success">Item submitted successfully! Awaiting approval.</p>
  <?php elseif (!empty($errors)): ?>
    <div class="error-box">
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="add_item.php" method="POST" enctype="multipart/form-data">
    <div class="form-grid">
      <!-- LEFT SIDE -->
      <div class="form-left">
        <label for="image">Add Images</label>
        <div class="image-box">
          <img id="preview" src="assets/images/placeholder.png" alt="Preview" />
        </div>
        <input type="file" name="image" id="image" accept="image/*" required onchange="previewImage(event)">
        <br><input type="text" name="title" placeholder="Title" required>
      </div>

      <!-- RIGHT SIDE -->
      <div class="form-right">
        <textarea name="description" placeholder="Add Product Description" required></textarea>

        <select name="category" required>
          <option value="">-- Category --</option>
          <option value="Tops">Tops</option>
          <option value="Bottoms">Bottoms</option>
          <option value="Footwear">Footwear</option>
          <option value="Accessories">Accessories</option>
          <option value="Outerwear">Outerwear</option>
        </select>

        <select name="type" required>
          <option value="">-- Type --</option>
          <option value="Men">Men</option>
          <option value="Women">Women</option>
          <option value="Unisex">Unisex</option>
        </select>

        <select name="size" required>
          <option value="">-- Size --</option>
          <option value="XS">XS</option>
          <option value="S">S</option>
          <option value="M">M</option>
          <option value="L">L</option>
          <option value="XL">XL</option>
          <option value="Free Size">Free Size</option>
        </select>

        <select name="condition" required>
          <option value="">-- Condition --</option>
          <option value="New with tags">New with tags</option>
          <option value="Like new">Like new</option>
          <option value="Gently used">Gently used</option>
          <option value="Visible wear">Visible wear</option>
        </select>

        <select name="availability" required>
        <option value="">-- Availability --</option>
        <option value="Available">Available</option>
        <option value="Swap Only">Swap Only</option>
        </select>


        <input type="text" name="tags" placeholder="Tags (comma-separated)">
        <button type="submit" class="submit-btn">Submit Item</button>
      </div>
    </div>
  
  </form>

  <!-- Previous Listings -->
  <?php if ($previousItems->num_rows > 0): ?>
    <div class="previous-listings">
      <h3>Your Recent Listings</h3>
      <div class="product-grid">
        <?php while ($item = $previousItems->fetch_assoc()): ?>
          <div class="product-card">
            <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
            <p><?= htmlspecialchars($item['title']) ?></p>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  <?php endif; ?>

</div>
<script>
function previewImage(event) {
  const output = document.getElementById('preview');
  output.src = URL.createObjectURL(event.target.files[0]);
}
</script>
</body>
</html>

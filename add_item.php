<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: auth.php?action=login");
  exit();
}

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title       = trim($_POST['title']);
  $description = trim($_POST['description']);
  $category    = $_POST['category'];
  $type        = $_POST['type'];
  $size        = $_POST['size'];
  $condition   = $_POST['condition'];
  $availability= $_POST['availability'];
  $tags        = trim($_POST['tags']);
  $user_id     = $_SESSION['user_id'];

  // basic validation
  if ($title === '' || $category === '' || $type === '' || $size === '' || $condition === '' || $availability === '')
    $errors[] = "Please fill all required fields.";

  // make sure at least one image
  if (!isset($_FILES['images']) || $_FILES['images']['error'][0] !== UPLOAD_ERR_OK)
    $errors[] = "Please upload at least one image.";

  // if no errors so far → insert item row
  if (empty($errors)) {

    $coverPath = null;
    $allPaths  = [];
  
    foreach ($_FILES['images']['tmp_name'] as $idx => $tmp) {
      if ($_FILES['images']['error'][$idx] === UPLOAD_ERR_OK) {
        $name   = basename($_FILES['images']['name'][$idx]);
        $target = 'uploads/' . time() . '_' . $name;
  
        if (move_uploaded_file($tmp, $target)) {
          if ($coverPath === null) {
            $coverPath = $target; // first image becomes main
          }
          $allPaths[] = $target;
        }
      }
    }
  
    if (!$coverPath) $errors[] = "Image upload failed.";
  
    if (empty($errors)) {
      $stmt = $conn->prepare(
        "INSERT INTO items (title, description, category, type, size, condition_state, tags, availability, image_path, status, user_id) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)"
      );
      $stmt->bind_param("sssssssssi", $title, $description, $category, $type, $size, $condition, $tags, $availability, $coverPath, $user_id);
  
      if ($stmt->execute()) {
        $itemId = $stmt->insert_id;
  
        $imgStmt = $conn->prepare("INSERT INTO item_images (item_id, image_path) VALUES (?, ?)");
        foreach ($allPaths as $path) {
          $imgStmt->bind_param("is", $itemId, $path);
          $imgStmt->execute();
        }
        $imgStmt->close();
        // Reward 10 points for listing an item
$updatePoints = $conn->prepare("UPDATE users SET points = points + 10 WHERE id = ?");
$updatePoints->bind_param("i", $user_id);
$updatePoints->execute();
$updatePoints->close();

        $success = true;
      } else {
        $errors[] = "Database error: " . $conn->error;
      }
      $stmt->close();
    }
  }
  
}

/* fetch last 3 listings for sidebar */
$prev = $conn->prepare("SELECT id FROM items WHERE user_id=? ORDER BY created_at DESC LIMIT 3");
$prev->bind_param("i", $_SESSION['user_id']);
$prev->execute();
$previous = $prev->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Item – ReWear</title>
<link rel="stylesheet" href="css/add_item.css">
<style>
.image-box{width:100%;height:250px;border:2px dashed #ccc;border-radius:8px;
display:flex;justify-content:center;align-items:center;background:#fafafa;margin-bottom:12px;overflow:hidden}
.image-box img{max-width:100%;max-height:100%;object-fit:contain}
.thumb-strip{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:15px}
.thumb-strip img{width:60px;height:60px;object-fit:cover;border-radius:4px;cursor:pointer;border:1px solid #ccc}
</style>
</head>
<body>
<header class="top-bar">
  <h1><a href="rewear.php" class="logo-link">ReWear</a></h1>
  <div class="user-info">
    <a href="user_dashboard.php"><?=htmlspecialchars($_SESSION['user_name'])?></a> |
    <a href="logout.php" class="logout">Logout</a>
  </div>
</header>

<div class="form-container">
<h2>Add a New Clothing Item</h2>

<?php if($success): ?>
  <p class="success">Item submitted successfully!</p>
<?php elseif($errors): ?>
  <div class="error-box"><ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div>
<?php endif; ?>

<form action="add_item.php" method="POST" enctype="multipart/form-data">
  <div class="form-grid">
    <!-- LEFT -->
    <div class="form-left">
      <label><strong>Add Images</strong></label>
      <div class="image-box"><img id="preview" src="assets/images/placeholder.png"></div>
      <div class="thumb-strip" id="thumbs"></div>
      <input type="file" name="images[]" accept="image/*" multiple required onchange="handleFiles(this.files)">
      <input type="text" name="title" placeholder="Title" required>
    </div>

    <!-- RIGHT -->
    <div class="form-right">
      <textarea name="description" placeholder="Product Description" required></textarea>
      <select name="category" required>
        <option value="">-- Category --</option><option>Tops</option><option>Bottoms</option>
        <option>Outerwear</option>
      </select>
      <select name="type" required>
        <option value="">-- Type --</option><option>Men</option><option>Women</option><option>Unisex</option>
      </select>
      <select name="size" required>
        <option value="">-- Size --</option><option>XS</option><option>S</option><option>M</option>
        <option>L</option><option>XL</option><option>Free Size</option>
      </select>
      <select name="condition" required>
        <option value="">-- Condition --</option><option>New with tags</option><option>Like new</option>
        <option>Gently used</option><option>Visible wear</option>
      </select>
      <select name="availability" required>
        <option value="">-- Availability --</option><option>Available</option><option>Swap Only</option>
      </select>
      <input type="text" name="tags" placeholder="Tags (comma-separated)">
      <button class="submit-btn">Submit Item</button>
    </div>
  </div>
</form>

<?php if($previous->num_rows): ?>
  <div class="previous-listings">
    <h3>Your Recent Listings</h3>
    <div class="product-grid">
      <?php while($it=$previous->fetch_assoc()):
        $img=$conn->query("SELECT image_path FROM item_images WHERE item_id={$it['id']} LIMIT 1")->fetch_assoc()['image_path'] ?? 'assets/images/placeholder.png'; ?>
        <div class="product-card"><img src="<?=$img?>"><p>ID #<?=$it['id']?></p></div>
      <?php endwhile; ?>
    </div>
  </div>
<?php endif; ?>
</div>

<script>
function handleFiles(files){
  const preview=document.getElementById('preview');
  const thumbs=document.getElementById('thumbs');
  thumbs.innerHTML='';
  if(files.length){
    preview.src=URL.createObjectURL(files[0]);
    for(let i=0;i<files.length;i++){
      const t=document.createElement('img');
      t.src=URL.createObjectURL(files[i]);
      t.onclick=()=>preview.src=t.src;
      thumbs.appendChild(t);
    }
  }
}
</script>
</body>
</html>

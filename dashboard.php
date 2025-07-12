<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard – ReWear</title>
  <link rel="stylesheet" href="assets/css/auth.css"><!-- reuse styles for now -->
</head>
<body>
  <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
  <p>This is your dashboard placeholder. Build your swap lists and point balance here.</p>
  <a href="logout.php">Logout</a>
</body>
</html>

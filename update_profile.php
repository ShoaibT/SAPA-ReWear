<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: auth.php");
  exit();
}

$userId = $_SESSION['user_id'];
$phone = $_POST['phone'] ?? null;

$updateQuery = "UPDATE users SET phone = ? WHERE id = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("si", $phone, $userId);
$stmt->execute();

// OPTIONAL: Handle profile image upload
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
  $imageName = basename($_FILES['profile_image']['name']);
  $targetPath = 'uploads/profile_' . $userId . '_' . time() . '_' . $imageName;
  if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
    $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?")
         ->bind_param("si", $targetPath, $userId)
         ->execute();
  }
}

$_SESSION['message'] = "Profile updated successfully.";
header("Location: user_dashboard.php");
exit();
?>

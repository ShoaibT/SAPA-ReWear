<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user_dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$phone_updated = false;
$image_updated = false;

// 1. Update phone if present
if (!empty($_POST['phone'])) {
    $phone = preg_replace("/[^0-9]/", "", $_POST['phone']); // sanitize
    $stmt = $conn->prepare("UPDATE users SET phone = ? WHERE id = ?");
    $stmt->bind_param("si", $phone, $user_id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) $phone_updated = true;
    $stmt->close();
}

// 2. Update profile image if uploaded
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "user/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
    $newFileName = 'user_' . $user_id . "_" . time() . '.' . $ext;
    $targetFile = $uploadDir . $newFileName;

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->bind_param("si", $targetFile, $user_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) $image_updated = true;
        $stmt->close();
    }
}

// 3. Set message
if ($phone_updated && $image_updated) {
    $_SESSION['message'] = "Phone number and profile image updated successfully.";
} elseif ($phone_updated) {
    $_SESSION['message'] = "Phone number updated successfully.";
} elseif ($image_updated) {
    $_SESSION['message'] = "Profile image updated successfully.";
} else {
    $_SESSION['error'] = "No changes were made.";
}

header("Location: user_dashboard.php");
exit();
?>

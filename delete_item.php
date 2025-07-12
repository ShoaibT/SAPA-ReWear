<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: user_dashboard.php");
  exit();
}

$itemId = (int)$_POST['item_id'];
$userId = $_SESSION['user_id'];

/* 1. Verify ownership and get created_at */
$stmt = $conn->prepare(
  "SELECT created_at FROM items WHERE id = ? AND user_id = ? LIMIT 1"
);
$stmt->bind_param("ii", $itemId, $userId);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
  $_SESSION['error'] = "Item not found or not authorized.";
  header("Location: user_dashboard.php");
  exit();
}
$item = $res->fetch_assoc();
$stmt->close();

/* 2. Calculate age in hours */
$created   = new DateTime($item['created_at']);
$now       = new DateTime();
$interval  = $created->diff($now);
$hours     = ($interval->days * 24) + $interval->h;

/* 3. Determine point deduction */
$deduction = ($hours < 24) ? 10 : 5;

/* 4. Deduct points, never below 0 */
$updatePts = $conn->prepare(
  "UPDATE users SET points = GREATEST(points - ?, 0) WHERE id = ?"
);
$updatePts->bind_param("ii", $deduction, $userId);
$updatePts->execute();
$updatePts->close();

/* 5. Delete images */
$delImgs = $conn->prepare("DELETE FROM item_images WHERE item_id = ?");
$delImgs->bind_param("i", $itemId);
$delImgs->execute();
$delImgs->close();

/* 6. Delete the item */
$delItem = $conn->prepare("DELETE FROM items WHERE id = ?");
$delItem->bind_param("i", $itemId);
$delItem->execute();
$delItem->close();

/* 7. Flash message & redirect */
$_SESSION['message'] = "Item deleted. {$deduction} points deducted.";
header("Location: user_dashboard.php");
exit();
?>

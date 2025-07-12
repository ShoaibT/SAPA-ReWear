<?php
session_start();
require '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'], $_POST['action'])) {
  $item_id = (int)$_POST['item_id'];
  $status = in_array($_POST['action'], ['approved', 'rejected']) ? $_POST['action'] : 'pending';

  $stmt = $conn->prepare("UPDATE items SET status = ? WHERE id = ?");
  $stmt->bind_param("si", $status, $item_id);
  $stmt->execute();
}

header("Location: admin_dashboard.php");
exit();

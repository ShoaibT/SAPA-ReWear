<?php
require 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name  = trim($_POST['name']);
  $email = trim($_POST['email']);
  $pass  = $_POST['password'];

  if ($name && $email && $pass) {
    $hash = password_hash($pass, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO admins (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hash);
    if ($stmt->execute()) {
      echo "Admin created successfully!";
    } else {
      echo "Error: " . $conn->error;
    }
  } else {
    echo "All fields are required.";
  }
}
?>
<form method="POST">
  <h2>Admin Signup</h2>
  <input name="name" placeholder="Name"><br><br>
  <input name="email" placeholder="Email"><br><br>
  <input name="password" type="password" placeholder="Password"><br><br>
  <button>Sign Up</button>
</form>

<?php
session_start();
require_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password_hash FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        if (password_verify($pass, $admin['password_hash'])) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_name'] = $admin['name'];
            $_SESSION['is_admin'] = true;

            header("Location: admin_dashboard.php");
            exit();
        }
    }
    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    /* same styles as your previous glass+particles login design */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body, html {
      height: 100%;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: url('Superadmin.png') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .particle-overlay {
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1;
      overflow: hidden;
    }

    .particle {
      position: absolute;
      width: 6px;
      height: 6px;
      background: rgba(255, 255, 255, 0.7);
      border-radius: 50%;
      animation: float 8s linear infinite;
    }

    @keyframes float {
      from {
        transform: translateY(100vh) scale(1);
        opacity: 1;
      }
      to {
        transform: translateY(-100vh) scale(0);
        opacity: 0;
      }
    }

    .login-container {
      z-index: 2;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      max-width: 400px;
      width: 90%;
      color: #fff;
      animation: zoomIn 0.6s ease-out;
    }

    @keyframes zoomIn {
      from {
        opacity: 0;
        transform: scale(0.9);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    h2 {
      margin-bottom: 25px;
      font-size: 26px;
      font-weight: 600;
      color: #fff;
    }

    .form-group {
      text-align: left;
      margin-bottom: 20px;
    }

    .form-group label {
      font-size: 14px;
      margin-bottom: 5px;
      display: block;
      color: #eee;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      font-size: 15px;
      outline: none;
    }

    input[type="text"]::placeholder,
    input[type="password"]::placeholder {
      color: #ccc;
    }

    input[type="submit"] {
      background-color: #007BFF;
      border: none;
      padding: 12px;
      width: 100%;
      border-radius: 10px;
      color: #fff;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
      background-color: #0056b3;
    }

    .error {
      background: #f8d7da;
      color: #721c24;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 20px;
      border: 1px solid #f5c6cb;
      font-weight: bold;
      text-align: center;
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 25px;
      }

      h2 {
        font-size: 20px;
      }

      input[type="submit"] {
        font-size: 15px;
      }
    }
  </style>
</head>
<body>

  <div class="particle-overlay" id="particles"></div>

  <div class="login-container">
    <h2>Admin Login</h2>
    <?php if (isset($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="text" name="email" required placeholder="Enter Email">
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" name="password" required placeholder="Enter Password">
      </div>
      <input type="submit" value="Login">
    </form>
  </div>

  <script>
    const particleCount = 50;
    const container = document.getElementById('particles');
    for (let i = 0; i < particleCount; i++) {
      const particle = document.createElement('div');
      particle.classList.add('particle');
      particle.style.left = Math.random() * 100 + 'vw';
      particle.style.top = Math.random() * 100 + 'vh';
      particle.style.animationDuration = (5 + Math.random() * 5) + 's';
      particle.style.animationDelay = Math.random() * 5 + 's';
      container.appendChild(particle);
    }
  </script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login / Signup - ReWear</title>
  <link rel="stylesheet" href="assets/css/auth.css">
  <script>
    function toggleForm(formType) {
      document.getElementById('login-form').style.display = (formType === 'login') ? 'block' : 'none';
      document.getElementById('signup-form').style.display = (formType === 'signup') ? 'block' : 'none';
    }
  </script>
</head>
<body>

<div class="auth-container">
  <div class="form-toggle">
    <button onclick="toggleForm('login')">Login</button>
    <button onclick="toggleForm('signup')">Signup</button>
  </div>

  <!-- Login Form -->
  <form id="login-form" action="process_auth.php" method="POST" style="display: block;">
    <h2>Login</h2>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="submit" name="login" value="Login">
  </form>

  <!-- Signup Form -->
  <form id="signup-form" action="process_auth.php" method="POST" style="display: none;">
    <h2>Signup</h2>
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <input type="text" name="phone" placeholder="Phone (Optional)">
    <input type="submit" name="signup" value="Signup">
  </form>
</div>

</body>
</html>

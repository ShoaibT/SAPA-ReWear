<?php
$defaultForm = isset($_GET['action']) && $_GET['action'] === 'signup' ? 'signup' : 'login';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login / Signup - ReWear</title>
  <link rel="stylesheet" href="css/auth.css">
  <script>
  window.addEventListener("DOMContentLoaded", () => {
    const defaultForm = "<?php echo $defaultForm; ?>";
    const loginForm = document.getElementById("login-form");
    const signupForm = document.getElementById("signup-form");

    if (defaultForm === "signup") {
      signupForm.style.display = "block";
      loginForm.style.display = "none";
    } else {
      signupForm.style.display = "none";
      loginForm.style.display = "block";
    }
  });

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
    <br>
    <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" name="login" value="Login">
  </form>

  <!-- Signup Form -->
  <form id="signup-form" action="process_auth.php" method="POST" style="display: none;">
    <h2>Signup</h2>
    <input type="text" name="name" placeholder="Full Name" required>
    <br>  
    <input type="email" name="email" placeholder="Email" required>
    <br>
    <input type="password" name="password" placeholder="Password" required>
    <br>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <br>
    <input type="text" name="phone" placeholder="Phone (Optional)">
    <br>
    <input type="submit" name="signup" value="Signup">
  </form>
</div>

 <!-- Theme toggle switch -->
 <div class="theme-toggle">
    <label class="switch">
      <input type="checkbox" id="theme-checkbox" onchange="toggleTheme()" />
      <span class="slider round"></span>
    </label>
  </div>

<script src="auth.js"></script>

</body>
</html>

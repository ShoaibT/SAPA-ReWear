<?php
require 'db_connect.php';
session_start();

/* ----------  SIGN‑UP  ---------- */
if (isset($_POST['signup'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $phone    = trim($_POST['phone']);

    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $name, $email, $hashed, $phone);

    if ($stmt->execute()) {
        /*  Auto‑login right after successful signup  */
        $_SESSION['user_id']   = $stmt->insert_id;
        $_SESSION['user_name'] = $name;
        header("Location: rewear.php");   // ⬅️ redirect to logged‑in home
        exit();
    } else {
        echo "Signup failed: " . $conn->error;
    }

    $stmt->close();
}

/* ----------  LOGIN  ---------- */
if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT id, name, password FROM users WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id']   = $id;
            $_SESSION['user_name'] = $name;
            header("Location: rewear.php");   // ⬅️ redirect to logged‑in home
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Email not registered.";
    }

    $stmt->close();
}
?>

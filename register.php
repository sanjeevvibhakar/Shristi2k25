<?php
session_start();
include('db.php');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $credits = 3; // default starting credits

  // Check if user exists
  $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $check->bind_param("s", $username);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    $message = "<div class='alert alert-warning'>⚠️ Username already exists.</div>";
  } else {
    $stmt = $conn->prepare("INSERT INTO users (username, password, credits) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $username, $password, $credits);
    $stmt->execute();
    $message = "<div class='alert alert-success'>✅ Registered successfully! <a href='login.php'>Login now</a>.</div>";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h3 class="mb-4">📝 User Registration</h3>

  <?= $message ?>

  <form method="POST" class="card p-4 shadow-sm" style="max-width: 400px;">
    <input type="text" name="username" class="form-control mb-3" placeholder="Choose username" required>
    <input type="password" name="password" class="form-control mb-3" placeholder="Choose password" required>
    <button class="btn btn-success w-100">Register</button>
  </form>
</div>
</body>
</html>

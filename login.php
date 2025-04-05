<?php
session_start();
include('db.php');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
      $_SESSION['username'] = $user['username'];
      header("Location: dashboard.php");
      exit();
    } else {
      $message = "<div class='alert alert-danger'>❌ Incorrect password.</div>";
    }
  } else {
    $message = "<div class='alert alert-danger'>❌ User not found.</div>";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>User Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h3 class="mb-4">🔐 User Login</h3>

  <?= $message ?>

  <form method="POST" class="card p-4 shadow-sm" style="max-width: 400px;">
    <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
    <button class="btn btn-primary w-100">Login</button>
  </form>
</div>
</body>
</html>

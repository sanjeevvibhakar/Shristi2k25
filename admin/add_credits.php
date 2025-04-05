<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $credits = intval($_POST['credits']);

  if ($credits > 0) {
    $stmt = $conn->prepare("UPDATE users SET credits = credits + ? WHERE username = ?");
    $stmt->bind_param("is", $credits, $username);
    $stmt->execute();
  }
}

header("Location: dashboard.php");
exit();

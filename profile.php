<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

$username = $_SESSION['username'];

// Get user data
$stmt = $conn->prepare("SELECT credits FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get recent match logs
$logStmt = $conn->prepare("SELECT * FROM match_logs WHERE username = ? ORDER BY matched_on DESC LIMIT 10");
$logStmt->bind_param("s", $username);
$logStmt->execute();
$logs = $logStmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="d-flex justify-content-between mb-3">
    <h3>👋 Welcome, <?= htmlspecialchars($username) ?></h3>
    <a href="logout.php" class="btn btn-danger">Logout</a>
  </div>

  <div class="card mb-4 p-3 shadow-sm">
    <h5>💳 Available Credits: <strong><?= $user['credits'] ?></strong></h5>
  </div>

  <div class="card p-3 shadow-sm">
    <h5 class="mb-3">📄 Recent Matches</h5>
    <table class="table table-sm table-striped">
      <thead>
        <tr>
          <th>Filename</th>
          <th>Similarity</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $logs->fetch_assoc()): ?>
          <tr>
            <td><?= $row['filename'] ?></td>
            <td><?= number_format($row['similarity_score'] * 100, 2) ?>%</td>
            <td><?= $row['matched_on'] ?></td>
          </tr>
        <?php endwhile; ?>
        <?php if ($logs->num_rows == 0): ?>
          <tr><td colspan="3">No matches yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>

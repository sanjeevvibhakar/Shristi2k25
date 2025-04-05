<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3>📄 Document Match Dashboard</h3>
      <div>
        <a href="profile.php" class="btn btn-secondary me-2">👤 My Profile</a>

        👤 <?= $username ?> |
        <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
        <a href="profile.php" class="btn btn-secondary me-2">👤 My Profile</a>

      </div>
    </div>

    <!-- Upload + Match Form -->
    <form action="match.php" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm mb-4">
      <div class="mb-3">
        <label class="form-label">Upload Document</label>
        <input type="file" name="document" class="form-control" required>
      </div>
      <button class="btn btn-success">🔍 Match Now</button>
    </form>

    <!-- Match result display -->
    <div class="card p-4 shadow-sm">
      <h5>📊 Match Result:</h5>
      <div id="matchResult">
        <!-- match.php output will appear here after submit -->
        <?php if (isset($_SESSION['match_result'])) {
          echo $_SESSION['match_result'];
          unset($_SESSION['match_result']);
        } ?>
      </div>
    </div>
  </div>
</body>
</html>

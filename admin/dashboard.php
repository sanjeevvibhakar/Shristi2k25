
<?php

session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}


// Optional: Protect with admin session
// if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$result = $conn->query("SELECT * FROM match_logs ORDER BY matched_on DESC");
$users = $conn->query("SELECT username, credits FROM users");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h3 class="mb-4">🛠 Admin Dashboard</h3>
    <div class="mb-3 text-end">
  <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
</div>


    <h5>➕ Add Credits</h5>
    <form action="add_credits.php" method="POST" class="mb-4 row g-2">
      <div class="col-md-4">
        <select name="username" class="form-select" required>
          <option value="">-- Select User --</option>
          <?php while($row = $users->fetch_assoc()): ?>
            <option value="<?= $row['username'] ?>"><?= $row['username'] ?> (💳 <?= $row['credits'] ?>)</option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-3">
        <input type="number" name="credits" class="form-control" placeholder="Credits to add" required>
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100">Add</button>
      </div>
    </form>

    <h5>📄 Match Logs</h5>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>User</th>
          <th>File</th>
          <th>Similarity</th>
          <th>Matched On</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['username'] ?></td>
          <td><?= $row['filename'] ?></td>
          <td><?= number_format($row['similarity_score'] * 100, 2) ?>%</td>
          <td><?= $row['matched_on'] ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
  <title>Upload Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="upload.php">📄 DocMatch</a>
    <a class="btn btn-outline-light" href="match.php">Scan Docs</a>
  </nav>

  <div class="container py-5">
    <h2 class="mb-4">📤 Upload a Document (.txt)</h2>
    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
      <input type="file" name="document" class="form-control mb-3" accept=".txt" required>
      <button class="btn btn-primary">Upload</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $file = $_FILES['document'];

        $curl = curl_init('http://localhost:3000/api/upload');
        $cfile = new CURLFile($file['tmp_name'], $file['type'], $file['name']);

        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => ['document' => $cfile]
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            echo "<div class='alert alert-danger mt-4'>❌ $error</div>";
        } else {
            $data = json_decode($response, true);
            echo "<div class='alert alert-success mt-4'>✅ Uploaded: <strong>{$data['filename']}</strong></div>";
        }
    }
    ?>
  </div>
</body>
</html>

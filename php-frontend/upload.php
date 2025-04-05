<!DOCTYPE html>
<html>
<head>
  <title>Upload Document</title>
</head>
<body>
  <h2>Upload Document (.txt)</h2>
  <form action="upload.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="document" accept=".txt" required>
    <button type="submit">Upload</button>
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
        echo "<p>Error: $error</p>";
    } else {
        $data = json_decode($response, true);
        echo "<p>✅ Uploaded: <strong>{$data['filename']}</strong></p>";
    }
}
?>
<br><a href="match.php">🔍 Go to Match Page</a>
</body>
</html>

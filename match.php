<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    echo "<div class='alert alert-danger'>❌ You must be logged in.</div>";
    exit();
}

$username = $_SESSION['username'];

// Check credits
$sql = "SELECT credits FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['match_result'] = "<div class='alert alert-danger'>❌ User not found.</div>";
    header("Location: dashboard.php");
    exit();
}

if ($user['credits'] <= 0) {
    $_SESSION['match_result'] = "<div class='alert alert-danger'>❌ Not enough credits! Please contact admin.</div>";
    header("Location: dashboard.php");
    exit();
}

// ✅ Handle file upload
if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['match_result'] = "<div class='alert alert-warning'>⚠️ Please upload a valid document.</div>";
    header("Location: dashboard.php");
    exit();
}

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir);

$filename = basename($_FILES['document']['name']);
$targetFile = $uploadDir . time() . '-' . $filename;

if (!move_uploaded_file($_FILES['document']['tmp_name'], $targetFile)) {
    $_SESSION['match_result'] = "<div class='alert alert-danger'>❌ Failed to upload file.</div>";
    header("Location: dashboard.php");
    exit();
}

// 🌐 Send to Node.js API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'http://localhost:3000/api/match',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'document' => new CURLFile($targetFile)
    ]
]);

$response = curl_exec($curl);
$error = curl_error($curl);
curl_close($curl);

if ($error) {
    $_SESSION['match_result'] = "<div class='alert alert-danger'>❌ Match API Error: $error</div>";
    header("Location: dashboard.php");
    exit();
}
// 🧠 Decode JSON response
$data = json_decode($response, true);
$score = isset($data['similarity']) ? $data['similarity'] : 0.0;

// 📝 Log to DB
$logSql = "INSERT INTO match_logs (username, filename, similarity_score) VALUES (?, ?, ?)";
$logStmt = $conn->prepare($logSql);
$savedFile = basename($targetFile);
$logStmt->bind_param("ssd", $username, $savedFile, $score);
$logStmt->execute();

// 🧾 Deduct 1 credit
$deduct = "UPDATE users SET credits = credits - 1 WHERE username = ?";
$stmt = $conn->prepare($deduct);
$stmt->bind_param("s", $username);
$stmt->execute();

$newCredits = $user['credits'] - 1;

// ✅ Save result
$_SESSION['match_result'] = "<div class='alert alert-success'>✅ Match successful! 🪙 Credits left: <strong>$newCredits</strong></div><div class='alert alert-secondary mt-3'><pre>$response</pre></div>";
header("Location: dashboard.php");
exit();

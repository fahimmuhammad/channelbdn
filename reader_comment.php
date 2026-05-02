<?php
require_once 'reader_auth.php';
header('Content-Type: application/json');

if (!readerIsLoggedIn()) { echo json_encode(['error' => 'login_required']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['error' => 'method']); exit; }

global $conn;
$rid     = (int)$_SESSION['reader_id'];
$pid     = (int)($_POST['post_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if (!$pid)              { echo json_encode(['error' => 'invalid_post']); exit; }
if (mb_strlen($comment) < 2) { echo json_encode(['error' => 'too_short']); exit; }
if (mb_strlen($comment) > 1000) { echo json_encode(['error' => 'too_long']); exit; }

$cm = $conn->real_escape_string($comment);
$conn->query("INSERT INTO reader_comments (reader_id,post_id,comment) VALUES ($rid,$pid,'$cm')");

if ($conn->insert_id) {
    echo json_encode(['success' => true, 'message' => 'আপনার মন্তব্য পর্যালোচনার জন্য পাঠানো হয়েছে।']);
} else {
    echo json_encode(['error' => 'db_error']);
}

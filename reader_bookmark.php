<?php
require_once 'reader_auth.php';
header('Content-Type: application/json');

if (!readerIsLoggedIn()) { echo json_encode(['error' => 'login_required']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['error' => 'method']); exit; }

global $conn;
$rid = (int)$_SESSION['reader_id'];
$pid = (int)($_POST['post_id'] ?? 0);
if (!$pid) { echo json_encode(['error' => 'invalid']); exit; }

$r = $conn->query("SELECT id FROM reader_bookmarks WHERE reader_id=$rid AND post_id=$pid LIMIT 1");
if ($r && $r->num_rows) {
    $conn->query("DELETE FROM reader_bookmarks WHERE reader_id=$rid AND post_id=$pid");
    $bookmarked = false;
} else {
    $conn->query("INSERT INTO reader_bookmarks (reader_id,post_id) VALUES ($rid,$pid)");
    $bookmarked = true;
}

$cnt = $conn->query("SELECT COUNT(*) as c FROM reader_bookmarks WHERE post_id=$pid");
$count = ($cnt && $row = $cnt->fetch_assoc()) ? (int)$row['c'] : 0;

echo json_encode(['bookmarked' => $bookmarked, 'count' => $count]);

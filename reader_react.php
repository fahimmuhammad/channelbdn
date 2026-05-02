<?php
require_once 'reader_auth.php';
header('Content-Type: application/json');

if (!readerIsLoggedIn()) { echo json_encode(['error' => 'login_required']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['error' => 'method']); exit; }

global $conn;
$rid      = (int)$_SESSION['reader_id'];
$pid      = (int)($_POST['post_id'] ?? 0);
$reaction = trim($_POST['reaction'] ?? '');
$allowed  = ['like','love','sad','angry','wow'];

if (!$pid || !in_array($reaction, $allowed)) { echo json_encode(['error' => 'invalid']); exit; }

$r = $conn->query("SELECT id,reaction FROM reader_reactions WHERE reader_id=$rid AND post_id=$pid LIMIT 1");
if ($r && $r->num_rows) {
    $existing = $r->fetch_assoc();
    if ($existing['reaction'] === $reaction) {
        $conn->query("DELETE FROM reader_reactions WHERE reader_id=$rid AND post_id=$pid");
        $myReaction = null;
    } else {
        $re = $conn->real_escape_string($reaction);
        $conn->query("UPDATE reader_reactions SET reaction='$re' WHERE reader_id=$rid AND post_id=$pid");
        $myReaction = $reaction;
    }
} else {
    $re = $conn->real_escape_string($reaction);
    $conn->query("INSERT INTO reader_reactions (reader_id,post_id,reaction) VALUES ($rid,$pid,'$re')");
    $myReaction = $reaction;
}

$counts = getReactionCounts($pid);
echo json_encode(['my_reaction' => $myReaction, 'counts' => $counts]);

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__FILE__) . '/config.php';

/* ── helpers ── */
function readerIsLoggedIn() {
    return !empty($_SESSION['reader_id']);
}

function readerGet() {
    if (!readerIsLoggedIn()) return null;
    global $conn;
    $id = (int)$_SESSION['reader_id'];
    $r  = $conn->query("SELECT id,name,email,avatar,bio,created_at FROM readers WHERE id=$id AND is_active=1 LIMIT 1");
    return ($r && $r->num_rows) ? $r->fetch_assoc() : null;
}

function readerRegister($name, $email, $password) {
    global $conn;
    $name     = trim($name);
    $email    = trim(strtolower($email));
    $password = trim($password);

    if (strlen($name) < 2)           return ['error' => 'নাম কমপক্ষে ২ অক্ষরের হতে হবে।'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return ['error' => 'সঠিক ইমেইল দিন।'];
    if (strlen($password) < 6)       return ['error' => 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।'];

    $em = $conn->real_escape_string($email);
    $exists = $conn->query("SELECT id FROM readers WHERE email='$em' LIMIT 1");
    if ($exists && $exists->num_rows) return ['error' => 'এই ইমেইলে ইতিমধ্যে অ্যাকাউন্ট আছে।'];

    $nm = $conn->real_escape_string($name);
    $ph = password_hash($password, PASSWORD_DEFAULT);
    $pp = $conn->real_escape_string($ph);
    $conn->query("INSERT INTO readers (name,email,password) VALUES ('$nm','$em','$pp')");
    $id = $conn->insert_id;
    if (!$id) return ['error' => 'নিবন্ধন ব্যর্থ হয়েছে। আবার চেষ্টা করুন।'];

    $_SESSION['reader_id']   = $id;
    $_SESSION['reader_name'] = $name;
    $_SESSION['reader_email']= $email;
    return ['success' => true];
}

function readerLogin($email, $password) {
    global $conn;
    $email = trim(strtolower($email));
    $em    = $conn->real_escape_string($email);
    $r     = $conn->query("SELECT * FROM readers WHERE email='$em' LIMIT 1");
    if (!$r || !$r->num_rows) return ['error' => 'ইমেইল বা পাসওয়ার্ড ভুল।'];
    $reader = $r->fetch_assoc();
    if (!password_verify($password, $reader['password'])) return ['error' => 'ইমেইল বা পাসওয়ার্ড ভুল।'];
    if (!$reader['is_active']) return ['error' => 'আপনার অ্যাকাউন্ট নিষ্ক্রিয় করা হয়েছে।'];

    $_SESSION['reader_id']   = $reader['id'];
    $_SESSION['reader_name'] = $reader['name'];
    $_SESSION['reader_email']= $reader['email'];
    return ['success' => true];
}

function readerLogout() {
    unset($_SESSION['reader_id'], $_SESSION['reader_name'], $_SESSION['reader_email']);
}

function readerIsBookmarked($post_id) {
    if (!readerIsLoggedIn()) return false;
    global $conn;
    $rid = (int)$_SESSION['reader_id'];
    $pid = (int)$post_id;
    $r   = $conn->query("SELECT id FROM reader_bookmarks WHERE reader_id=$rid AND post_id=$pid LIMIT 1");
    return $r && $r->num_rows > 0;
}

function readerGetReaction($post_id) {
    if (!readerIsLoggedIn()) return null;
    global $conn;
    $rid = (int)$_SESSION['reader_id'];
    $pid = (int)$post_id;
    $r   = $conn->query("SELECT reaction FROM reader_reactions WHERE reader_id=$rid AND post_id=$pid LIMIT 1");
    return ($r && $r->num_rows) ? $r->fetch_assoc()['reaction'] : null;
}

function getReactionCounts($post_id) {
    global $conn;
    $pid = (int)$post_id;
    $r   = $conn->query("SELECT reaction, COUNT(*) as cnt FROM reader_reactions WHERE post_id=$pid GROUP BY reaction");
    $counts = [];
    if ($r) while ($row = $r->fetch_assoc()) $counts[$row['reaction']] = (int)$row['cnt'];
    return $counts;
}

function getApprovedComments($post_id) {
    global $conn;
    $pid = (int)$post_id;
    $r   = $conn->query("SELECT rc.*, rd.name, rd.avatar FROM reader_comments rc
                         JOIN readers rd ON rc.reader_id=rd.id
                         WHERE rc.post_id=$pid AND rc.status='approved'
                         ORDER BY rc.created_at ASC");
    return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
}

function readerLogHistory($post_id) {
    if (!readerIsLoggedIn()) return;
    global $conn;
    $rid = (int)$_SESSION['reader_id'];
    $pid = (int)$post_id;
    $conn->query("INSERT INTO reader_history (reader_id,post_id) VALUES ($rid,$pid)
                  ON DUPLICATE KEY UPDATE viewed_at=NOW()");
}

<?php
require_once 'config.php';
if (!function_exists('readerIsLoggedIn')) require_once 'reader_auth.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success'=>false,'message'=>'invalid']); exit; }

global $conn;
$email = trim(strtolower($_POST['email'] ?? ''));
$name  = trim($_POST['name'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success'=>false,'message'=>'সঠিক ইমেইল ঠিকানা দিন।']); exit;
}

$em  = $conn->real_escape_string($email);
$rid = readerIsLoggedIn() ? (int)$_SESSION['reader_id'] : 'NULL';
$nm  = $conn->real_escape_string($name ?: ($_SESSION['reader_name'] ?? ''));

$r = $conn->query("SELECT id,is_active FROM newsletter_subscribers WHERE email='$em' LIMIT 1");
if ($r && $r->num_rows) {
    $sub = $r->fetch_assoc();
    if ($sub['is_active']) {
        echo json_encode(['success'=>false,'message'=>'এই ইমেইলে ইতিমধ্যে সাবস্ক্রিপশন সক্রিয় আছে।']); exit;
    }
    $conn->query("UPDATE newsletter_subscribers SET is_active=1 WHERE id={$sub['id']}");
} else {
    $conn->query("INSERT INTO newsletter_subscribers (email,reader_id,name) VALUES ('$em',$rid,'$nm')");
}

echo json_encode(['success'=>true,'message'=>'সফলভাবে সাবস্ক্রাইব করা হয়েছে! ধন্যবাদ।']);

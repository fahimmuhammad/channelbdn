<?php
/**
 * Database Setup Script
 * Run once: http://localhost/channelbdn/setup.php
 * Delete after setup!
 */
$host = 'localhost'; $user = 'root'; $pass = ''; $db = 'channelbdn';

// Connect without DB first
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->query("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($db);
$conn->set_charset('utf8mb4');

$sql = file_get_contents(__DIR__ . '/db_setup.sql');

// Fix admin password hash
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$sql = str_replace(
    "'\\$2y\\$10\\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'",
    "'" . $conn->real_escape_string($hash) . "'",
    $sql
);

// Execute each statement
$stmts = array_filter(array_map('trim', explode(';', $sql)));
$errors = []; $ok = 0;
foreach ($stmts as $s) {
    if (empty($s)) continue;
    if ($conn->query($s)) { $ok++; } else { $errors[] = $conn->error . ' — ' . substr($s, 0, 80); }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head><meta charset="UTF-8"><title>Database Setup</title>
<style>body{font-family:sans-serif;max-width:700px;margin:40px auto;padding:0 20px} .ok{color:#27ae60} .err{color:#e74c3c} pre{background:#f5f5f5;padding:12px;border-radius:6px;font-size:12px;overflow-x:auto}</style>
</head>
<body>
<h2>চ্যানেল বিডিএন — ডাটাবেস সেটআপ</h2>
<p class="ok"><strong><?= $ok ?> টি স্টেটমেন্ট সফলভাবে সম্পন্ন হয়েছে।</strong></p>
<?php if ($errors): ?>
<p class="err"><strong><?= count($errors) ?> টি ত্রুটি (কিছু ত্রুটি ডুপ্লিকেট থেকে হতে পারে, সাধারণত উপেক্ষযোগ্য):</strong></p>
<pre><?= implode("\n", array_slice($errors, 0, 10)) ?></pre>
<?php endif; ?>
<p class="ok"><strong>Admin credentials:</strong> username: <code>admin</code> | password: <code>admin123</code></p>
<p style="margin-top:20px">
  <a href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/channelbdn/' ?>" style="background:#c0392b;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;margin-right:10px">সাইট দেখুন</a>
  <a href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/channelbdn/admin/' ?>" style="background:#2c3e50;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none">অ্যাডমিন প্যানেল</a>
</p>
<p style="margin-top:16px;color:#888;font-size:13px"><strong>⚠️ নিরাপত্তার জন্য এই ফাইলটি সেটআপের পর মুছে ফেলুন।</strong></p>
</body></html>

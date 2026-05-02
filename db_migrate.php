<?php
/**
 * DB Migration Script
 * Run once: http://localhost/channelbdn/db_migrate.php
 * Delete after running!
 */
require_once 'config.php';

$raw = file_get_contents(__DIR__ . '/db_setup.sql');

// Strip single-line comments and blank lines, then split on ;
$lines = explode("\n", $raw);
$cleaned_lines = [];
foreach ($lines as $line) {
    $trimmed = trim($line);
    if ($trimmed === '' || strpos($trimmed, '--') === 0) continue;
    $cleaned_lines[] = $line;
}
$cleaned = implode("\n", $cleaned_lines);

$stmts = array_filter(array_map('trim', explode(';', $cleaned)));
$errors = []; $ok = 0;

foreach ($stmts as $s) {
    if (empty($s)) continue;
    if ($conn->query($s)) {
        $ok++;
    } else {
        // Suppress: duplicate column (1060), table already exists (1050), duplicate entry (1062)
        if (!in_array($conn->errno, [1060, 1050, 1062])) {
            $errors[] = '[' . $conn->errno . '] ' . $conn->error . ' — ' . substr($s, 0, 100);
        } else {
            $ok++; // count as ok since it means it was already done
        }
    }
}
?><!DOCTYPE html><html lang="bn"><head><meta charset="UTF-8"><title>DB Migration</title>
<style>body{font-family:sans-serif;max-width:700px;margin:40px auto;padding:0 20px}.ok{color:#27ae60}.err{color:#e74c3c}pre{background:#f5f5f5;padding:12px;border-radius:6px;font-size:12px;overflow-x:auto;white-space:pre-wrap}</style>
</head><body>
<h2>চ্যানেল বিডিএন — ডাটাবেস আপডেট</h2>
<p class="ok"><strong>✓ <?= $ok ?> টি স্টেটমেন্ট সফলভাবে সম্পন্ন হয়েছে।</strong></p>
<?php if ($errors): ?>
<p class="err"><strong><?= count($errors) ?> টি ত্রুটি:</strong></p>
<pre><?= htmlspecialchars(implode("\n\n", $errors)) ?></pre>
<?php else: ?>
<p class="ok"><strong>✓ কোনো ত্রুটি নেই। সব ঠিকঠাক!</strong></p>
<?php endif; ?>
<p class="ok" style="margin-top:16px"><strong>✓ মাইগ্রেশন সম্পন্ন! নতুন ফিচার সক্রিয় হয়েছে।</strong></p>
<p style="margin-top:12px">
  <a href="http://localhost/channelbdn/" style="background:#e8001c;color:#fff;padding:10px 20px;border-radius:4px;text-decoration:none">সাইট দেখুন</a>
  &nbsp;<a href="http://localhost/channelbdn/admin/" style="background:#2c3e50;color:#fff;padding:10px 20px;border-radius:4px;text-decoration:none">অ্যাডমিন প্যানেল</a>
</p>
<p style="margin-top:16px;color:#888;font-size:13px"><strong>⚠️ নিরাপত্তার জন্য এই ফাইলটি মুছে ফেলুন।</strong></p>
</body></html>

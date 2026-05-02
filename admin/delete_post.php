<?php
require_once 'auth.php';
requirePermission('delete_post');
require_once dirname(__DIR__) . '/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $conn->query("DELETE FROM posts WHERE id=$id");
    logActivity('delete_post', "পোস্ট আইডি $id মুছে ফেলা হয়েছে");
}
header('Location: posts.php?deleted=1');
exit;

<?php
require_once 'config.php';
require_once 'includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

$slug    = trim($_GET['slug'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 2));
$per     = 12;
$offset  = ($page - 1) * $per;

$posts = getPostsByCategory($slug, $per, $offset);
$out   = [];
foreach ($posts as $p) {
    $out[] = [
        'title'   => htmlspecialchars($p['title']),
        'slug'    => $p['slug'],
        'image'   => $p['image'],
        'cat_name'=> htmlspecialchars($p['category_name']),
        'cat_slug'=> $p['category_slug'],
        'excerpt' => htmlspecialchars(mb_substr($p['excerpt'] ?? '', 0, 100)),
        'time'    => timeAgo($p['created_at']),
    ];
}
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

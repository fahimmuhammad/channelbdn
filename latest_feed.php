<?php
require_once 'config.php';
require_once 'includes/functions.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$posts = getPosts(10);
$out = [];
foreach ($posts as $p) {
    $out[] = [
        'title'    => $p['title'],
        'slug'     => $p['slug'],
        'image'    => $p['image'],
        'category' => $p['category_name'],
        'cat_slug' => $p['category_slug'],
        'time'     => timeAgo($p['created_at']),
    ];
}
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

<?php
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$q = trim($_GET['q'] ?? '');
if (mb_strlen($q) < 2) { echo json_encode([]); exit; }

$q_esc = $conn->real_escape_string($q);
$r = $conn->query(
    "SELECT p.title, p.slug, p.image, c.name AS category_name
     FROM posts p JOIN categories c ON p.category_id = c.id
     WHERE p.status='published'
       AND (p.title LIKE '%$q_esc%' OR p.excerpt LIKE '%$q_esc%')
     ORDER BY p.created_at DESC LIMIT 6"
);
$results = $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

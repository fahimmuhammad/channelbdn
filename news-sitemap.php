<?php
require_once 'config.php';
require_once 'includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';

$site_name = getSetting('site_name') ?: SITE_NAME;
// Google News sitemap: articles from last 48 hours
$result = $conn->query("
    SELECT p.slug, p.title, p.created_at, c.name as cat_name
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.status='published'
      AND p.created_at >= DATE_SUB(NOW(), INTERVAL 48 HOUR)
    ORDER BY p.created_at DESC
    LIMIT 1000
");
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
  <?php while ($p = $result->fetch_assoc()): ?>
  <url>
    <loc><?= SITE_URL ?>/single.php?slug=<?= urlencode($p['slug']) ?></loc>
    <news:news>
      <news:publication>
        <news:name><?= htmlspecialchars($site_name) ?></news:name>
        <news:language>bn</news:language>
      </news:publication>
      <news:publication_date><?= date('c', strtotime($p['created_at'])) ?></news:publication_date>
      <news:title><?= htmlspecialchars($p['title']) ?></news:title>
    </news:news>
  </url>
  <?php endwhile; ?>
</urlset>

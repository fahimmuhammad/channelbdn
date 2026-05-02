<?php
require_once 'config.php';
require_once 'includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';

$result = $conn->query("SELECT slug, image, updated_at, created_at FROM posts WHERE status='published' ORDER BY created_at DESC LIMIT 50000");
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
  <?php while ($p = $result->fetch_assoc()): $mod = $p['updated_at'] ?: $p['created_at']; ?>
  <url>
    <loc><?= SITE_URL ?>/single.php?slug=<?= urlencode($p['slug']) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($mod)) ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
    <?php if ($p['image']): ?>
    <image:image>
      <image:loc><?= htmlspecialchars($p['image']) ?></image:loc>
    </image:image>
    <?php endif; ?>
  </url>
  <?php endwhile; ?>
</urlset>

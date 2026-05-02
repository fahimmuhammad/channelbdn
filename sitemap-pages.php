<?php
require_once 'config.php';
require_once 'includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';

$categories = getCategories();
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc><?= SITE_URL ?>/</loc>
    <changefreq>hourly</changefreq>
    <priority>1.0</priority>
    <lastmod><?= date('Y-m-d') ?></lastmod>
  </url>
  <?php foreach ($categories as $cat): ?>
  <url>
    <loc><?= SITE_URL ?>/category.php?slug=<?= urlencode($cat['slug']) ?></loc>
    <changefreq>hourly</changefreq>
    <priority>0.8</priority>
    <lastmod><?= date('Y-m-d') ?></lastmod>
  </url>
  <?php endforeach; ?>
  <url>
    <loc><?= SITE_URL ?>/search.php</loc>
    <changefreq>daily</changefreq>
    <priority>0.4</priority>
  </url>
</urlset>

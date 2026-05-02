<?php
require_once 'config.php';
require_once 'includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc><?= SITE_URL ?>/sitemap-pages.php</loc>
    <lastmod><?= date('Y-m-d') ?></lastmod>
  </sitemap>
  <sitemap>
    <loc><?= SITE_URL ?>/sitemap-posts.php</loc>
    <lastmod><?= date('Y-m-d') ?></lastmod>
  </sitemap>
  <sitemap>
    <loc><?= SITE_URL ?>/news-sitemap.php</loc>
    <lastmod><?= date('Y-m-d') ?></lastmod>
  </sitemap>
</sitemapindex>

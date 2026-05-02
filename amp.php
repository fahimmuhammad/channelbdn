<?php
require_once 'config.php';
require_once 'includes/functions.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: ' . SITE_URL); exit; }

$post = getPostBySlug($slug);
if (!$post) { header('HTTP/1.0 404 Not Found'); exit; }

$site_name  = getSetting('site_name') ?: SITE_NAME;
$meta_desc  = $post['excerpt'] ?: mb_substr(strip_tags($post['content']), 0, 160);
$canonical  = SITE_URL . '/single.php?slug=' . urlencode($post['slug']);
$amp_url    = SITE_URL . '/amp.php?slug='    . urlencode($post['slug']);
$og_image   = $post['image'] ?: SITE_URL . '/assets/img/og-default.jpg';
$content    = strip_tags($post['content'], '<p><br><b><strong><i><em><ul><ol><li><h2><h3><blockquote>');
?>
<!doctype html>
<html ⚡ lang="bn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
<title><?= htmlspecialchars($post['title']) ?> | <?= htmlspecialchars($site_name) ?></title>
<meta name="description" content="<?= htmlspecialchars($meta_desc) ?>">
<link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
<meta property="og:type"        content="article">
<meta property="og:title"       content="<?= htmlspecialchars($post['title']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($meta_desc) ?>">
<meta property="og:image"       content="<?= htmlspecialchars($og_image) ?>">
<meta property="og:url"         content="<?= htmlspecialchars($canonical) ?>">
<script async src="https://cdn.ampproject.org/v0.js"></script>
<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style>
<noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
<style amp-custom>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Hind Siliguri',sans-serif;background:#f4f4f4;color:#111;font-size:17px;line-height:1.8}
a{color:#e8001c;text-decoration:none}
.amp-header{background:#fff;border-bottom:3px solid #e8001c;padding:12px 16px;display:flex;align-items:center;justify-content:space-between}
.amp-logo{font-size:22px;font-weight:900;color:#111}
.amp-logo span{color:#e8001c}
.amp-article{background:#fff;margin:12px;border-radius:4px;padding:20px;max-width:800px;margin-left:auto;margin-right:auto}
.amp-category{display:inline-block;background:#e8001c;color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:2px;margin-bottom:10px;text-transform:uppercase}
h1{font-size:26px;font-weight:900;line-height:1.4;margin-bottom:12px;color:#111}
.amp-meta{font-size:12px;color:#888;display:flex;gap:14px;flex-wrap:wrap;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid #eee}
.amp-meta span{display:flex;align-items:center;gap:4px}
.amp-img-wrap{margin-bottom:16px;border-radius:4px;overflow:hidden}
.amp-content{font-size:17px;line-height:1.9;color:#222}
.amp-content p{margin-bottom:14px}
.amp-content h2,.amp-content h3{font-size:20px;font-weight:700;margin:18px 0 8px}
.amp-content blockquote{border-left:4px solid #e8001c;padding:10px 16px;background:#fff5f5;margin:14px 0;font-style:italic}
.amp-footer{background:#1a1f2e;color:#aaa;text-align:center;padding:20px;font-size:13px;margin-top:24px}
.amp-footer a{color:#e8001c}
.amp-view-full{display:block;text-align:center;margin:20px 0;padding:12px 24px;background:#e8001c;color:#fff;border-radius:4px;font-weight:700;font-size:14px}
</style>
</head>
<body>
<header class="amp-header">
    <a href="<?= SITE_URL ?>/" class="amp-logo"><?= htmlspecialchars(mb_substr($site_name,0,3)) ?><span><?= htmlspecialchars(mb_substr($site_name,3)) ?></span></a>
    <a href="<?= htmlspecialchars($canonical) ?>" style="font-size:12px;color:#888">পূর্ণ সংস্করণ</a>
</header>

<article class="amp-article">
    <a href="<?= SITE_URL ?>/category.php?slug=<?= $post['category_slug'] ?>" class="amp-category"><?= htmlspecialchars($post['category_name']) ?></a>
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <div class="amp-meta">
        <span>✍ <?= htmlspecialchars($post['author']) ?></span>
        <span>📅 <?= date('d/m/Y', strtotime($post['created_at'])) ?></span>
        <span>👁 <?= number_format($post['views']) ?></span>
    </div>

    <?php if ($post['image']): ?>
    <div class="amp-img-wrap">
        <amp-img src="<?= htmlspecialchars($post['image']) ?>"
                 width="800" height="450"
                 layout="responsive"
                 alt="<?= htmlspecialchars($post['title']) ?>">
        </amp-img>
    </div>
    <?php endif; ?>

    <div class="amp-content"><?= $content ?></div>

    <a href="<?= htmlspecialchars($canonical) ?>" class="amp-view-full">🔗 পূর্ণ সংস্করণে পড়ুন</a>
</article>

<footer class="amp-footer">
    <p>&copy; <?= date('Y') ?> <a href="<?= SITE_URL ?>"><?= htmlspecialchars($site_name) ?></a> — সত্যের সন্ধানে সর্বদা</p>
</footer>
</body>
</html>

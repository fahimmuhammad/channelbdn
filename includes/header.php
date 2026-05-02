<?php
require_once dirname(__DIR__) . '/includes/functions.php';
if (!function_exists('readerIsLoggedIn')) require_once dirname(__DIR__) . '/reader_auth.php';
$categories = getCategories();
$breaking = getBreakingNews(8);
$current_page = basename($_SERVER['PHP_SELF']);
$site_name    = getSetting('site_name') ?: SITE_NAME;
$site_tagline = getSetting('site_tagline') ?: SITE_TAGLINE;

// SEO vars — pages set these before including header.php
$seo_title       = isset($page_title)    ? htmlspecialchars($page_title) . ' | ' . htmlspecialchars($site_name) : htmlspecialchars($site_name) . ' | ' . htmlspecialchars($site_tagline);
$seo_desc        = isset($meta_desc)     ? htmlspecialchars($meta_desc)       : htmlspecialchars($site_tagline);
$seo_image       = isset($og_image)      ? $og_image                           : SITE_URL . '/assets/img/og-default.jpg';
$seo_url         = isset($canonical_url) ? $canonical_url                      : SITE_URL . $_SERVER['REQUEST_URI'];
$seo_type        = isset($og_type)       ? $og_type                            : 'website';
$seo_article_author = isset($article_author) ? htmlspecialchars($article_author) : htmlspecialchars($site_name);
$seo_published   = isset($article_published) ? $article_published : '';
$seo_modified    = isset($article_modified)  ? $article_modified  : '';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
<script>(function(){if(localStorage.getItem('darkMode')==='1')document.documentElement.setAttribute('data-theme','dark');}());</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $seo_title ?></title>
    <meta name="description" content="<?= $seo_desc ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="<?= htmlspecialchars($seo_url) ?>">
    <?php if (isset($amp_url)): ?><link rel="amphtml" href="<?= htmlspecialchars($amp_url) ?>"><?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:type"        content="<?= htmlspecialchars($seo_type) ?>">
    <meta property="og:title"       content="<?= $seo_title ?>">
    <meta property="og:description" content="<?= $seo_desc ?>">
    <meta property="og:url"         content="<?= htmlspecialchars($seo_url) ?>">
    <?php $og_img_url = (isset($og_image) && $og_image && strpos($og_image, 'og-default') === false) ? SITE_URL . '/og_image.php?slug=' . urlencode($_GET['slug'] ?? '') : $seo_image; ?>
    <meta property="og:image"       content="<?= htmlspecialchars($og_img_url) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name"   content="<?= htmlspecialchars($site_name) ?>">
    <meta property="og:locale"      content="bn_BD">
    <?php if ($seo_type === 'article'): ?>
    <meta property="article:published_time" content="<?= $seo_published ?>">
    <meta property="article:modified_time"  content="<?= $seo_modified ?>">
    <meta property="article:author"         content="<?= $seo_article_author ?>">
    <meta property="article:section"        content="<?= isset($article_section) ? htmlspecialchars($article_section) : '' ?>">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= $seo_title ?>">
    <meta name="twitter:description" content="<?= $seo_desc ?>">
    <meta name="twitter:image"       content="<?= htmlspecialchars($seo_image) ?>">
    <?php $tw = getSetting('twitter'); if ($tw): $tw_handle = preg_replace('#https?://(www\.)?twitter\.com/|https?://(www\.)?x\.com/#','@',$tw); ?>
    <meta name="twitter:site"        content="<?= htmlspecialchars($tw_handle) ?>">
    <?php endif; ?>

    <!-- hreflang -->
    <link rel="alternate" hreflang="bn" href="<?= htmlspecialchars($seo_url) ?>">
    <link rel="alternate" hreflang="x-default" href="<?= htmlspecialchars($seo_url) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Tiro+Bangla:ital@0;1&family=Noto+Serif+Bengali:wght@600;700;800;900&family=Noto+Naskh+Arabic:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body data-siteurl="<?= SITE_URL ?>">
<div id="reading-progress"></div>

<!-- Live Feed Panel -->
<div class="live-feed-overlay" id="liveFeedOverlay"></div>
<button class="live-feed-btn" id="liveFeedBtn" title="সর্বশেষ সংবাদ">সর্বশেষ</button>
<div class="live-feed-panel" id="liveFeedPanel">
    <div class="live-feed-header">
        <h4><span class="live-feed-dot"></span> সর্বশেষ সংবাদ</h4>
        <button class="live-feed-close" id="liveFeedClose" title="বন্ধ করুন">&times;</button>
    </div>
    <div class="live-feed-body" id="liveFeedBody"></div>
    <div class="live-feed-footer">প্রতি ৬০ সেকেন্ডে স্বয়ংক্রিয়ভাবে আপডেট হয়</div>
</div>

<!-- Utility Navigation -->
<div class="utility-nav">
    <div class="container">
        <div class="utility-nav-left">
            <a href="#"><i class="far fa-newspaper"></i> আজকের পত্রিকা</a>
            <a href="#"><i class="fas fa-tablet-alt"></i> ই-পেপার</a>
            <a href="#"><i class="fas fa-book-open"></i> ম্যাগাজিন</a>
            <a href="#"><i class="fas fa-archive"></i> আর্কাইভ</a>
            <a href="#"><i class="fas fa-share-alt"></i> সোশ্যাল মিডিয়া</a>
            <a href="<?= SITE_URL ?>/weather.php"><i class="fas fa-cloud-sun"></i> আবহাওয়া</a>
            <a href="#"><i class="fas fa-language"></i> বাংলা কনভার্টার</a>
        </div>
        <div class="utility-nav-right">
            <!-- Reader Account -->
            <?php if (readerIsLoggedIn()): ?>
            <a href="<?= SITE_URL ?>/reader_profile.php" class="reader-nav-link" title="<?= htmlspecialchars($_SESSION['reader_name']) ?>">
                <i class="fas fa-user-circle"></i> <?= htmlspecialchars(explode(' ', $_SESSION['reader_name'])[0]) ?>
            </a>
            <?php else: ?>
            <a href="<?= SITE_URL ?>/reader_login.php" class="reader-nav-link reader-nav-login" title="লগইন">
                <i class="fas fa-sign-in-alt"></i> লগইন
            </a>
            <?php endif; ?>
            <!-- Dark Mode Toggle -->
            <button class="dark-mode-btn" id="darkModeToggle" title="ডার্ক মোড"><i class="fas fa-moon" id="darkIcon"></i></button>
            <!-- Language Toggle -->
            <div class="lang-toggle-wrap">
                <button class="lang-btn active" id="langBn" onclick="setLang('bn')" title="বাংলা">বাং</button>
                <span class="lang-sep">|</span>
                <button class="lang-btn" id="langEn" onclick="setLang('en')" title="English">EN</button>
                <div id="google_translate_element" style="display:none"></div>
            </div>
            <a href="<?= getSetting('facebook') ?: '#' ?>" target="_blank" title="ফেসবুক"><i class="fab fa-facebook-f"></i></a>
            <a href="<?= getSetting('youtube') ?: '#' ?>" target="_blank" title="ইউটিউব"><i class="fab fa-youtube"></i></a>
            <a href="<?= getSetting('twitter') ?: '#' ?>" target="_blank" title="টুইটার"><i class="fab fa-x-twitter"></i></a>
            <a href="<?= getSetting('instagram') ?: '#' ?>" target="_blank" title="ইনস্টাগ্রাম"><i class="fab fa-instagram"></i></a>
        </div>
    </div>
</div>

<!-- Site Header -->
<header class="site-header">
    <div class="container">
        <div class="header-inner">
            <!-- Left Ad -->
            <div class="header-ad">
                <?php $ad_left = getAd('header_left'); ?>
                <?php if ($ad_left): ?>
                    <a href="<?= htmlspecialchars($ad_left['link_url']) ?>" target="_blank">
                        <img src="<?= htmlspecialchars($ad_left['image_url']) ?>" alt="বিজ্ঞাপন">
                    </a>
                <?php else: ?>
                    <div class="header-ad-placeholder">বিজ্ঞাপন ৩০০×৮০</div>
                <?php endif; ?>
            </div>

            <!-- Logo -->
            <div class="site-branding">
                <a href="<?= SITE_URL ?>/" class="site-logo">
                    <div class="logo-text"><?= htmlspecialchars($site_name) ?></div>
                    <p class="logo-tagline"><?= htmlspecialchars($site_tagline) ?></p>
                </a>
            </div>

            <!-- Right Ad -->
            <div class="header-ad">
                <?php $ad_right = getAd('header_right'); ?>
                <?php if ($ad_right): ?>
                    <a href="<?= htmlspecialchars($ad_right['link_url']) ?>" target="_blank">
                        <img src="<?= htmlspecialchars($ad_right['image_url']) ?>" alt="বিজ্ঞাপন">
                    </a>
                <?php else: ?>
                    <div class="header-ad-placeholder">বিজ্ঞাপন ৩০০×৮০</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Main Navigation -->
<nav class="main-nav" id="mainNav">
    <div class="container" style="position:relative">
        <div class="nav-wrapper">
            <a href="<?= SITE_URL ?>/" class="nav-home-btn <?= $current_page == 'index.php' ? 'active' : '' ?>" title="হোম">
                <i class="fas fa-home"></i>
            </a>
            <ul class="nav-menu" id="navMenu">
                <li><a href="<?= SITE_URL ?>/?latest=1" class="<?= (isset($_GET['latest'])) ? 'active' : '' ?>">সর্বশেষ</a></li>
                <?php foreach ($categories as $cat): ?>
                <li><a href="<?= SITE_URL ?>/category.php?slug=<?= $cat['slug'] ?>" class="<?= (isset($active_category) && $active_category == $cat['slug']) ? 'active' : '' ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
            <div class="nav-right">
                <button class="nav-search-btn" id="searchToggle" aria-label="অনুসন্ধান"><i class="fas fa-search"></i></button>
                <div class="nav-mega-wrap">
                    <button class="nav-toggle" aria-label="সব বিভাগ"><i class="fas fa-bars"></i></button>
                    <div class="mega-menu" id="megaMenu">
                        <div class="mega-menu-inner">
                            <div class="mega-menu-top">
                                <a href="<?= SITE_URL ?>/?latest=1"><i class="fas fa-clock"></i> সর্বশেষ</a>
                                <a href="<?= SITE_URL ?>/search.php"><i class="fas fa-search"></i> অনুসন্ধান</a>
                                <a href="<?= SITE_URL ?>/weather.php"><i class="fas fa-cloud-sun"></i> আবহাওয়া</a>
                                <a href="#"><i class="fas fa-play-circle"></i> ভিডিও</a>
                                <a href="#"><i class="fas fa-camera"></i> ফটোগ্যালারি</a>
                            </div>
                            <div class="mega-menu-grid">
                                <?php foreach ($categories as $cat): ?>
                                <a href="<?= SITE_URL ?>/category.php?slug=<?= $cat['slug'] ?>" class="mega-menu-item <?= (isset($active_category) && $active_category == $cat['slug']) ? 'active' : '' ?>">
                                    <i class="fas fa-angle-right"></i> <?= htmlspecialchars($cat['name']) ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Search Box -->
        <div class="nav-search-box" id="searchBox">
            <form action="<?= SITE_URL ?>/search.php" method="GET">
                <input type="search" name="q" placeholder="সংবাদ খুঁজুন..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit"><i class="fas fa-search"></i> খুঁজুন</button>
            </form>
        </div>
    </div>
</nav>

<!-- Breaking News Ticker -->
<?php if (!empty($breaking)): ?>
<div class="breaking-news-bar">
    <div class="container">
        <div class="breaking-inner">
            <span class="breaking-label"><i class="fas fa-bolt"></i> ব্রেকিং</span>
            <div class="ticker-wrapper">
                <div class="ticker-content" id="ticker">
                    <?php foreach ($breaking as $b): ?>
                    <a href="<?= SITE_URL ?>/single.php?slug=<?= htmlspecialchars($b['slug']) ?>"><?= htmlspecialchars($b['title']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Date Bar -->
<div class="date-bar">
    <div class="container">
        <div class="date-trio">
            <span class="date-item date-en"><?= getCurrentDateBengali() ?></span>
            <span class="date-sep">|</span>
            <span class="date-item date-bn"><?= getCurrentDateBangabda() ?></span>
            <span class="date-sep">|</span>
            <span class="date-item date-ar" id="hijri-date">—</span>
        </div>
        <div class="date-bar-right">
            <span id="live-clock"></span>
            <span class="weather-widget" id="weather-display"><i class="fas fa-cloud-sun"></i> ঢাকা —°C</span>
        </div>
    </div>
</div>

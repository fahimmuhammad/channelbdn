<?php
require_once 'config.php';
require_once 'includes/functions.php';

$featured      = getPosts(1, null, 1);
$hero_side     = getPosts(8, null, null, null, 1);
$curated       = getCuratedPosts(8);
$national_lead = getPostsByCategory('national', 1);
$national_mid  = getPostsByCategory('national', 3, 1);
$national_right= getPostsByCategory('national', 6, 4);
$politics_lead = getPostsByCategory('politics', 1);
$politics_sub  = getPostsByCategory('politics', 9, 1);
$business_main = getPostsByCategory('economy', 3);
$business_list = getPostsByCategory('economy', 6, 3);
$saradesh_posts= getPostsByCategory('national', 8, 10);
$world_posts   = getPostsByCategory('international', 8);
$opinion_posts = getPostsByCategory('opinion', 5);
$popular_posts = getPopularPosts(10);
$latest_posts  = getPosts(10);
$ent_posts     = getPostsByCategory('entertainment', 8);
$sports_main   = getPostsByCategory('sports', 1);
$sports_left   = getPostsByCategory('sports', 3, 1);
$sports_right  = getPostsByCategory('sports', 3, 4);
$videos        = getVideos(10);
$gallery       = getGalleryPhotos(5);
$active_poll   = getActivePoll();
$ad_strip      = getAd('content_strip');
$sections      = getHomepageSections();

$page_title    = '';
$canonical_url = SITE_URL . '/';

include 'includes/header.php';

// JSON-LD: WebSite + Organization
$_sn = getSetting('site_name') ?: SITE_NAME;
$_st = getSetting('site_tagline') ?: SITE_TAGLINE;
echo '<script type="application/ld+json">' . json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'WebSite',
    'name'            => $_sn,
    'url'             => SITE_URL . '/',
    'description'     => $_st,
    'inLanguage'      => 'bn',
    'potentialAction' => ['@type'=>'SearchAction','target'=> SITE_URL . '/search.php?q={search_term_string}','query-input'=>'required name=search_term_string'],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';

echo '<script type="application/ld+json">' . json_encode([
    '@context'  => 'https://schema.org',
    '@type'     => 'NewsMediaOrganization',
    'name'      => $_sn,
    'url'       => SITE_URL . '/',
    'logo'      => ['@type'=>'ImageObject','url'=> SITE_URL . '/assets/img/logo.png'],
    'sameAs'    => array_filter([getSetting('facebook'),getSetting('youtube'),getSetting('twitter'),getSetting('instagram')]),
    'contactPoint' => ['@type'=>'ContactPoint','contactType'=>'customer service','email'=> getSetting('email') ?: ''],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
?>

<div class="page-wrap">
<div class="container">

<!-- ===== HERO ===== -->
<?php if (!empty($featured)): $h = $featured[0]; ?>
<div class="home-section">
    <div class="hero-grid">
        <div>
            <a href="<?= SITE_URL ?>/single.php?slug=<?= $h['slug'] ?>">
                <div class="hero-main-img">
                    <img src="<?= htmlspecialchars($h['image']) ?>" alt="<?= htmlspecialchars($h['title']) ?>" loading="lazy">
                </div>
            </a>
            <div class="hero-main-body">
                <a href="<?= SITE_URL ?>/category.php?slug=<?= $h['category_slug'] ?>" class="cat-badge"><?= htmlspecialchars($h['category_name']) ?></a>
                <a href="<?= SITE_URL ?>/single.php?slug=<?= $h['slug'] ?>">
                    <div class="hero-main-title"><?= htmlspecialchars($h['title']) ?></div>
                </a>
                <?php if ($h['excerpt']): ?><p class="hero-main-excerpt"><?= htmlspecialchars(mb_substr($h['excerpt'],0,160)) ?>...</p><?php endif; ?>
                <div class="news-card-meta" style="margin-top:8px"><i class="far fa-clock"></i> <?= timeAgo($h['created_at']) ?> &nbsp;|&nbsp; <i class="fas fa-user"></i> <?= htmlspecialchars($h['author']) ?></div>
            </div>
        </div>
        <div class="hero-side">
            <?php foreach ($hero_side as $s): ?>
            <a href="<?= SITE_URL ?>/single.php?slug=<?= $s['slug'] ?>" class="hero-side-item">
                <div class="hero-side-img"><img src="<?= htmlspecialchars($s['image']) ?>" alt="<?= htmlspecialchars($s['title']) ?>" loading="lazy"></div>
                <div>
                    <div class="hero-side-title"><?= htmlspecialchars($s['title']) ?></div>
                    <div class="hero-side-time"><i class="far fa-clock"></i> <?= timeAgo($s['created_at']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== AD STRIP ===== -->
<div class="ad-strip">
    <?php if ($ad_strip): ?>
        <a href="<?= htmlspecialchars($ad_strip['link_url']) ?>" target="_blank"><img src="<?= htmlspecialchars($ad_strip['image_url']) ?>" alt="বিজ্ঞাপন"></a>
    <?php else: ?>
        <div class="ad-placeholder">বিজ্ঞাপন ৯৭০×৯০</div>
    <?php endif; ?>
</div>

<!-- ===== CURATED ===== -->
<?php if (!empty($curated) && isSectionActive($sections, 'curated')): ?>
<div class="home-section">
    <div class="section-header">
        <div class="section-title"><span class="section-icon"><i class="fas fa-star"></i></span> বাছাইকৃত</div>
        <a href="<?= SITE_URL ?>/" class="section-more">সব <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="curated-grid">
        <?php foreach ($curated as $p): ?>
        <a href="<?= SITE_URL ?>/single.php?slug=<?= $p['slug'] ?>" class="curated-card">
            <div class="curated-card-img"><img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy"></div>
            <div class="curated-card-body">
                <div class="curated-card-title"><?= htmlspecialchars($p['title']) ?></div>
                <div class="curated-card-meta"><i class="far fa-clock"></i> <?= timeAgo($p['created_at']) ?></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ===== NATIONAL ===== -->
<?php if (!empty($national_lead) && isSectionActive($sections, 'national')): ?>
<div class="home-section">
    <div class="section-header">
        <div class="section-title"><span class="section-icon"><i class="fas fa-flag"></i></span> জাতীয়</div>
        <a href="<?= SITE_URL ?>/category.php?slug=national" class="section-more">সব জাতীয় <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="national-grid">
        <?php $nl = $national_lead[0]; ?>
        <a href="<?= SITE_URL ?>/single.php?slug=<?= $nl['slug'] ?>" class="national-lead">
            <div class="national-lead-img"><img src="<?= htmlspecialchars($nl['image']) ?>" alt="<?= htmlspecialchars($nl['title']) ?>" loading="lazy"></div>
            <div class="national-lead-body">
                <div class="national-lead-title"><?= htmlspecialchars($nl['title']) ?></div>
                <div class="time-meta"><i class="far fa-clock"></i> <?= timeAgo($nl['created_at']) ?></div>
            </div>
        </a>
        <div class="national-mid">
            <?php foreach ($national_mid as $nm): ?>
            <a href="<?= SITE_URL ?>/single.php?slug=<?= $nm['slug'] ?>" class="national-mid-item">
                <div class="national-mid-img"><img src="<?= htmlspecialchars($nm['image']) ?>" alt="<?= htmlspecialchars($nm['title']) ?>" loading="lazy"></div>
                <div class="national-mid-body">
                    <div class="national-mid-title"><?= htmlspecialchars($nm['title']) ?></div>
                    <div class="time-meta"><i class="far fa-clock"></i> <?= timeAgo($nm['created_at']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="national-right">
            <?php foreach ($national_right as $nr): ?>
            <a href="<?= SITE_URL ?>/single.php?slug=<?= $nr['slug'] ?>" class="national-right-item">
                <?php if ($nr['image']): ?>
                <div class="national-right-img"><img src="<?= htmlspecialchars($nr['image']) ?>" alt="<?= htmlspecialchars($nr['title']) ?>" loading="lazy"></div>
                <?php endif; ?>
                <div>
                    <div class="national-right-title"><?= htmlspecialchars($nr['title']) ?></div>
                    <div class="time-meta"><i class="far fa-clock"></i> <?= timeAgo($nr['created_at']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== POLITICS ===== -->
<?php if (!empty($politics_lead) && isSectionActive($sections, 'politics')): ?>
<div class="home-section">
    <div class="section-header">
        <div class="section-title"><span class="section-icon"><i class="fas fa-landmark"></i></span> রাজনীতি</div>
        <a href="<?= SITE_URL ?>/category.php?slug=politics" class="section-more">সব রাজনীতি <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="politics-grid">
        <?php $pl = $politics_lead[0]; ?>
        <a href="<?= SITE_URL ?>/single.php?slug=<?= $pl['slug'] ?>" class="politics-lead">
            <div class="politics-lead-img"><img src="<?= htmlspecialchars($pl['image']) ?>" alt="<?= htmlspecialchars($pl['title']) ?>" loading="lazy"></div>
            <div class="politics-lead-body">
                <div class="politics-lead-title"><?= htmlspecialchars($pl['title']) ?></div>
                <?php if ($pl['excerpt']): ?><p class="news-card-excerpt"><?= htmlspecialchars(mb_substr($pl['excerpt'],0,120)) ?>...</p><?php endif; ?>
                <div class="time-meta"><i class="far fa-clock"></i> <?= timeAgo($pl['created_at']) ?></div>
            </div>
        </a>
        <div>
            <div class="politics-sub-grid">
                <?php foreach ($politics_sub as $ps): ?>
                <a href="<?= SITE_URL ?>/single.php?slug=<?= $ps['slug'] ?>" class="politics-sub-item">
                    <div class="politics-sub-img"><img src="<?= htmlspecialchars($ps['image']) ?>" alt="<?= htmlspecialchars($ps['title']) ?>" loading="lazy"></div>
                    <div class="politics-sub-title"><?= htmlspecialchars($ps['title']) ?></div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== BUSINESS ===== -->
<?php if (!empty($business_main) && isSectionActive($sections, 'business')): ?>
<div class="home-section">
    <div class="section-header">
        <div class="section-title"><span class="section-icon"><i class="fas fa-chart-line"></i></span> বাণিজ্য</div>
        <a href="<?= SITE_URL ?>/category.php?slug=economy" class="section-more">সব বাণিজ্য <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="business-grid">
        <div class="business-left">
            <?php foreach ($business_main as $bm): ?>
            <a href="<?= SITE_URL ?>/single.php?slug=<?= $bm['slug'] ?>" class="business-item">
                <div class="business-item-img"><img src="<?= htmlspecialchars($bm['image']) ?>" alt="<?= htmlspecialchars($bm['title']) ?>" loading="lazy"></div>
                <div class="business-item-body">
                    <div class="business-item-title"><?= htmlspecialchars($bm['title']) ?></div>
                    <?php if ($bm['excerpt']): ?><div class="business-item-excerpt"><?= htmlspecialchars(mb_substr($bm['excerpt'],0,100)) ?>...</div><?php endif; ?>
                    <div class="time-meta" style="margin-top:6px"><i class="far fa-clock"></i> <?= timeAgo($bm['created_at']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="business-right">
            <div class="business-right-header">সর্বশেষ বাণিজ্য</div>
            <?php foreach ($business_list as $bl): ?>
            <a href="<?= SITE_URL ?>/single.php?slug=<?= $bl['slug'] ?>" class="business-list-item">
                <div class="business-list-img"><img src="<?= htmlspecialchars($bl['image']) ?>" alt="<?= htmlspecialchars($bl['title']) ?>" loading="lazy"></div>
                <div>
                    <div class="business-list-title"><?= htmlspecialchars($bl['title']) ?></div>
                    <div class="time-meta"><i class="far fa-clock"></i> <?= timeAgo($bl['created_at']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== AROUND BD ===== -->
<?php if (!empty($saradesh_posts) && isSectionActive($sections, 'around_bd')): ?>
<div class="home-section">
    <div class="section-header">
        <div class="section-title"><span class="section-icon"><i class="fas fa-map-marked-alt"></i></span> সারাদেশ</div>
        <a href="<?= SITE_URL ?>/category.php?slug=national" class="section-more">সব সারাদেশ <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="around-bd-grid">
        <div class="around-posts-grid">
            <?php foreach ($saradesh_posts as $ap): ?>
            <a href="<?= SITE_URL ?>/single.php?slug=<?= $ap['slug'] ?>" class="around-card">
                <div class="around-card-img"><img src="<?= htmlspecialchars($ap['image']) ?>" alt="<?= htmlspecialchars($ap['title']) ?>" loading="lazy"></div>
                <div class="around-card-body">
                    <div class="around-card-title"><?= htmlspecialchars($ap['title']) ?></div>
                    <div class="time-meta"><i class="far fa-clock"></i> <?= timeAgo($ap['created_at']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="local-news-widget">
            <div class="local-news-widget-header"><i class="fas fa-map-pin" style="color:var(--primary)"></i> আমার এলাকার সংবাদ</div>
            <div class="local-news-widget-body">
                <select id="sel-division" onchange="loadDistricts(this.value)"><option value="">-- বিভাগ নির্বাচন --</option><option>ঢাকা</option><option>চট্টগ্রাম</option><option>রাজশাহী</option><option>খুলনা</option><option>বরিশাল</option><option>সিলেট</option><option>রংপুর</option><option>ময়মনসিংহ</option></select>
                <select id="sel-district" onchange="loadUpazilas(this.value)"><option value="">-- জেলা নির্বাচন --</option></select>
                <select id="sel-upazila"><option value="">-- উপজেলা নির্বাচন --</option></select>
                <button class="local-search-btn" onclick="localSearch()"><i class="fas fa-search"></i> অনুসন্ধান</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== WORLD ===== -->
<?php if (!empty($world_posts) && isSectionActive($sections, 'world')): ?>
<div class="home-section">
    <div class="section-header">
        <div class="section-title"><span class="section-icon"><i class="fas fa-globe-asia"></i></span> বিশ্ব</div>
        <a href="<?= SITE_URL ?>/category.php?slug=international" class="section-more">সব বিশ্ব <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="world-grid">
        <?php foreach ($world_posts as $wp): ?>
        <a href="<?= SITE_URL ?>/single.php?slug=<?= $wp['slug'] ?>" class="world-card">
            <div class="world-card-img"><img src="<?= htmlspecialchars($wp['image']) ?>" alt="<?= htmlspecialchars($wp['title']) ?>" loading="lazy"></div>
            <div class="world-card-body">
                <div class="world-card-title"><?= htmlspecialchars($wp['title']) ?></div>
                <div class="time-meta" style="margin-top:6px"><i class="far fa-clock"></i> <?= timeAgo($wp['created_at']) ?></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ===== OPINION + POLL + MOST READ ===== -->
<?php if (isSectionActive($sections, 'opinion')): ?>
<div class="home-section">
    <div class="section-header">
        <div class="section-title"><span class="section-icon"><i class="fas fa-pen-nib"></i></span> মতামত</div>
        <a href="<?= SITE_URL ?>/category.php?slug=opinion" class="section-more">সব মতামত <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="opinion-grid">
        <div class="opinion-list">
            <?php foreach ($opinion_posts as $op): ?>
            <a href="<?= SITE_URL ?>/single.php?slug=<?= $op['slug'] ?>" class="opinion-item">
                <div class="opinion-avatar"><img src="https://picsum.photos/seed/<?= $op['id'] ?>au/100/100" alt="<?= htmlspecialchars($op['author']) ?>"></div>
                <div>
                    <div class="opinion-item-title"><?= htmlspecialchars($op['title']) ?></div>
                    <div class="opinion-item-excerpt"><?= htmlspecialchars(mb_substr($op['excerpt'] ?? '',0,100)) ?>...</div>
                    <div class="opinion-author"><?= htmlspecialchars($op['author']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="poll-widget">
            <div class="poll-widget-header"><i class="fas fa-poll"></i> অনলাইন জরিপ</div>
            <div class="poll-widget-body">
                <?php if ($active_poll): ?>
                <div class="poll-question"><?= htmlspecialchars($active_poll['question']) ?></div>
                <form method="POST" action="<?= SITE_URL ?>/vote.php">
                    <input type="hidden" name="poll_id" value="<?= $active_poll['id'] ?>">
                    <?php for ($i=1;$i<=3;$i++): if (!empty($active_poll["option$i"])): ?>
                    <div class="poll-option"><label><input type="radio" name="option" value="<?= $i ?>"> <?= htmlspecialchars($active_poll["option$i"]) ?></label></div>
                    <?php endif; endfor; ?>
                    <button type="submit" class="poll-submit"><i class="fas fa-paper-plane"></i> ভোট দিন</button>
                </form>
                <?php $tv = $active_poll['votes1']+$active_poll['votes2']+$active_poll['votes3']; ?>
                <div class="poll-votes">মোট ভোটদাতা: <?= number_format($tv) ?> জন</div>
                <?php else: ?>
                <p style="font-size:13px;color:var(--text-light);text-align:center;padding:20px 0">কোনো সক্রিয় জরিপ নেই</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="most-read-widget">
            <div class="most-read-tabs">
                <div class="most-read-tab active" onclick="switchTab(this,'latest')">সর্বশেষ</div>
                <div class="most-read-tab" onclick="switchTab(this,'popular')">জনপ্রিয়</div>
            </div>
            <div id="tab-popular" style="display:none">
                <?php foreach (array_slice($popular_posts,0,7) as $i=>$pp): ?>
                <a href="<?= SITE_URL ?>/single.php?slug=<?= $pp['slug'] ?>" class="most-read-item">
                    <div class="most-read-num"><?= $i+1 ?></div>
                    <div class="most-read-title"><?= htmlspecialchars($pp['title']) ?></div>
                </a>
                <?php endforeach; ?>
            </div>
            <div id="tab-latest">
                <?php foreach ($latest_posts as $i=>$lp): ?>
                <a href="<?= SITE_URL ?>/single.php?slug=<?= $lp['slug'] ?>" class="most-read-item">
                    <div class="most-read-num"><?= $i+1 ?></div>
                    <div class="most-read-title"><?= htmlspecialchars($lp['title']) ?></div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== ENTERTAINMENT ===== -->
<?php if (!empty($ent_posts) && isSectionActive($sections, 'entertainment')): ?>
<div class="home-section">
    <div class="section-header">
        <div class="section-title"><span class="section-icon"><i class="fas fa-film"></i></span> বিনোদন</div>
        <a href="<?= SITE_URL ?>/category.php?slug=entertainment" class="section-more">সব বিনোদন <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="ent-grid">
        <?php foreach ($ent_posts as $ep): ?>
        <a href="<?= SITE_URL ?>/single.php?slug=<?= $ep['slug'] ?>" class="ent-card">
            <div class="ent-card-img"><img src="<?= htmlspecialchars($ep['image']) ?>" alt="<?= htmlspecialchars($ep['title']) ?>" loading="lazy"></div>
            <div class="ent-card-body">
                <div class="ent-card-title"><?= htmlspecialchars($ep['title']) ?></div>
                <div class="time-meta" style="margin-top:4px"><i class="far fa-clock"></i> <?= timeAgo($ep['created_at']) ?></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ===== SPORTS ===== -->
<?php if (!empty($sports_main) && isSectionActive($sections, 'sports')): ?>
<div class="home-section">
    <div class="section-header">
        <div class="section-title"><span class="section-icon"><i class="fas fa-futbol"></i></span> খেলা</div>
        <a href="<?= SITE_URL ?>/category.php?slug=sports" class="section-more">সব খেলা <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="sports-grid">
        <div class="sports-side">
            <?php foreach ($sports_left as $sl): ?>
            <a href="<?= SITE_URL ?>/single.php?slug=<?= $sl['slug'] ?>" class="sports-side-item">
                <div class="sports-side-img"><img src="<?= htmlspecialchars($sl['image']) ?>" alt="<?= htmlspecialchars($sl['title']) ?>" loading="lazy"></div>
                <div class="sports-side-title"><?= htmlspecialchars($sl['title']) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php $sm = $sports_main[0]; ?>
        <a href="<?= SITE_URL ?>/single.php?slug=<?= $sm['slug'] ?>" class="sports-main">
            <div class="sports-main-img"><img src="<?= htmlspecialchars($sm['image']) ?>" alt="<?= htmlspecialchars($sm['title']) ?>" loading="lazy"></div>
            <div class="sports-main-body">
                <div class="sports-main-title"><?= htmlspecialchars($sm['title']) ?></div>
                <?php if ($sm['excerpt']): ?><p class="news-card-excerpt"><?= htmlspecialchars(mb_substr($sm['excerpt'],0,120)) ?>...</p><?php endif; ?>
                <div class="time-meta"><i class="far fa-clock"></i> <?= timeAgo($sm['created_at']) ?></div>
            </div>
        </a>
        <div class="sports-side">
            <?php foreach ($sports_right as $sr): ?>
            <a href="<?= SITE_URL ?>/single.php?slug=<?= $sr['slug'] ?>" class="sports-side-item">
                <div class="sports-side-img"><img src="<?= htmlspecialchars($sr['image']) ?>" alt="<?= htmlspecialchars($sr['title']) ?>" loading="lazy"></div>
                <div class="sports-side-title"><?= htmlspecialchars($sr['title']) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

</div><!-- /container -->

<!-- ===== VIDEO ===== -->
<?php if (!empty($videos) && isSectionActive($sections, 'videos')): ?>
<div class="home-section video-section-wrap" style="padding:20px 0;margin-bottom:0">
    <div class="container">
        <div class="section-header">
            <div class="section-title"><span class="section-icon"><i class="fas fa-play"></i></span> ভিডিও</div>
            <a href="#" class="section-more">সব ভিডিও <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="video-grid">
            <?php foreach ($videos as $v): ?>
            <a href="<?= htmlspecialchars($v['youtube_url']) ?>" target="_blank" class="video-card">
                <div class="video-card-thumb">
                    <img src="<?= htmlspecialchars($v['thumbnail']) ?>" alt="<?= htmlspecialchars($v['title']) ?>" loading="lazy">
                    <div class="video-play-btn"><i class="fas fa-play"></i></div>
                </div>
                <div class="video-card-title"><?= htmlspecialchars($v['title']) ?></div>
                <div class="video-card-meta"><i class="far fa-clock"></i> <?= timeAgo($v['created_at']) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== PHOTO GALLERY ===== -->
<?php if (!empty($gallery) && isSectionActive($sections, 'gallery')): ?>
<div class="gallery-section">
    <div class="container">
        <div class="section-header">
            <div class="section-title"><span class="section-icon"><i class="fas fa-camera"></i></span> ফটোগ্যালারি</div>
            <a href="#" class="section-more">সব ছবি <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="gallery-grid">
            <a href="<?= htmlspecialchars($gallery[0]['image_url']) ?>" class="gallery-main" target="_blank">
                <img src="<?= htmlspecialchars($gallery[0]['image_url']) ?>" alt="<?= htmlspecialchars($gallery[0]['title']) ?>" loading="lazy">
                <div class="gallery-main-caption"><?= htmlspecialchars($gallery[0]['caption'] ?: $gallery[0]['title']) ?></div>
            </a>
            <div class="gallery-thumb-grid">
                <?php foreach (array_slice($gallery,1,4) as $g): ?>
                <a href="<?= htmlspecialchars($g['image_url']) ?>" class="gallery-thumb" target="_blank">
                    <img src="<?= htmlspecialchars($g['image_url']) ?>" alt="<?= htmlspecialchars($g['title']) ?>" loading="lazy">
                    <div class="gallery-thumb-caption"><?= htmlspecialchars(mb_substr($g['title'],0,40)) ?></div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function switchTab(el,tab){
    document.querySelectorAll('.most-read-tab').forEach(t=>t.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('tab-popular').style.display=tab==='popular'?'block':'none';
    document.getElementById('tab-latest').style.display=tab==='latest'?'block':'none';
}

// ===== CASCADING LOCATION SELECTS =====
var bdData = {
    'ঢাকা': {
        'ঢাকা':['ধামরাই','দোহার','কেরানীগঞ্জ','নবাবগঞ্জ','সাভার'],
        'ফরিদপুর':['আলফাডাঙ্গা','বোয়ালমারী','চরভদ্রাসন','ফরিদপুর সদর','মধুখালী','নগরকান্দা','সালথা','ভাঙ্গা'],
        'গাজীপুর':['কালিয়াকৈর','কালীগঞ্জ','কাপাসিয়া','গাজীপুর সদর','শ্রীপুর'],
        'গোপালগঞ্জ':['গোপালগঞ্জ সদর','কাশিয়ানী','কোটালীপাড়া','মুকসুদপুর','টুঙ্গিপাড়া'],
        'কিশোরগঞ্জ':['অষ্টগ্রাম','বাজিতপুর','ভৈরব','হোসেনপুর','ইটনা','কটিয়াদি','করিমগঞ্জ','কিশোরগঞ্জ সদর','কুলিয়ারচর','মিঠামইন','নিকলী','পাকুন্দিয়া','তাড়াইল'],
        'মাদারীপুর':['কালকিনি','মাদারীপুর সদর','রাজৈর','শিবচর'],
        'মানিকগঞ্জ':['দৌলতপুর','ঘিওর','হরিরামপুর','মানিকগঞ্জ সদর','সাটুরিয়া','শিবালয়','সিংগাইর'],
        'মুন্সিগঞ্জ':['গজারিয়া','লৌহজং','মুন্সিগঞ্জ সদর','শ্রীনগর','সিরাজদিখান','টংগীবাড়ী'],
        'নারায়ণগঞ্জ':['আড়াইহাজার','বন্দর','নারায়ণগঞ্জ সদর','রূপগঞ্জ','সোনারগাঁও'],
        'নরসিংদী':['বেলাব','মনোহরদী','নরসিংদী সদর','পলাশ','রায়পুরা','শিবপুর'],
        'রাজবাড়ী':['বালিয়াকান্দি','গোয়ালন্দ','কালুখালী','পাংশা','রাজবাড়ী সদর'],
        'শরীয়তপুর':['ভেদরগঞ্জ','ডামুড্যা','গোসাইরহাট','জাজিরা','নড়িয়া','শরীয়তপুর সদর'],
        'টাঙ্গাইল':['বাসাইল','ভূঞাপুর','দেলদুয়ার','ধনবাড়ী','ঘাটাইল','গোপালপুর','কালিহাতী','মধুপুর','মির্জাপুর','নাগরপুর','সখিপুর','টাঙ্গাইল সদর']
    },
    'চট্টগ্রাম': {
        'বান্দরবান':['আলীকদম','বান্দরবান সদর','লামা','নাইক্ষ্যংছড়ি','রোয়াংছড়ি','রুমা','থানচি'],
        'ব্রাহ্মণবাড়িয়া':['আখাউড়া','বাঞ্ছারামপুর','বিজয়নগর','ব্রাহ্মণবাড়িয়া সদর','কসবা','নাসিরনগর','নবীনগর','সরাইল'],
        'চাঁদপুর':['চাঁদপুর সদর','ফরিদগঞ্জ','হাইমচর','হাজীগঞ্জ','কচুয়া','মতলব উত্তর','মতলব দক্ষিণ','শাহরাস্তি'],
        'চট্টগ্রাম':['আনোয়ারা','বাঁশখালী','বোয়ালখালী','চন্দনাইশ','ফটিকছড়ি','হাটহাজারী','কর্ণফুলী','লোহাগাড়া','মিরসরাই','পটিয়া','রাঙ্গুনিয়া','রাউজান','সন্দ্বীপ','সাতকানিয়া','সীতাকুণ্ড'],
        'কুমিল্লা':['বরুড়া','ব্রাহ্মণপাড়া','বুড়িচং','চান্দিনা','চৌদ্দগ্রাম','দাউদকান্দি','দেবিদ্বার','হোমনা','কুমিল্লা সদর','লাকসাম','লালমাই','মেঘনা','মনোহরগঞ্জ','মুরাদনগর','নাঙ্গলকোট','তিতাস'],
        'কক্সবাজার':['চকরিয়া','কক্সবাজার সদর','কুতুবদিয়া','মহেশখালী','পেকুয়া','রামু','টেকনাফ','উখিয়া'],
        'ফেনী':['ছাগলনাইয়া','দাগনভূঞা','ফেনী সদর','ফুলগাজী','পরশুরাম','সোনাগাজী'],
        'খাগড়াছড়ি':['দিঘীনালা','গুইমারা','খাগড়াছড়ি সদর','লক্ষ্মীছড়ি','মাটিরাঙ্গা','মানিকছড়ি','মহালছড়ি','পানছড়ি','রামগড়'],
        'লক্ষ্মীপুর':['কমলনগর','লক্ষ্মীপুর সদর','রামগঞ্জ','রামগতি','রায়পুর'],
        'নোয়াখালী':['বেগমগঞ্জ','চাটখিল','কবিরহাট','কোম্পানীগঞ্জ','হাতিয়া','নোয়াখালী সদর','সেনবাগ','সোনাইমুড়ী','সুবর্ণচর'],
        'রাঙ্গামাটি':['বাঘাইছড়ি','বরকল','বিলাইছড়ি','কাউখালী','কাপ্তাই','জুরাইছড়ি','লংগদু','নানিয়ারচর','রাজস্থলী','রাঙ্গামাটি সদর']
    },
    'রাজশাহী': {
        'বগুড়া':['আদমদীঘি','বগুড়া সদর','ধুনট','দুপচাঁচিয়া','গাবতলী','কাহালু','নন্দীগ্রাম','শাজাহানপুর','শেরপুর','শিবগঞ্জ','সারিয়াকান্দি','সোনাতলা'],
        'চাঁপাইনবাবগঞ্জ':['ভোলাহাট','গোমস্তাপুর','নাচোল','চাঁপাইনবাবগঞ্জ সদর','শিবগঞ্জ'],
        'জয়পুরহাট':['আক্কেলপুর','কালাই','ক্ষেতলাল','জয়পুরহাট সদর','পাঁচবিবি'],
        'নওগাঁ':['আত্রাই','বদলগাছি','ধামইরহাট','মান্দা','মহাদেবপুর','নওগাঁ সদর','নিয়ামতপুর','পত্নীতলা','পোরশা','রাণীনগর','সাপাহার'],
        'নাটোর':['বাগাতিপাড়া','বড়াইগ্রাম','গুরুদাসপুর','লালপুর','নাটোর সদর','সিংড়া'],
        'পাবনা':['আটঘরিয়া','বেড়া','ভাঙ্গুড়া','চাটমোহর','ঈশ্বরদী','ফরিদপুর','পাবনা সদর','সাঁথিয়া','সুজানগর'],
        'রাজশাহী':['বাঘ','বাগমারা','চারঘাট','দুর্গাপুর','গোদাগাড়ী','মোহনপুর','পবা','পুঠিয়া','তানোর'],
        'সিরাজগঞ্জ':['বেলকুচি','চৌহালি','কামারখন্দ','কাজীপুর','রায়গঞ্জ','শাহজাদপুর','সিরাজগঞ্জ সদর','তাড়াশ','উল্লাপাড়া']
    },
    'খুলনা': {
        'বাগেরহাট':['বাগেরহাট সদর','চিতলমারী','ফকিরহাট','কচুয়া','মোংলা','মোরেলগঞ্জ','মোল্লাহাট','রামপাল','শরণখোলা'],
        'চুয়াডাঙ্গা':['আলমডাঙ্গা','চুয়াডাঙ্গা সদর','দামুড়হুদা','জীবননগর'],
        'যশোর':['অভয়নগর','বাঘারপাড়া','চৌগাছা','ঝিকরগাছা','কেশবপুর','মণিরামপুর','শার্শা','যশোর সদর'],
        'ঝিনাইদহ':['হরিণাকুণ্ডু','ঝিনাইদহ সদর','কালীগঞ্জ','কোটচাঁদপুর','মহেশপুর','শৈলকুপা'],
        'খুলনা':['বটিয়াঘাটা','দাকোপ','দিঘলিয়া','ডুমুরিয়া','ফুলতলা','কয়রা','পাইকগাছা','রূপসা','তেরখাদা'],
        'কুষ্টিয়া':['ভেড়ামারা','দৌলতপুর','খোকসা','কুমারখালী','কুষ্টিয়া সদর','মিরপুর'],
        'মাগুরা':['মাগুরা সদর','মোহাম্মদপুর','শালিখা','শ্রীপুর'],
        'মেহেরপুর':['গাংনী','মেহেরপুর সদর','মুজিবনগর'],
        'নড়াইল':['কালিয়া','লোহাগড়া','নড়াইল সদর'],
        'সাতক্ষীরা':['আশাশুনি','দেবহাটা','কালীগঞ্জ','কলারোয়া','সাতক্ষীরা সদর','শ্যামনগর','তালা']
    },
    'বরিশাল': {
        'বরগুনা':['আমতলী','বামনা','বরগুনা সদর','বেতাগী','পাথরঘাটা','তালতলী'],
        'বরিশাল':['আগৈলঝাড়া','বাকেরগঞ্জ','বানারীপাড়া','বরিশাল সদর','বাবুগঞ্জ','গৌরনদী','হিজলা','মেহেন্দিগঞ্জ','মুলাদী','উজিরপুর'],
        'ভোলা':['বোরহানউদ্দিন','চরফ্যাশন','দৌলতখান','ভোলা সদর','লালমোহন','মনপুরা','তজুমদ্দিন'],
        'ঝালকাঠি':['ঝালকাঠি সদর','কাঁঠালিয়া','নলছিটি','রাজাপুর'],
        'পটুয়াখালী':['বাউফল','দশমিনা','গলাচিপা','কলাপাড়া','মির্জাগঞ্জ','পটুয়াখালী সদর','রাঙ্গাবালী'],
        'পিরোজপুর':['ভাণ্ডারিয়া','ইন্দুরকানি','কাউখালী','মঠবাড়িয়া','নাজিরপুর','নেছারাবাদ','পিরোজপুর সদর']
    },
    'সিলেট': {
        'হবিগঞ্জ':['আজমিরীগঞ্জ','বাহুবল','বানিয়াচং','চুনারুঘাট','হবিগঞ্জ সদর','লাখাই','মাধবপুর','নবীগঞ্জ'],
        'মৌলভীবাজার':['বড়লেখা','জুড়ী','কমলগঞ্জ','কুলাউড়া','মৌলভীবাজার সদর','রাজনগর','শ্রীমঙ্গল'],
        'সুনামগঞ্জ':['বিশ্বম্ভরপুর','ছাতক','দিরাই','দোয়ারাবাজার','ধর্মপাশা','জামালগঞ্জ','জগন্নাথপুর','শাল্লা','সুনামগঞ্জ সদর','তাহিরপুর'],
        'সিলেট':['বালাগঞ্জ','বিয়ানীবাজার','বিশ্বনাথ','কোম্পানীগঞ্জ','দক্ষিণ সুরমা','ফেঞ্চুগঞ্জ','গোলাপগঞ্জ','গোয়াইনঘাট','জকিগঞ্জ','কানাইঘাট','ওসমানীনগর','সিলেট সদর','জৈন্তাপুর']
    },
    'রংপুর': {
        'দিনাজপুর':['বিরামপুর','বিরল','বোচাগঞ্জ','চিরিরবন্দর','দিনাজপুর সদর','ফুলবাড়ী','ঘোড়াঘাট','হাকিমপুর','খানসামা','নবাবগঞ্জ','পার্বতীপুর'],
        'গাইবান্ধা':['ফুলছড়ি','গাইবান্ধা সদর','গোবিন্দগঞ্জ','পলাশবাড়ী','সাদুল্লাপুর','সাঘাটা','সুন্দরগঞ্জ'],
        'কুড়িগ্রাম':['ভুরুঙ্গামারী','চর রাজিবপুর','চিলমারী','ফুলবাড়ী','কুড়িগ্রাম সদর','নাগেশ্বরী','রাজারহাট','রৌমারী','উলিপুর'],
        'লালমনিরহাট':['আদিতমারী','হাতীবান্ধা','কালীগঞ্জ','লালমনিরহাট সদর','পাটগ্রাম'],
        'নীলফামারী':['ডিমলা','ডোমার','জলঢাকা','কিশোরগঞ্জ','নীলফামারী সদর','সৈয়দপুর'],
        'পঞ্চগড়':['আটোয়ারী','বোদা','দেবীগঞ্জ','পঞ্চগড় সদর','তেতুলিয়া'],
        'রংপুর':['বদরগঞ্জ','গঙ্গাচড়া','কাউনিয়া','মিঠাপুকুর','পীরগাছা','পীরগঞ্জ','রংপুর সদর','তারাগঞ্জ'],
        'ঠাকুরগাঁও':['বালিয়াডাঙ্গী','হরিপুর','পীরগঞ্জ','রাণীশংকৈল','ঠাকুরগাঁও সদর']
    },
    'ময়মনসিংহ': {
        'জামালপুর':['বকশীগঞ্জ','দেওয়ানগঞ্জ','ইসলামপুর','জামালপুর সদর','মাদারগঞ্জ','মেলান্দহ','সরিষাবাড়ী'],
        'ময়মনসিংহ':['ভালুকা','ধোবাউড়া','ফুলবাড়িয়া','ফুলপুর','গফরগাঁও','গৌরীপুর','হালুয়াঘাট','ঈশ্বরগঞ্জ','ময়মনসিংহ সদর','মুক্তাগাছা','নান্দাইল','তারাকান্দা','ত্রিশাল'],
        'নেত্রকোণা':['আটপাড়া','বারহাট্টা','দুর্গাপুর','খালিয়াজুরী','কলমাকান্দা','কেন্দুয়া','মদন','মোহনগঞ্জ','নেত্রকোণা সদর','পূর্বধলা'],
        'শেরপুর':['ঝিনাইগাতী','নকলা','নালিতাবাড়ী','শেরপুর সদর','শ্রীবরদী']
    }
};

function loadDistricts(div) {
    var dSel = document.getElementById('sel-district');
    var uSel = document.getElementById('sel-upazila');
    dSel.innerHTML = '<option value="">-- জেলা নির্বাচন --</option>';
    uSel.innerHTML = '<option value="">-- উপজেলা নির্বাচন --</option>';
    if (!div || !bdData[div]) return;
    Object.keys(bdData[div]).forEach(function(d) {
        var o = document.createElement('option'); o.value = d; o.textContent = d; dSel.appendChild(o);
    });
}

function loadUpazilas(district) {
    var div  = document.getElementById('sel-division').value;
    var uSel = document.getElementById('sel-upazila');
    uSel.innerHTML = '<option value="">-- উপজেলা নির্বাচন --</option>';
    if (!div || !district || !bdData[div] || !bdData[div][district]) return;
    bdData[div][district].forEach(function(u) {
        var o = document.createElement('option'); o.value = u; o.textContent = u; uSel.appendChild(o);
    });
}

function localSearch() {
    var dist = document.getElementById('sel-district').value;
    var upaz = document.getElementById('sel-upazila').value;
    var q    = upaz || dist;
    if (q) window.location.href = '<?= SITE_URL ?>/search.php?q=' + encodeURIComponent(q);
}
</script>

<?php include 'includes/footer.php'; ?>

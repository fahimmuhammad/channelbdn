<?php
require_once 'includes/functions.php';
require_once 'reader_auth.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (empty($slug)) { header('Location: ' . SITE_URL); exit; }

$post = getPostBySlug($slug);
if (!$post) { header('HTTP/1.0 404 Not Found'); include '404.php'; exit; }

incrementViews($post['id']);
readerLogHistory($post['id']);

$related = getRelatedPosts($post['category_id'], $post['id'], 6);
$popular = getPopularPosts(6);
$latest_sidebar = getPosts(5);
$categories = getCategories();

$isBookmarked  = readerIsBookmarked($post['id']);
$myReaction    = readerGetReaction($post['id']);
$reactionCounts= getReactionCounts($post['id']);
$comments      = getApprovedComments($post['id']);

$page_title       = $post['title'];
$meta_desc        = $post['excerpt'] ?: mb_substr(strip_tags($post['content']), 0, 160);
$og_type          = 'article';
$og_image         = $post['image'] ?: SITE_URL . '/assets/img/og-default.jpg';
$canonical_url    = SITE_URL . '/single.php?slug=' . urlencode($post['slug']);
$amp_url          = SITE_URL . '/amp.php?slug=' . urlencode($post['slug']);
$article_author   = $post['author'];
$article_published= date('c', strtotime($post['created_at']));
$article_modified = date('c', strtotime($post['updated_at'] ?? $post['created_at']));
$article_section  = $post['category_name'];
$active_category  = $post['category_slug'];

include 'includes/header.php';

// JSON-LD: NewsArticle
$jsonld = [
    '@context'         => 'https://schema.org',
    '@type'            => 'NewsArticle',
    'headline'         => $post['title'],
    'description'      => $meta_desc,
    'datePublished'    => $article_published,
    'dateModified'     => $article_modified,
    'author'           => ['@type'=>'Person','name'=>$post['author']],
    'publisher'        => [
        '@type' => 'Organization',
        'name'  => getSetting('site_name') ?: SITE_NAME,
        'logo'  => ['@type'=>'ImageObject','url'=> SITE_URL . '/assets/img/logo.png'],
    ],
    'image'            => $og_image,
    'mainEntityOfPage' => ['@type'=>'WebPage','@id'=> $canonical_url],
    'articleSection'   => $post['category_name'],
    'inLanguage'       => 'bn',
];
echo '<script type="application/ld+json">' . json_encode($jsonld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';

// JSON-LD: BreadcrumbList
$breadcrumb = [
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => [
        ['@type'=>'ListItem','position'=>1,'name'=>'হোম','item'=> SITE_URL . '/'],
        ['@type'=>'ListItem','position'=>2,'name'=>$post['category_name'],'item'=> SITE_URL . '/category.php?slug=' . $post['category_slug']],
        ['@type'=>'ListItem','position'=>3,'name'=>$post['title'],'item'=> $canonical_url],
    ],
];
echo '<script type="application/ld+json">' . json_encode($breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
?>

<div class="main-content">
<div class="container">
  <div class="single-layout">
    <main>
      <article class="single-post">
        <a href="<?= SITE_URL ?>/category.php?slug=<?= $post['category_slug'] ?>" class="post-category-badge"><?= $post['category_name'] ?></a>
        <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>
        <div class="post-meta">
          <span><i class="fas fa-user"></i> <?= htmlspecialchars($post['author']) ?></span>
          <span><i class="far fa-calendar-alt"></i> <?= formatDate($post['created_at']) ?></span>
          <span><i class="far fa-clock"></i> <span id="readingTime"></span></span>
          <span><i class="far fa-eye"></i> <?= number_format($post['views']) ?> বার পড়া হয়েছে</span>
          <button class="bm-btn <?= $isBookmarked ? 'active' : '' ?>" id="bmBtn" data-post="<?= $post['id'] ?>" title="বুকমার্ক">
            <i class="<?= $isBookmarked ? 'fas' : 'far' ?> fa-bookmark"></i>
            <span id="bmLabel"><?= $isBookmarked ? 'সংরক্ষিত' : 'সংরক্ষণ' ?></span>
          </button>
        </div>
        <?php if ($post['image']): ?>
        <img src="<?= $post['image'] ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="post-featured-img">
        <?php endif; ?>
        <div class="post-content"><?= $post['content'] ?></div>

        <!-- Share Buttons -->
        <div class="post-share">
          <span>শেয়ার করুন:</span>
          <a href="#" class="share-btn fb" data-share="fb"><i class="fab fa-facebook-f"></i> ফেসবুক</a>
          <a href="#" class="share-btn tw" data-share="tw"><i class="fab fa-x-twitter"></i> টুইটার</a>
          <a href="#" class="share-btn wa" data-share="wa"><i class="fab fa-whatsapp"></i> হোয়াটসঅ্যাপ</a>
          <a href="#" class="share-btn ln" data-share="ln"><i class="fab fa-linkedin-in"></i> লিংকডইন</a>
          <button class="print-btn" onclick="window.print()"><i class="fas fa-print"></i> প্রিন্ট</button>
          <a href="<?= htmlspecialchars($amp_url) ?>" class="print-btn" style="background:#e65100" target="_blank"><i class="fas fa-bolt"></i> AMP</a>
        </div>

        <!-- Tags -->
        <div style="margin:10px 0">
          <span style="font-weight:600;font-size:13px;color:#555;">বিভাগ:</span>
          <a href="<?= SITE_URL ?>/category.php?slug=<?= $post['category_slug'] ?>" class="tag" style="margin-left:6px"><?= $post['category_name'] ?></a>
        </div>

        <!-- Reaction Bar -->
        <?php
        $reactions = [
            'like'  => ['emoji' => '👍', 'label' => 'পছন্দ'],
            'love'  => ['emoji' => '❤️', 'label' => 'ভালোবাসা'],
            'wow'   => ['emoji' => '😮', 'label' => 'বিস্মিত'],
            'sad'   => ['emoji' => '😢', 'label' => 'দুঃখিত'],
            'angry' => ['emoji' => '😡', 'label' => 'রাগান্বিত'],
        ];
        ?>
        <div class="reaction-bar" id="reactionBar" data-post="<?= $post['id'] ?>">
          <div class="reaction-title">আপনার প্রতিক্রিয়া জানান</div>
          <div class="reaction-buttons">
            <?php foreach ($reactions as $key => $info): ?>
            <button class="react-btn <?= $myReaction === $key ? 'active' : '' ?>" data-reaction="<?= $key ?>">
              <span class="react-emoji"><?= $info['emoji'] ?></span>
              <span class="react-label"><?= $info['label'] ?></span>
              <span class="react-count" id="rc-<?= $key ?>"><?= $reactionCounts[$key] ?? 0 ?></span>
            </button>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Comment Section -->
        <div class="comment-section" id="commentSection">
          <h3 class="comment-section-title"><i class="far fa-comments"></i> মন্তব্য
            <span class="comment-count-badge"><?= count($comments) ?></span>
          </h3>

          <!-- Comment Form -->
          <?php if (readerIsLoggedIn()): ?>
          <div class="comment-form-card">
            <div class="comment-form-card-header">
              <div class="comment-form-avatar"><?= mb_substr($_SESSION['reader_name'], 0, 1) ?></div>
              <div class="comment-form-name">
                <?= htmlspecialchars(explode(' ', $_SESSION['reader_name'])[0]) ?>
                <span>মন্তব্য করুন</span>
              </div>
            </div>
            <form id="commentForm" data-post="<?= $post['id'] ?>">
              <textarea class="comment-input" name="comment" placeholder="আপনার মতামত শেয়ার করুন..." rows="3" maxlength="1000" required></textarea>
              <div class="comment-form-footer">
                <span class="comment-char-count"><span id="charCount">0</span> / ১০০০</span>
                <button type="submit" class="comment-submit-btn"><i class="fas fa-paper-plane"></i> প্রকাশ করুন</button>
              </div>
              <div class="comment-form-msg" id="commentMsg"></div>
            </form>
          </div>
          <?php else: ?>
          <div class="comment-login-prompt">
            <i class="fas fa-comment-dots"></i>
            <div>মন্তব্য করতে <a href="<?= SITE_URL ?>/reader_login.php?redirect=<?= urlencode(SITE_URL . '/single.php?slug=' . $post['slug']) ?>">লগইন করুন</a> অথবা <a href="<?= SITE_URL ?>/reader_login.php?tab=register&redirect=<?= urlencode(SITE_URL . '/single.php?slug=' . $post['slug']) ?>">বিনামূল্যে নিবন্ধন করুন</a>।</div>
          </div>
          <?php endif; ?>

          <!-- Approved Comments -->
          <?php if (!empty($comments)): ?>
          <div class="comments-list" id="commentsList">
            <?php foreach ($comments as $c): ?>
            <div class="comment-item">
              <div class="comment-avatar"><?= mb_substr($c['name'], 0, 1) ?></div>
              <div class="comment-body">
                <div class="comment-header">
                  <span class="comment-author"><?= htmlspecialchars($c['name']) ?></span>
                  <span class="comment-time"><i class="far fa-clock"></i> <?= timeAgo($c['created_at']) ?></span>
                </div>
                <div class="comment-text"><?= nl2br(htmlspecialchars($c['comment'])) ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <div class="no-comments">
            <i class="far fa-comments"></i>
            <p>এখনো কোনো মন্তব্য নেই। প্রথম মন্তব্যটি করুন!</p>
          </div>
          <?php endif; ?>
        </div>
      </article>

      <!-- Related Posts Carousel -->
      <?php if (!empty($related)): ?>
      <div class="category-section" style="margin-top:20px">
        <div class="related-section">
          <h3 class="related-title">সম্পর্কিত সংবাদ</h3>
          <div class="carousel-wrap">
            <button class="carousel-btn prev"><i class="fas fa-chevron-left"></i></button>
            <div class="carousel-track">
              <?php foreach ($related as $r): ?>
              <div class="news-card">
                <div class="card-img"><a href="<?= SITE_URL ?>/single.php?slug=<?= $r['slug'] ?>"><img src="<?= $r['image'] ?>" alt="<?= htmlspecialchars($r['title']) ?>" loading="lazy"></a></div>
                <div class="card-body">
                  <a href="<?= SITE_URL ?>/category.php?slug=<?= $r['category_slug'] ?>" class="card-category"><?= $r['category_name'] ?></a>
                  <div class="card-title"><a href="<?= SITE_URL ?>/single.php?slug=<?= $r['slug'] ?>"><?= htmlspecialchars($r['title']) ?></a></div>
                  <div class="card-meta"><span><i class="far fa-clock"></i> <?= timeAgo($r['created_at']) ?></span></div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <button class="carousel-btn next"><i class="fas fa-chevron-right"></i></button>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </main>

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-widget">
        <div class="widget-header"><i class="fas fa-fire"></i> জনপ্রিয় সংবাদ</div>
        <div class="widget-body">
          <div class="popular-list">
            <?php foreach ($popular as $i => $p): ?>
            <div class="popular-item">
              <span class="popular-num"><?= $i + 1 ?></span>
              <div>
                <div class="popular-title"><a href="<?= SITE_URL ?>/single.php?slug=<?= $p['slug'] ?>"><?= htmlspecialchars($p['title']) ?></a></div>
                <div class="popular-meta"><i class="far fa-eye"></i> <?= number_format($p['views']) ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="sidebar-widget">
        <div class="widget-ad"><a href="#"><img src="https://picsum.photos/seed/sidad3/280/250" alt="বিজ্ঞাপন"></a><p>বিজ্ঞাপন</p></div>
      </div>

      <div class="sidebar-widget">
        <div class="widget-header"><i class="fas fa-newspaper"></i> সর্বশেষ সংবাদ</div>
        <div class="widget-body">
          <?php foreach ($latest_sidebar as $p): ?>
          <div class="sidebar-news-item">
            <a href="<?= SITE_URL ?>/single.php?slug=<?= $p['slug'] ?>"><img src="<?= $p['image'] ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy"></a>
            <div class="sidebar-news-body">
              <a href="<?= SITE_URL ?>/category.php?slug=<?= $p['category_slug'] ?>" class="sn-category"><?= $p['category_name'] ?></a>
              <div class="sn-title"><a href="<?= SITE_URL ?>/single.php?slug=<?= $p['slug'] ?>"><?= htmlspecialchars($p['title']) ?></a></div>
              <div class="sn-meta"><i class="far fa-clock"></i> <?= timeAgo($p['created_at']) ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="sidebar-widget">
        <div class="widget-header"><i class="fas fa-tags"></i> বিভাগসমূহ</div>
        <div class="tag-cloud">
          <?php foreach ($categories as $cat): ?>
          <a href="<?= SITE_URL ?>/category.php?slug=<?= $cat['slug'] ?>" class="tag"><?= $cat['name'] ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    </aside>
  </div>
</div>
</div>

<script>
// Bookmark
var bmBtn = document.getElementById('bmBtn');
if (bmBtn) {
    bmBtn.addEventListener('click', function() {
        var pid = this.dataset.post;
        var fd  = new FormData();
        fd.append('post_id', pid);
        fetch('<?= SITE_URL ?>/reader_bookmark.php', { method:'POST', body:fd })
            .then(function(r){ return r.json(); })
            .then(function(d) {
                if (d.error === 'login_required') {
                    window.location.href = '<?= SITE_URL ?>/reader_login.php?redirect=<?= urlencode(SITE_URL . '/single.php?slug=' . $post['slug']) ?>';
                    return;
                }
                bmBtn.classList.toggle('active', d.bookmarked);
                bmBtn.querySelector('i').className = (d.bookmarked ? 'fas' : 'far') + ' fa-bookmark';
                document.getElementById('bmLabel').textContent = d.bookmarked ? 'সংরক্ষিত' : 'সংরক্ষণ';
            });
    });
}

// Reactions
var reactionBar = document.getElementById('reactionBar');
if (reactionBar) {
    reactionBar.querySelectorAll('.react-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var reaction = this.dataset.reaction;
            var pid      = reactionBar.dataset.post;
            var fd       = new FormData();
            fd.append('post_id', pid);
            fd.append('reaction', reaction);
            fetch('<?= SITE_URL ?>/reader_react.php', { method:'POST', body:fd })
                .then(function(r){ return r.json(); })
                .then(function(d) {
                    if (d.error === 'login_required') {
                        window.location.href = '<?= SITE_URL ?>/reader_login.php?redirect=<?= urlencode(SITE_URL . '/single.php?slug=' . $post['slug']) ?>';
                        return;
                    }
                    reactionBar.querySelectorAll('.react-btn').forEach(function(b) {
                        b.classList.toggle('active', b.dataset.reaction === d.my_reaction);
                    });
                    var counts = d.counts || {};
                    ['like','love','wow','sad','angry'].forEach(function(k) {
                        var el = document.getElementById('rc-' + k);
                        if (el) el.textContent = counts[k] || 0;
                    });
                });
        });
    });
}

// Comment form
var cForm = document.getElementById('commentForm');
if (cForm) {
    var textarea  = cForm.querySelector('textarea');
    var charCount = document.getElementById('charCount');
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
    cForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = cForm.querySelector('.comment-submit-btn');
        var msg = document.getElementById('commentMsg');
        btn.disabled = true;
        var fd = new FormData(cForm);
        fd.append('post_id', cForm.dataset.post);
        fetch('<?= SITE_URL ?>/reader_comment.php', { method:'POST', body:fd })
            .then(function(r){ return r.json(); })
            .then(function(d) {
                btn.disabled = false;
                if (d.success) {
                    msg.className = 'comment-form-msg success';
                    msg.textContent = d.message;
                    textarea.value = '';
                    charCount.textContent = '0';
                } else {
                    msg.className = 'comment-form-msg error';
                    var errMap = {
                        too_short: 'মন্তব্য খুব ছোট।',
                        too_long:  'মন্তব্য ১০০০ অক্ষরের মধ্যে রাখুন।',
                        db_error:  'সংরক্ষণ ব্যর্থ হয়েছে।'
                    };
                    msg.textContent = errMap[d.error] || 'একটি সমস্যা হয়েছে।';
                }
            });
    });
}
</script>

<?php include 'includes/footer.php'; ?>

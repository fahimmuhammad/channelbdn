<?php
require_once 'includes/functions.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (empty($slug)) { header('Location: ' . SITE_URL); exit; }

$category = getCategoryBySlug($slug);
if (!$category) { header('HTTP/1.0 404 Not Found'); include '404.php'; exit; }

$per_page = 12;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $per_page;
$posts = getPostsByCategory($slug, $per_page, $offset);
$total = getTotalPostsByCategory($slug);
$total_pages = ceil($total / $per_page);

$popular = getPopularPosts(5);
$latest_sidebar = getPosts(5);
$categories = getCategories();
$active_category = $slug;
$page_title      = $category['name'] . ' বিভাগ';
$meta_desc       = $category['name'] . ' সংক্রান্ত সর্বশেষ সংবাদ — ' . (getSetting('site_name') ?: SITE_NAME);
$canonical_url   = SITE_URL . '/category.php?slug=' . urlencode($slug) . ($page > 1 ? '&page=' . $page : '');

include 'includes/header.php';

echo '<script type="application/ld+json">' . json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => [
        ['@type'=>'ListItem','position'=>1,'name'=>'হোম','item'=> SITE_URL . '/'],
        ['@type'=>'ListItem','position'=>2,'name'=>$category['name'],'item'=> SITE_URL . '/category.php?slug=' . $slug],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
?>

<div class="main-content">
<div class="container">

  <!-- Category Header -->
  <div class="category-header">
    <h1><?= htmlspecialchars($category['name']) ?></h1>
    <p>মোট <?= $total ?> টি সংবাদ পাওয়া গেছে</p>
  </div>

  <div class="content-wrapper">
    <main>
      <?php if (!empty($posts)): ?>
      <section class="category-section">
        <div class="news-grid">
          <?php foreach ($posts as $post): ?>
          <div class="news-card">
            <div class="card-img">
              <a href="<?= SITE_URL ?>/single.php?slug=<?= $post['slug'] ?>">
                <img src="<?= $post['image'] ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
              </a>
            </div>
            <div class="card-body">
              <a href="<?= SITE_URL ?>/category.php?slug=<?= $post['category_slug'] ?>" class="card-category"><?= $post['category_name'] ?></a>
              <div class="card-title"><a href="<?= SITE_URL ?>/single.php?slug=<?= $post['slug'] ?>"><?= htmlspecialchars($post['title']) ?></a></div>
              <p class="card-excerpt"><?= htmlspecialchars($post['excerpt']) ?></p>
              <div class="card-meta">
                <span><i class="far fa-clock"></i> <?= timeAgo($post['created_at']) ?></span>
                <span><i class="far fa-eye"></i> <?= number_format($post['views']) ?></span>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Load More -->
        <?php if ($total_pages > 1): ?>
        <div class="load-more-wrap">
          <button class="load-more-btn" id="loadMoreBtn"
                  data-slug="<?= htmlspecialchars($slug) ?>"
                  data-page="1"
                  data-total="<?= $total_pages ?>">
            <i class="fas fa-plus-circle"></i> আরও সংবাদ দেখুন
          </button>
        </div>
        <!-- Fallback pagination for no-JS -->
        <noscript>
          <div class="pagination">
            <?php if ($page > 1): ?><a href="?slug=<?= $slug ?>&page=<?= $page-1 ?>"><i class="fas fa-chevron-left"></i></a><?php endif; ?>
            <?php for ($i=max(1,$page-2);$i<=min($total_pages,$page+2);$i++): ?>
            <?php if($i==$page): ?><span class="current"><?= $i ?></span><?php else: ?><a href="?slug=<?= $slug ?>&page=<?= $i ?>"><?= $i ?></a><?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?><a href="?slug=<?= $slug ?>&page=<?= $page+1 ?>"><i class="fas fa-chevron-right"></i></a><?php endif; ?>
          </div>
        </noscript>
        <?php endif; ?>
      </section>
      <?php else: ?>
      <div class="no-results">
        <i class="far fa-newspaper"></i>
        <h3>কোনো সংবাদ পাওয়া যায়নি</h3>
        <p>এই বিভাগে এখনো কোনো সংবাদ প্রকাশিত হয়নি।</p>
      </div>
      <?php endif; ?>
    </main>

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
        <div class="widget-ad"><a href="#"><img src="https://picsum.photos/seed/sidad4/280/250" alt="বিজ্ঞাপন"></a><p>বিজ্ঞাপন</p></div>
      </div>

      <div class="sidebar-widget">
        <div class="widget-header"><i class="fas fa-list"></i> সকল বিভাগ</div>
        <div class="tag-cloud">
          <?php foreach ($categories as $cat): ?>
          <a href="<?= SITE_URL ?>/category.php?slug=<?= $cat['slug'] ?>" class="tag <?= $cat['slug'] == $slug ? 'active' : '' ?>"><?= $cat['name'] ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    </aside>
  </div>
</div>
</div>

<style>.tag.active { background: var(--primary); color: #fff; border-color: var(--primary); }</style>
<script>
(function(){
    var btn = document.getElementById('loadMoreBtn');
    if (!btn) return;
    var grid    = document.querySelector('.news-grid');
    var siteUrl = document.body.dataset.siteurl || '';
    btn.addEventListener('click', function(){
        var slug  = btn.dataset.slug;
        var page  = parseInt(btn.dataset.page) + 1;
        var total = parseInt(btn.dataset.total);
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> লোড হচ্ছে...';
        fetch(siteUrl + '/category_ajax.php?slug=' + encodeURIComponent(slug) + '&page=' + page)
            .then(function(r){ return r.json(); })
            .then(function(posts){
                posts.forEach(function(p){
                    var card = document.createElement('div');
                    card.className = 'news-card';
                    card.innerHTML =
                        '<div class="card-img"><a href="' + siteUrl + '/single.php?slug=' + encodeURIComponent(p.slug) + '">' +
                        '<img src="' + p.image + '" alt="' + p.title.replace(/"/g,'') + '" loading="lazy"></a></div>' +
                        '<div class="card-body">' +
                        '<a href="' + siteUrl + '/category.php?slug=' + p.cat_slug + '" class="card-category">' + p.cat_name + '</a>' +
                        '<div class="card-title"><a href="' + siteUrl + '/single.php?slug=' + encodeURIComponent(p.slug) + '">' + p.title + '</a></div>' +
                        '<p class="card-excerpt">' + (p.excerpt || '') + '</p>' +
                        '<div class="card-meta"><span><i class="far fa-clock"></i> ' + p.time + '</span></div></div>';
                    grid.appendChild(card);
                });
                btn.dataset.page = page;
                if (page >= total) { btn.parentNode.remove(); }
                else { btn.disabled = false; btn.innerHTML = '<i class="fas fa-plus-circle"></i> আরও সংবাদ দেখুন'; }
            })
            .catch(function(){
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus-circle"></i> আরও সংবাদ দেখুন';
            });
    });
})();
</script>
<?php include 'includes/footer.php'; ?>

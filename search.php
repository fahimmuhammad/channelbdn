<?php
require_once 'includes/functions.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$posts = [];
$total = 0;

if (!empty($q)) {
    global $conn;
    $search = $conn->real_escape_string($q);
    $result = $conn->query("SELECT p.*, c.name as category_name, c.slug as category_slug FROM posts p JOIN categories c ON p.category_id = c.id WHERE p.status='published' AND (p.title LIKE '%$search%' OR p.content LIKE '%$search%' OR p.excerpt LIKE '%$search%') ORDER BY p.created_at DESC LIMIT 24");
    $posts = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $total = count($posts);
}

$popular = getPopularPosts(5);
$categories = getCategories();
$page_title = !empty($q) ? '"' . htmlspecialchars($q) . '" অনুসন্ধান ফলাফল' : 'অনুসন্ধান';

include 'includes/header.php';
?>

<div class="main-content">
<div class="container">
  <div class="search-header">
    <h1><?= !empty($q) ? '"' . htmlspecialchars($q) . '" এর জন্য ' . $total . ' টি ফলাফল' : 'সংবাদ অনুসন্ধান' ?></h1>
    <form class="search-form-large" action="search.php" method="GET">
      <input type="search" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="সংবাদ খুঁজুন...">
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
  </div>

  <div class="content-wrapper">
    <main>
      <?php if (!empty($posts)): ?>
      <section class="category-section">
        <div class="news-grid">
          <?php foreach ($posts as $post): ?>
          <div class="news-card">
            <div class="card-img"><a href="<?= SITE_URL ?>/single.php?slug=<?= $post['slug'] ?>"><img src="<?= $post['image'] ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy"></a></div>
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
      </section>
      <?php elseif (!empty($q)): ?>
      <div class="no-results">
        <i class="fas fa-search"></i>
        <h3>"<?= htmlspecialchars($q) ?>" এর জন্য কোনো ফলাফল পাওয়া যায়নি</h3>
        <p>অন্য কিওয়ার্ড দিয়ে আবার চেষ্টা করুন।</p>
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
        <div class="widget-header"><i class="fas fa-list"></i> বিভাগসমূহ</div>
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

<?php include 'includes/footer.php'; ?>

<?php
require_once 'auth.php';
requirePermission('posts');
require_once dirname(__DIR__) . '/includes/functions.php';

global $conn;
$total_posts    = $conn->query("SELECT COUNT(*) as c FROM posts WHERE status='published'")->fetch_assoc()['c'];
$total_drafts   = $conn->query("SELECT COUNT(*) as c FROM posts WHERE status='draft'")->fetch_assoc()['c'];
$total_sched    = $conn->query("SELECT COUNT(*) as c FROM posts WHERE status='scheduled'")->fetch_assoc()['c'] ?? 0;
$total_cats     = $conn->query("SELECT COUNT(*) as c FROM categories")->fetch_assoc()['c'];
$total_views    = $conn->query("SELECT SUM(views) as c FROM posts")->fetch_assoc()['c'] ?? 0;
$recent_posts   = getPosts(8);

// Reader stats (tables may not exist yet — suppress errors)
$total_readers    = @$conn->query("SELECT COUNT(*) as c FROM readers WHERE is_active=1")->fetch_assoc()['c'] ?? 0;
$total_comments   = @$conn->query("SELECT COUNT(*) as c FROM reader_comments WHERE status='pending'")->fetch_assoc()['c'] ?? 0;
$total_newsletter = @$conn->query("SELECT COUNT(*) as c FROM newsletter_subscribers WHERE is_active=1")->fetch_assoc()['c'] ?? 0;

// Posts per day for last 7 days
$chart_labels = []; $chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('d/m', strtotime($date));
    $r = $conn->query("SELECT COUNT(*) as c FROM posts WHERE DATE(created_at)='$date' AND status='published'");
    $chart_labels[] = $label;
    $chart_data[]   = (int)($r ? $r->fetch_assoc()['c'] : 0);
}

// Top 5 categories by post count
$cat_r = $conn->query("SELECT c.name, COUNT(p.id) as cnt FROM categories c LEFT JOIN posts p ON p.category_id=c.id AND p.status='published' GROUP BY c.id ORDER BY cnt DESC LIMIT 5");
$cat_labels = []; $cat_data = [];
while ($row = $cat_r->fetch_assoc()) { $cat_labels[] = $row['name']; $cat_data[] = (int)$row['cnt']; }

include 'includes/admin_header.php';
?>

<div class="admin-main">
  <h1 class="page-title"><i class="fas fa-tachometer-alt"></i> <?= __('dashboard') ?></h1>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card blue">
      <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
      <div class="stat-info"><span class="stat-num"><?= number_format($total_posts) ?></span><span class="stat-label"><?= __('published_posts') ?></span></div>
    </div>
    <div class="stat-card orange">
      <div class="stat-icon"><i class="fas fa-edit"></i></div>
      <div class="stat-info"><span class="stat-num"><?= number_format($total_drafts) ?></span><span class="stat-label"><?= __('drafts') ?></span></div>
    </div>
    <div class="stat-card green">
      <div class="stat-icon"><i class="fas fa-list"></i></div>
      <div class="stat-info"><span class="stat-num"><?= number_format($total_cats) ?></span><span class="stat-label"><?= __('nav_categories') ?></span></div>
    </div>
    <div class="stat-card red">
      <div class="stat-icon"><i class="fas fa-eye"></i></div>
      <div class="stat-info"><span class="stat-num"><?= number_format($total_views) ?></span><span class="stat-label"><?= __('total_views') ?></span></div>
    </div>
    <a href="<?= SITE_URL ?>/admin/readers.php" style="text-decoration:none">
    <div class="stat-card blue">
      <div class="stat-icon"><i class="fas fa-user-friends"></i></div>
      <div class="stat-info"><span class="stat-num"><?= number_format($total_readers) ?></span><span class="stat-label">সক্রিয় পাঠক</span></div>
    </div></a>
    <a href="<?= SITE_URL ?>/admin/comments.php" style="text-decoration:none">
    <div class="stat-card orange">
      <div class="stat-icon"><i class="far fa-comments"></i></div>
      <div class="stat-info"><span class="stat-num"><?= number_format($total_comments) ?></span><span class="stat-label">অপেক্ষমান মন্তব্য</span></div>
    </div></a>
    <a href="<?= SITE_URL ?>/admin/newsletter.php" style="text-decoration:none">
    <div class="stat-card green">
      <div class="stat-icon"><i class="fas fa-envelope-open-text"></i></div>
      <div class="stat-info"><span class="stat-num"><?= number_format($total_newsletter) ?></span><span class="stat-label">নিউজলেটার সদস্য</span></div>
    </div></a>
  </div>

  <!-- Charts -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
    <div class="admin-card" style="margin-bottom:0">
      <div class="admin-card-header"><h3><i class="fas fa-chart-bar"></i> গত ৭ দিনের প্রকাশনা</h3></div>
      <div class="admin-card-body"><canvas id="postsChart" height="140"></canvas></div>
    </div>
    <div class="admin-card" style="margin-bottom:0">
      <div class="admin-card-header"><h3><i class="fas fa-chart-pie"></i> বিভাগ অনুযায়ী সংবাদ</h3></div>
      <div class="admin-card-body"><canvas id="catChart" height="140"></canvas></div>
    </div>
  </div>

  <!-- Recent Posts -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h3><i class="fas fa-clock"></i> <?= __('recent_posts') ?></h3>
      <a href="add_post.php" class="btn-primary"><i class="fas fa-plus"></i> <?= __('nav_add_post') ?></a>
    </div>
    <div class="table-wrap">
      <table class="admin-table">
        <thead><tr><th><?= __('col_title') ?></th><th><?= __('col_category') ?></th><th><?= __('col_author') ?></th><th><?= __('col_views') ?></th><th><?= __('col_date') ?></th><th><?= __('col_actions') ?></th></tr></thead>
        <tbody>
          <?php foreach ($recent_posts as $p): ?>
          <tr>
            <td><a href="<?= SITE_URL ?>/single.php?slug=<?= $p['slug'] ?>" target="_blank" style="color:var(--text);font-weight:600"><?= mb_substr(htmlspecialchars($p['title']), 0, 55) ?>…</a></td>
            <td><span class="badge"><?= $p['category_name'] ?></span></td>
            <td><?= htmlspecialchars($p['author']) ?></td>
            <td><?= number_format($p['views']) ?></td>
            <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
            <td>
              <a href="edit_post.php?id=<?= $p['id'] ?>" class="btn-sm edit"><i class="fas fa-edit"></i></a>
              <a href="delete_post.php?id=<?= $p['id'] ?>" class="btn-sm delete" onclick="return confirm('<?= __('confirm_del') ?>')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
var labels  = <?= json_encode($chart_labels, JSON_UNESCAPED_UNICODE) ?>;
var data    = <?= json_encode($chart_data) ?>;
var cLabels = <?= json_encode($cat_labels, JSON_UNESCAPED_UNICODE) ?>;
var cData   = <?= json_encode($cat_data) ?>;

new Chart(document.getElementById('postsChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{ label: 'প্রকাশিত সংবাদ', data: data, backgroundColor: 'rgba(232,0,28,0.75)', borderRadius: 4 }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});

new Chart(document.getElementById('catChart'), {
    type: 'doughnut',
    data: {
        labels: cLabels,
        datasets: [{ data: cData, backgroundColor: ['#e8001c','#2196f3','#4caf50','#ff9800','#9c27b0'] }]
    },
    options: { plugins: { legend: { position: 'right' } } }
});
</script>
<?php include 'includes/admin_footer.php'; ?>

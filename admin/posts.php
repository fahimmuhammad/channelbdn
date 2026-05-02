<?php
require_once 'auth.php';
requirePermission('posts');
require_once dirname(__DIR__) . '/includes/functions.php';
global $conn;

$per_page = 15;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;
$cat_filter = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$status_filter = $_GET['status'] ?? '';
$search = isset($_GET['s']) ? $conn->real_escape_string(trim($_GET['s'])) : '';

$where = "WHERE 1=1";
if ($cat_filter) $where .= " AND p.category_id = $cat_filter";
if ($status_filter) $where .= " AND p.status = '$status_filter'";
if ($search) $where .= " AND p.title LIKE '%$search%'";

$total_result = $conn->query("SELECT COUNT(*) as c FROM posts p JOIN categories c ON p.category_id = c.id $where");
$total = $total_result->fetch_assoc()['c'];
$total_pages = ceil($total / $per_page);

$posts = $conn->query("SELECT p.*, c.name as category_name FROM posts p JOIN categories c ON p.category_id = c.id $where ORDER BY p.created_at DESC LIMIT $per_page OFFSET $offset");
$categories = getCategories();
$admin_title = __('post_list');
include 'includes/admin_header.php';
?>

<div class="admin-main">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
    <h1 class="page-title" style="margin-bottom:0"><i class="fas fa-newspaper"></i> <?= __('post_list') ?></h1>
    <a href="add_post.php" class="btn-primary"><i class="fas fa-plus"></i> <?= __('nav_add_post') ?></a>
  </div>

  <!-- Filters -->
  <div class="admin-card" style="margin-bottom:18px">
    <div class="admin-card-body" style="padding:14px 20px">
      <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
        <input type="text" name="s" value="<?= htmlspecialchars($search) ?>" placeholder="<?= __('search_title') ?>" class="form-control" style="width:220px">
        <select name="cat" class="form-control" style="width:160px">
          <option value=""><?= __('all_categories') ?></option>
          <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= $cat_filter == $cat['id'] ? 'selected' : '' ?>><?= $cat['name'] ?></option>
          <?php endforeach; ?>
        </select>
        <select name="status" class="form-control" style="width:140px">
          <option value=""><?= __('all_status') ?></option>
          <option value="published" <?= $status_filter == 'published' ? 'selected' : '' ?>><?= __('status_published') ?></option>
          <option value="draft" <?= $status_filter == 'draft' ? 'selected' : '' ?>><?= __('status_draft') ?></option>
        </select>
        <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> <?= __('btn_filter') ?></button>
        <a href="posts.php" class="btn-secondary"><?= __('btn_reset') ?></a>
      </form>
    </div>
  </div>

  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead><tr><th>#</th><th><?= __('col_image') ?></th><th><?= __('col_title') ?></th><th><?= __('col_category') ?></th><th><?= __('col_status') ?></th><th><?= __('col_views') ?></th><th><?= __('col_date') ?></th><th><?= __('col_actions') ?></th></tr></thead>
        <tbody>
          <?php if ($posts && $posts->num_rows > 0): ?>
          <?php $i = $offset + 1; while ($p = $posts->fetch_assoc()): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><img src="<?= htmlspecialchars($p['image']) ?>" style="width:64px;height:44px;object-fit:cover;border-radius:4px" alt=""></td>
            <td style="max-width:320px"><a href="<?= SITE_URL ?>/single.php?slug=<?= $p['slug'] ?>" target="_blank" style="font-weight:600;color:var(--text)"><?= htmlspecialchars(mb_substr($p['title'], 0, 60)) ?>…</a>
              <?php if ($p['is_breaking']): ?><span class="badge breaking" style="margin-left:4px"><?= __('breaking') ?></span><?php endif; ?>
              <?php if ($p['is_featured']): ?><span class="badge featured" style="margin-left:4px"><?= __('featured') ?></span><?php endif; ?>
            </td>
            <td><span class="badge"><?= $p['category_name'] ?></span></td>
            <td><?php
                if ($p['status']==='published') echo '<span class="badge published">'.  __('status_published').'</span>';
                elseif ($p['status']==='scheduled') echo '<span class="badge scheduled">'. __('status_scheduled').'</span>';
                else echo '<span class="badge draft">'.__('status_draft').'</span>';
            ?></td>
            <td><?= number_format($p['views']) ?></td>
            <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
            <td style="white-space:nowrap">
              <a href="edit_post.php?id=<?= $p['id'] ?>" class="btn-sm edit" title="<?= __('btn_edit') ?>"><i class="fas fa-edit"></i></a>
              <a href="delete_post.php?id=<?= $p['id'] ?>" class="btn-sm delete" title="<?= __('btn_delete') ?>" data-confirm="<?= __('confirm_del_post') ?>"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php else: ?><tr><td colspan="8" style="text-align:center;padding:30px;color:var(--text-muted)"><?= __('no_posts') ?></td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if ($total_pages > 1): ?>
    <div style="padding:14px 20px;display:flex;gap:6px;align-items:center;flex-wrap:wrap;border-top:1px solid var(--border)">
      <?php for ($pg = 1; $pg <= $total_pages; $pg++): ?>
      <a href="?page=<?= $pg ?>&cat=<?= $cat_filter ?>&status=<?= $status_filter ?>&s=<?= urlencode($search) ?>" class="page-link <?= $pg==$page?'active':'' ?>"><?= $pg ?></a>
      <?php endfor; ?>
      <span style="color:var(--text-muted);font-size:12px;margin-left:8px"><?= sprintf(__('total_items'), $total) ?></span>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/admin_footer.php'; ?>

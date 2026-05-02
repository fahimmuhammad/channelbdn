<?php
require_once 'auth.php';
requirePermission('categories');
require_once dirname(__DIR__) . '/config.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? ''); $slug = trim($_POST['slug'] ?? '');
        if ($name && $slug) {
            $n = $conn->real_escape_string($name); $s = $conn->real_escape_string($slug);
            $r = $conn->query("INSERT INTO categories (name,slug) VALUES ('$n','$s')");
            $success = $r ? __('cat_added') : __('error_prefix') . $conn->error;
        } else { $error = __('cat_error'); }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) { $conn->query("DELETE FROM categories WHERE id=$id"); $success = __('cat_deleted'); }
    }
}

$categories = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM posts p WHERE p.category_id=c.id) as post_count FROM categories c ORDER BY sort_order ASC");
$admin_title = __('nav_categories');
include 'includes/admin_header.php';
?>

<div class="admin-main">
  <h1 class="page-title"><i class="fas fa-list"></i> <?= __('cat_management') ?></h1>
  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

  <div class="form-grid">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-list"></i> <?= __('all_cats') ?></h3></div>
      <div class="table-wrap">
        <table class="admin-table">
          <thead><tr><th>#</th><th><?= __('col_name') ?></th><th><?= __('col_slug') ?></th><th><?= __('col_post_count') ?></th><th><?= __('col_actions') ?></th></tr></thead>
          <tbody>
            <?php $i=1; while ($cat = $categories->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($cat['name']) ?></td>
              <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
              <td><span class="badge"><?= $cat['post_count'] ?></span></td>
              <td>
                <form method="POST" style="display:inline">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                  <button type="submit" class="btn-sm delete" data-confirm="<?= __('confirm_del_cat') ?>"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div>
      <div class="admin-card">
        <div class="admin-card-header"><h3><i class="fas fa-plus"></i> <?= __('new_cat') ?></h3></div>
        <div class="admin-card-body">
          <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group"><label><?= __('cat_name') ?></label><input type="text" name="name" class="form-control" placeholder="জাতীয়" required></div>
            <div class="form-group"><label><?= __('col_slug') ?></label><input type="text" name="slug" class="form-control" placeholder="national" required></div>
            <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> <?= __('btn_add') ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/admin_footer.php'; ?>

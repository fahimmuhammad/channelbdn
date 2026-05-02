<?php
require_once 'auth.php'; requirePermission('homepage');
require_once dirname(__DIR__) . '/config.php';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("UPDATE homepage_sections SET is_active=1-is_active WHERE id=$id");
        $success = __('section_updated');
    } elseif ($action === 'reorder') {
        $orders = $_POST['order'] ?? [];
        foreach ($orders as $id => $ord) {
            $id = (int)$id; $ord = (int)$ord;
            $conn->query("UPDATE homepage_sections SET sort_order=$ord WHERE id=$id");
        }
        $success = __('order_updated');
    }
}

$sections = $conn->query("SELECT * FROM homepage_sections ORDER BY sort_order ASC");
$admin_title = __('nav_homepage'); include 'includes/admin_header.php';
?>
<div class="admin-main">
  <h1 class="page-title"><i class="fas fa-th-large"></i> <?= __('homepage_title') ?></h1>
  <p style="color:#888;font-size:13px;margin-bottom:16px"><?= __('homepage_desc') ?></p>

  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

  <div class="form-grid">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-sliders-h"></i> <?= __('section_list') ?></h3></div>
      <div class="table-wrap">
        <table class="admin-table">
          <thead><tr><th><?= __('col_order') ?></th><th><?= __('col_section') ?></th><th><?= __('col_key') ?></th><th><?= __('col_status') ?></th><th><?= __('btn_edit') ?></th></tr></thead>
          <tbody>
            <?php while ($sec = $sections->fetch_assoc()): ?>
            <tr>
              <td><?= $sec['sort_order'] ?></td>
              <td><strong><?= htmlspecialchars($sec['section_name']) ?></strong></td>
              <td><code style="font-size:12px"><?= htmlspecialchars($sec['section_key']) ?></code></td>
              <td>
                <span class="btn-status <?= $sec['is_active']?'active':'inactive' ?>">
                  <i class="fas fa-<?= $sec['is_active']?'eye':'eye-slash' ?>"></i>
                  <?= $sec['is_active'] ? __('status_showing') : __('status_hidden') ?>
                </span>
              </td>
              <td>
                <form method="POST" style="display:inline">
                  <input type="hidden" name="action" value="toggle">
                  <input type="hidden" name="id" value="<?= $sec['id'] ?>">
                  <button type="submit" class="btn-sm-text <?= $sec['is_active']?'btn-red':'btn-green' ?>">
                    <?= $sec['is_active'] ? '<i class="fas fa-eye-slash"></i> ' . __('btn_hide') : '<i class="fas fa-eye"></i> ' . __('btn_show') ?>
                  </button>
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
        <div class="admin-card-header"><h3><i class="fas fa-sort"></i> <?= __('reorder_title') ?></h3></div>
        <div class="admin-card-body">
          <?php
          $sections2 = $conn->query("SELECT * FROM homepage_sections ORDER BY sort_order ASC");
          ?>
          <form method="POST">
            <input type="hidden" name="action" value="reorder">
            <p style="font-size:13px;color:#888;margin-bottom:12px"><?= __('reorder_hint') ?></p>
            <?php while ($sec = $sections2->fetch_assoc()): ?>
            <div class="form-group" style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
              <label style="flex:1;font-size:13px;margin:0"><?= htmlspecialchars($sec['section_name']) ?></label>
              <input type="number" name="order[<?= $sec['id'] ?>]" value="<?= $sec['sort_order'] ?>" min="1" max="20" style="width:70px" class="form-control">
            </div>
            <?php endwhile; ?>
            <button type="submit" class="btn-primary" style="margin-top:8px"><i class="fas fa-save"></i> <?= __('btn_save_order') ?></button>
          </form>
        </div>
      </div>

      <div class="admin-card" style="margin-top:16px">
        <div class="admin-card-header"><h3><i class="fas fa-info-circle"></i> <?= __('quick_links') ?></h3></div>
        <div class="admin-card-body">
          <div style="display:flex;flex-direction:column;gap:8px">
            <a href="<?= SITE_URL ?>/admin/ads.php" class="btn-secondary" style="text-align:center"><i class="fas fa-ad"></i> <?= __('nav_ads') ?></a>
            <a href="<?= SITE_URL ?>/admin/polls.php" class="btn-secondary" style="text-align:center"><i class="fas fa-poll"></i> <?= __('nav_polls') ?></a>
            <a href="<?= SITE_URL ?>/admin/gallery.php" class="btn-secondary" style="text-align:center"><i class="fas fa-camera"></i> <?= __('nav_gallery') ?></a>
            <a href="<?= SITE_URL ?>/admin/videos.php" class="btn-secondary" style="text-align:center"><i class="fas fa-video"></i> <?= __('nav_videos') ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/admin_footer.php'; ?>

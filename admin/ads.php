<?php
require_once 'auth.php'; requirePermission('ads');
require_once dirname(__DIR__) . '/config.php';
$success = $error = '';

$positions_bn = [
    'header_left'   => 'হেডার বাম (৩০০×৮০)',
    'header_right'  => 'হেডার ডান (৩০০×৮০)',
    'content_strip' => 'কন্টেন্ট স্ট্রিপ (৯৭০×৯০)',
    'sidebar_top'   => 'সাইডবার উপর (৩০০×২৫০)',
    'sidebar_bottom'=> 'সাইডবার নিচ (৩০০×২৫০)',
];
$positions_en = [
    'header_left'   => 'Header Left (300×80)',
    'header_right'  => 'Header Right (300×80)',
    'content_strip' => 'Content Strip (970×90)',
    'sidebar_top'   => 'Sidebar Top (300×250)',
    'sidebar_bottom'=> 'Sidebar Bottom (300×250)',
];
$positions_display = (($_SESSION['admin_lang'] ?? 'bn') === 'en') ? $positions_en : $positions_bn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $title    = $conn->real_escape_string(trim($_POST['title'] ?? ''));
        $position = $conn->real_escape_string($_POST['position'] ?? '');
        $image    = $conn->real_escape_string(trim($_POST['image_url'] ?? ''));
        $link     = $conn->real_escape_string(trim($_POST['link_url'] ?? '#'));
        $active   = isset($_POST['is_active']) ? 1 : 0;
        if ($position && $image) {
            $r = $conn->query("INSERT INTO ads (title,position,image_url,link_url,is_active) VALUES ('$title','$position','$image','$link',$active)");
            $success = $r ? __('ad_added') : __('error_prefix') . $conn->error;
        } else { $error = __('ad_error'); }
    } elseif ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("UPDATE ads SET is_active = 1 - is_active WHERE id=$id");
        $success = __('status_updated_msg');
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("DELETE FROM ads WHERE id=$id");
        $success = __('ad_deleted');
    }
}

$ads = $conn->query("SELECT * FROM ads ORDER BY created_at DESC");
$admin_title = __('nav_ads'); include 'includes/admin_header.php';
?>
<div class="admin-main">
  <h1 class="page-title"><i class="fas fa-ad"></i> <?= __('ad_management') ?></h1>
  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

  <div class="form-grid">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-list"></i> <?= __('all_ads') ?></h3></div>
      <div class="table-wrap">
        <table class="admin-table">
          <thead><tr><th>#</th><th><?= __('col_title') ?></th><th><?= __('col_position') ?></th><th><?= __('image_label') ?></th><th><?= __('col_status') ?></th><th><?= __('col_actions') ?></th></tr></thead>
          <tbody>
            <?php $i=1; while ($ad = $ads->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($ad['title'] ?: '—') ?></td>
              <td><small><?= htmlspecialchars($positions_display[$ad['position']] ?? $ad['position']) ?></small></td>
              <td><img src="<?= htmlspecialchars($ad['image_url']) ?>" style="height:40px;border-radius:3px"></td>
              <td>
                <form method="POST" style="display:inline">
                  <input type="hidden" name="action" value="toggle">
                  <input type="hidden" name="id" value="<?= $ad['id'] ?>">
                  <button type="submit" class="btn-status <?= $ad['is_active']?'active':'inactive' ?>">
                    <i class="fas fa-<?= $ad['is_active']?'check-circle':'times-circle' ?>"></i>
                    <?= $ad['is_active'] ? __('status_active') : __('status_inactive') ?>
                  </button>
                </form>
              </td>
              <td>
                <form method="POST" style="display:inline">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $ad['id'] ?>">
                  <button type="submit" class="btn-sm delete" data-confirm="<?= __('confirm_del_ad') ?>"><i class="fas fa-trash"></i></button>
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
        <div class="admin-card-header"><h3><i class="fas fa-plus"></i> <?= __('new_ad') ?></h3></div>
        <div class="admin-card-body">
          <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group"><label><?= __('ad_title_optional') ?></label><input type="text" name="title" class="form-control"></div>
            <div class="form-group">
              <label><?= __('position') ?> <span class="required">*</span></label>
              <select name="position" class="form-control" required>
                <option value=""><?= __('select_position') ?></option>
                <?php foreach ($positions_display as $k => $v): ?>
                <option value="<?= $k ?>"><?= $v ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label><?= __('image_url_label') ?> <span class="required">*</span></label><input type="url" name="image_url" id="adImg" class="form-control" required></div>
            <div class="img-preview" style="display:none;margin-bottom:10px"><img id="adPreview" src="" style="max-width:100%;border-radius:4px"></div>
            <div class="form-group"><label><?= __('link_url') ?></label><input type="url" name="link_url" class="form-control"></div>
            <div class="form-group"><label class="form-check"><input type="checkbox" name="is_active" value="1" checked> <span><?= __('status_active') ?></span></label></div>
            <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> <?= __('btn_add') ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('adImg').addEventListener('input',function(){
    var p=document.querySelector('.img-preview'),img=document.getElementById('adPreview');
    if(this.value){p.style.display='block';img.src=this.value;}else{p.style.display='none';}
});
</script>
<?php include 'includes/admin_footer.php'; ?>

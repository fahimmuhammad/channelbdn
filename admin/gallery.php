<?php
require_once 'auth.php'; requirePermission('gallery');
require_once dirname(__DIR__) . '/config.php';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $title   = $conn->real_escape_string(trim($_POST['title'] ?? ''));
        $caption = $conn->real_escape_string(trim($_POST['caption'] ?? ''));
        $image   = $conn->real_escape_string(trim($_POST['image_url'] ?? ''));
        $order   = (int)($_POST['sort_order'] ?? 0);
        if ($title && $image) {
            $r = $conn->query("INSERT INTO photo_gallery (title,caption,image_url,sort_order,is_active) VALUES ('$title','$caption','$image',$order,1)");
            $success = $r ? __('photo_added') : __('error_prefix') . $conn->error;
        } else { $error = __('photo_error'); }
    } elseif ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("UPDATE photo_gallery SET is_active=1-is_active WHERE id=$id");
        $success = __('status_updated_msg');
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("DELETE FROM photo_gallery WHERE id=$id");
        $success = __('photo_deleted');
    }
}

$photos = $conn->query("SELECT * FROM photo_gallery ORDER BY sort_order ASC, created_at DESC");
$admin_title = __('nav_gallery'); include 'includes/admin_header.php';
?>
<div class="admin-main">
  <h1 class="page-title"><i class="fas fa-camera"></i> <?= __('gallery_management') ?></h1>
  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

  <div class="form-grid">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-images"></i> <?= __('all_photos') ?></h3></div>
      <div class="table-wrap">
        <table class="admin-table">
          <thead><tr><th>#</th><th><?= __('image_label') ?></th><th><?= __('col_title') ?></th><th><?= __('col_order') ?></th><th><?= __('col_status') ?></th><th><?= __('col_actions') ?></th></tr></thead>
          <tbody>
            <?php $i=1; while ($ph = $photos->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><img src="<?= htmlspecialchars($ph['image_url']) ?>" style="height:50px;border-radius:3px;object-fit:cover;width:80px"></td>
              <td><?= htmlspecialchars($ph['title']) ?><br><small style="color:#888"><?= htmlspecialchars(mb_substr($ph['caption']??'',0,50)) ?></small></td>
              <td><?= $ph['sort_order'] ?></td>
              <td>
                <form method="POST" style="display:inline"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $ph['id'] ?>">
                <button type="submit" class="btn-status <?= $ph['is_active']?'active':'inactive' ?>"><i class="fas fa-<?= $ph['is_active']?'check-circle':'times-circle' ?>"></i> <?= $ph['is_active'] ? __('status_active') : __('status_inactive') ?></button></form>
              </td>
              <td>
                <form method="POST" style="display:inline"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $ph['id'] ?>">
                <button type="submit" class="btn-sm delete" data-confirm="<?= __('confirm_del_photo') ?>"><i class="fas fa-trash"></i></button></form>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div>
      <div class="admin-card">
        <div class="admin-card-header"><h3><i class="fas fa-plus"></i> <?= __('new_photo') ?></h3></div>
        <div class="admin-card-body">
          <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group"><label><?= __('col_title') ?> <span class="required">*</span></label><input type="text" name="title" class="form-control" required></div>
            <div class="form-group"><label><?= __('caption') ?></label><textarea name="caption" class="form-control" rows="2"></textarea></div>
            <div class="form-group"><label><?= __('image_url_label') ?> <span class="required">*</span></label><input type="url" name="image_url" id="galImg" class="form-control" required></div>
            <div class="img-preview" style="display:none;margin-bottom:10px"><img id="galPreview" src="" style="max-width:100%;border-radius:4px"></div>
            <div class="form-group"><label><?= __('sort_order_label') ?></label><input type="number" name="sort_order" class="form-control" value="0" min="0"></div>
            <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> <?= __('btn_add') ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('galImg').addEventListener('input',function(){
    var p=document.querySelector('.img-preview'),img=document.getElementById('galPreview');
    if(this.value){p.style.display='block';img.src=this.value;}else{p.style.display='none';}
});
</script>
<?php include 'includes/admin_footer.php'; ?>

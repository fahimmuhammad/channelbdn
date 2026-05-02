<?php
require_once 'auth.php'; requirePermission('videos');
require_once dirname(__DIR__) . '/config.php';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $title = $conn->real_escape_string(trim($_POST['title'] ?? ''));
        $yt    = $conn->real_escape_string(trim($_POST['youtube_url'] ?? ''));
        $thumb = $conn->real_escape_string(trim($_POST['thumbnail'] ?? ''));
        if ($title && $yt) {
            if (!$thumb && preg_match('/(?:v=|youtu\.be\/)([^&\s]+)/', $yt, $m)) {
                $thumb = 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
            }
            $r = $conn->query("INSERT INTO videos (title,youtube_url,thumbnail,is_active) VALUES ('$title','$yt','$thumb',1)");
            $success = $r ? __('video_added') : __('error_prefix') . $conn->error;
        } else { $error = __('video_error'); }
    } elseif ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("UPDATE videos SET is_active=1-is_active WHERE id=$id");
        $success = __('status_updated_msg');
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("DELETE FROM videos WHERE id=$id");
        $success = __('video_deleted');
    }
}

$videos = $conn->query("SELECT * FROM videos ORDER BY created_at DESC");
$admin_title = __('nav_videos'); include 'includes/admin_header.php';
?>
<div class="admin-main">
  <h1 class="page-title"><i class="fas fa-video"></i> <?= __('video_management') ?></h1>
  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

  <div class="form-grid">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-list"></i> <?= __('all_videos') ?></h3></div>
      <div class="table-wrap">
        <table class="admin-table">
          <thead><tr><th>#</th><th><?= __('col_thumbnail') ?></th><th><?= __('col_title') ?></th><th><?= __('col_status') ?></th><th><?= __('col_actions') ?></th></tr></thead>
          <tbody>
            <?php $i=1; while ($v = $videos->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?php if($v['thumbnail']): ?><img src="<?= htmlspecialchars($v['thumbnail']) ?>" style="height:50px;width:80px;object-fit:cover;border-radius:3px"><?php endif; ?></td>
              <td><?= htmlspecialchars($v['title']) ?><br><a href="<?= htmlspecialchars($v['youtube_url']) ?>" target="_blank" style="font-size:11px;color:#3498db"><?= __('view_video') ?></a></td>
              <td>
                <form method="POST" style="display:inline"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $v['id'] ?>">
                <button type="submit" class="btn-status <?= $v['is_active']?'active':'inactive' ?>"><i class="fas fa-<?= $v['is_active']?'check-circle':'times-circle' ?>"></i> <?= $v['is_active'] ? __('status_active') : __('status_inactive') ?></button></form>
              </td>
              <td>
                <form method="POST" style="display:inline"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $v['id'] ?>">
                <button type="submit" class="btn-sm delete" data-confirm="<?= __('confirm_del_video') ?>"><i class="fas fa-trash"></i></button></form>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div>
      <div class="admin-card">
        <div class="admin-card-header"><h3><i class="fas fa-plus"></i> <?= __('new_video') ?></h3></div>
        <div class="admin-card-body">
          <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group"><label><?= __('col_title') ?> <span class="required">*</span></label><input type="text" name="title" class="form-control" required></div>
            <div class="form-group"><label><?= __('youtube_url_label') ?> <span class="required">*</span></label><input type="url" name="youtube_url" class="form-control" required placeholder="https://youtube.com/watch?v=..."></div>
            <div class="form-group"><label><?= __('thumb_optional') ?></label><input type="url" name="thumbnail" id="vidThumb" class="form-control"></div>
            <div class="img-preview" style="display:none;margin-bottom:10px"><img id="vidPreview" src="" style="max-width:100%;border-radius:4px"></div>
            <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> <?= __('btn_add') ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('vidThumb').addEventListener('input',function(){
    var p=document.querySelector('.img-preview'),img=document.getElementById('vidPreview');
    if(this.value){p.style.display='block';img.src=this.value;}else{p.style.display='none';}
});
</script>
<?php include 'includes/admin_footer.php'; ?>

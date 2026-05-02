<?php
require_once 'auth.php';
requirePermission('edit_post');
require_once dirname(__DIR__) . '/includes/functions.php';
global $conn;

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: posts.php'); exit; }

$post = $conn->query("SELECT * FROM posts WHERE id=$id")->fetch_assoc();
if (!$post) { header('Location: posts.php'); exit; }

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $slug     = trim($_POST['slug'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $excerpt  = trim($_POST['excerpt'] ?? '');
    $cat_id   = (int)($_POST['category_id'] ?? 0);
    $author   = trim($_POST['author'] ?? '');
    $image        = trim($_POST['image'] ?? '');
    $status       = $_POST['status'] ?? 'published';
    $scheduled_at = trim($_POST['scheduled_at'] ?? '');
    $featured     = isset($_POST['is_featured']) ? 1 : 0;
    $breaking = isset($_POST['is_breaking']) ? 1 : 0;
    $top      = isset($_POST['is_top']) ? 1 : 0;
    $curated  = isset($_POST['is_curated']) ? 1 : 0;

    // Handle image upload
    if (!empty($_FILES['image_file']['name'])) {
        $uploaded = handleImageUpload($_FILES['image_file']);
        if ($uploaded) $image = $uploaded;
        else $error = 'ছবি আপলোড ব্যর্থ হয়েছে।';
    }
    if (empty($error)) {
    if (empty($title) || empty($slug)) { $error = __('post_error_slug'); }
    else {
        $t  = $conn->real_escape_string($title); $s = $conn->real_escape_string($slug);
        $c  = $conn->real_escape_string($content); $e = $conn->real_escape_string($excerpt);
        $a  = $conn->real_escape_string($author); $i = $conn->real_escape_string($image);
        $st = $conn->real_escape_string($status);
        $sa = ($status === 'scheduled' && $scheduled_at) ? "'" . $conn->real_escape_string($scheduled_at) . "'" : 'NULL';
        $res = $conn->query("UPDATE posts SET title='$t',slug='$s',content='$c',excerpt='$e',category_id=$cat_id,author='$a',image='$i',status='$st',scheduled_at=$sa,is_featured=$featured,is_breaking=$breaking,is_top=$top,is_curated=$curated WHERE id=$id");
        if ($res) {
            logActivity('edit_post', "পোস্ট আইডি $id সম্পাদনা: $title");
            $success = __('post_success_edit');
            $post = $conn->query("SELECT * FROM posts WHERE id=$id")->fetch_assoc();
        }
        else { $error = __('error_prefix') . $conn->error; }
    }
    } // end empty($error)
}

$categories = getCategories();
$admin_title = __('edit_post_title');
include 'includes/admin_header.php';
?>

<div class="admin-main">
  <h1 class="page-title"><i class="fas fa-edit"></i> <?= __('edit_post_title') ?></h1>
  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data">
    <div class="form-grid">
      <div>
        <div class="admin-card">
          <div class="admin-card-header"><h3><i class="fas fa-pen"></i> <?= __('post_content') ?></h3></div>
          <div class="admin-card-body">
            <div class="form-group">
              <label><?= __('col_title') ?></label>
              <input type="text" name="title" id="postTitle" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
            </div>
            <div class="form-group">
              <label><?= __('col_slug') ?></label>
              <input type="text" name="slug" id="postSlug" data-locked="1" class="form-control" value="<?= htmlspecialchars($post['slug']) ?>" required>
            </div>
            <div class="form-group">
              <label><?= __('excerpt') ?></label>
              <textarea name="excerpt" class="form-control" rows="3"><?= htmlspecialchars($post['excerpt']) ?></textarea>
            </div>
            <div class="form-group">
              <label><?= __('main_content') ?></label>
              <textarea name="content" class="form-control" rows="14" required><?= htmlspecialchars($post['content']) ?></textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="form-sidebar">
        <div class="admin-card">
          <div class="admin-card-header"><h3><i class="fas fa-cog"></i> <?= __('publish_settings') ?></h3></div>
          <div class="admin-card-body">
            <div class="form-group">
              <label><?= __('col_status') ?></label>
              <select name="status" class="form-control" id="statusSelect" onchange="toggleSchedule(this.value)">
                <option value="published" <?= $post['status']=='published'?'selected':'' ?>><?= __('status_published') ?></option>
                <option value="draft" <?= $post['status']=='draft'?'selected':'' ?>><?= __('status_draft') ?></option>
                <option value="scheduled" <?= $post['status']=='scheduled'?'selected':'' ?>>নির্ধারিত সময়ে প্রকাশ</option>
              </select>
            </div>
            <div class="form-group" id="scheduledAtGroup" style="<?= $post['status']=='scheduled'?'':'display:none' ?>">
              <label>প্রকাশের তারিখ ও সময়</label>
              <input type="datetime-local" name="scheduled_at" class="form-control"
                     value="<?= htmlspecialchars($post['scheduled_at'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label><?= __('col_category') ?></label>
              <select name="category_id" class="form-control">
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id']==$post['category_id']?'selected':'' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label><?= __('col_author') ?></label>
              <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($post['author']) ?>">
            </div>
            <div class="form-group">
              <label class="form-check"><input type="checkbox" name="is_featured" value="1" <?= $post['is_featured']?'checked':'' ?>> <?= __('featured_news') ?></label>
            </div>
            <div class="form-group">
              <label class="form-check"><input type="checkbox" name="is_breaking" value="1" <?= $post['is_breaking']?'checked':'' ?>> <?= __('breaking_news') ?></label>
            </div>
            <div class="form-group">
              <label class="form-check"><input type="checkbox" name="is_top" value="1" <?= $post['is_top']?'checked':'' ?>> <?= __('top_news') ?></label>
            </div>
            <div class="form-group">
              <label class="form-check"><input type="checkbox" name="is_curated" value="1" <?= !empty($post['is_curated'])?'checked':'' ?>> <?= __('curated') ?></label>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn-primary"><i class="fas fa-save"></i> <?= __('btn_update') ?></button>
              <a href="posts.php" class="btn-secondary"><?= __('btn_cancel') ?></a>
            </div>
          </div>
        </div>

        <div class="admin-card">
          <div class="admin-card-header"><h3><i class="fas fa-image"></i> <?= __('image_label') ?></h3></div>
          <div class="admin-card-body">
            <div class="form-group">
              <label>নতুন ফাইল আপলোড করুন</label>
              <input type="file" name="image_file" class="form-control form-control-file" accept="image/jpeg,image/png,image/webp" onchange="previewUpload(this)">
            </div>
            <div class="form-group">
              <label><?= __('image_url_label') ?></label>
              <input type="url" name="image" id="imageUrl" class="form-control" value="<?= htmlspecialchars($post['image']) ?>">
            </div>
            <div class="img-preview" id="imgPreviewWrap">
              <img id="imagePreview" src="<?= htmlspecialchars($post['image']) ?>" alt="Preview" style="max-width:100%;border-radius:6px">
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
function toggleSchedule(v) {
    document.getElementById('scheduledAtGroup').style.display = v === 'scheduled' ? 'block' : 'none';
}
function previewUpload(input) {
    if (input.files && input.files[0]) {
        document.getElementById('imagePreview').src = URL.createObjectURL(input.files[0]);
        document.getElementById('imgPreviewWrap').style.display = 'block';
    }
}
var urlInput = document.getElementById('imageUrl');
if (urlInput) urlInput.addEventListener('input', function(){
    if (this.value) document.getElementById('imagePreview').src = this.value;
});
</script>
<?php include 'includes/admin_footer.php'; ?>

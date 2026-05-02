<?php
require_once 'auth.php';
requirePermission('add_post');
require_once dirname(__DIR__) . '/includes/functions.php';
global $conn;

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = trim($_POST['title'] ?? '');
    $slug         = trim($_POST['slug'] ?? '');
    $content      = trim($_POST['content'] ?? '');
    $excerpt      = trim($_POST['excerpt'] ?? '');
    $cat_id       = (int)($_POST['category_id'] ?? 0);
    $author       = trim($_POST['author'] ?? 'নিজস্ব প্রতিবেদক');
    $image        = trim($_POST['image'] ?? '');
    $status       = $_POST['status'] ?? 'published';
    $scheduled_at = trim($_POST['scheduled_at'] ?? '');
    $featured     = isset($_POST['is_featured']) ? 1 : 0;
    $breaking     = isset($_POST['is_breaking']) ? 1 : 0;
    $top          = isset($_POST['is_top']) ? 1 : 0;
    $curated      = isset($_POST['is_curated']) ? 1 : 0;

    // Handle image file upload
    if (!empty($_FILES['image_file']['name'])) {
        $uploaded = handleImageUpload($_FILES['image_file']);
        if ($uploaded) $image = $uploaded;
        else $error = 'ছবি আপলোড ব্যর্থ হয়েছে। শুধুমাত্র JPG/PNG/WebP গ্রহণযোগ্য।';
    }

    if (empty($error)) {
        if (empty($title) || empty($slug) || empty($cat_id)) {
            $error = __('post_error_fields');
        } else {
            $t  = $conn->real_escape_string($title);
            $s  = $conn->real_escape_string($slug);
            $c  = $conn->real_escape_string($content);
            $e  = $conn->real_escape_string($excerpt);
            $a  = $conn->real_escape_string($author);
            $i  = $conn->real_escape_string($image);
            $st = $conn->real_escape_string($status);
            $sa = ($status === 'scheduled' && $scheduled_at) ? "'" . $conn->real_escape_string($scheduled_at) . "'" : 'NULL';
            $res = $conn->query("INSERT INTO posts (title,slug,content,excerpt,category_id,author,image,status,scheduled_at,is_featured,is_breaking,is_top,is_curated) VALUES ('$t','$s','$c','$e',$cat_id,'$a','$i','$st',$sa,$featured,$breaking,$top,$curated)");
            if ($res) {
                logActivity('add_post', "নতুন সংবাদ: $title");
                $success = __('post_success_add');
            } else { $error = __('error_prefix') . $conn->error; }
        }
    }
}

$categories = getCategories();
$admin_title = __('nav_add_post');
include 'includes/admin_header.php';
?>

<div class="admin-main">
  <h1 class="page-title"><i class="fas fa-plus-circle"></i> <?= __('add_new_post') ?></h1>
  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data">
    <div class="form-grid">
      <div>
        <div class="admin-card">
          <div class="admin-card-header"><h3><i class="fas fa-pen"></i> <?= __('post_content') ?></h3></div>
          <div class="admin-card-body">
            <div class="form-group">
              <label><?= __('col_title') ?> <span class="required">*</span></label>
              <input type="text" name="title" id="postTitle" class="form-control" placeholder="<?= __('col_title') ?>" required>
            </div>
            <div class="form-group">
              <label><?= __('col_slug') ?> <span class="required">*</span></label>
              <input type="text" name="slug" id="postSlug" class="form-control" placeholder="post-slug-here" required>
            </div>
            <div class="form-group">
              <label><?= __('excerpt') ?></label>
              <textarea name="excerpt" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label><?= __('main_content') ?> <span class="required">*</span></label>
              <textarea name="content" class="form-control" rows="14" required></textarea>
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
                <option value="published"><?= __('status_published') ?></option>
                <option value="draft"><?= __('status_draft') ?></option>
                <option value="scheduled">নির্ধারিত সময়ে প্রকাশ</option>
              </select>
            </div>
            <div class="form-group" id="scheduledAtGroup" style="display:none">
              <label>প্রকাশের তারিখ ও সময় <span class="required">*</span></label>
              <input type="datetime-local" name="scheduled_at" class="form-control">
            </div>
            <div class="form-group">
              <label><?= __('col_category') ?> <span class="required">*</span></label>
              <select name="category_id" class="form-control" required>
                <option value=""><?= __('select_category') ?></option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label><?= __('col_author') ?></label>
              <input type="text" name="author" class="form-control" value="নিজস্ব প্রতিবেদক">
            </div>
            <div class="form-group">
              <label class="form-check"><input type="checkbox" name="is_featured" value="1"> <span><?= __('featured_news') ?></span></label>
            </div>
            <div class="form-group">
              <label class="form-check"><input type="checkbox" name="is_breaking" value="1"> <span><?= __('breaking_news') ?></span></label>
            </div>
            <div class="form-group">
              <label class="form-check"><input type="checkbox" name="is_top" value="1"> <span><?= __('top_news') ?></span></label>
            </div>
            <div class="form-group">
              <label class="form-check"><input type="checkbox" name="is_curated" value="1"> <span><?= __('curated') ?></span></label>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn-primary"><i class="fas fa-paper-plane"></i> <?= __('btn_publish') ?></button>
              <a href="posts.php" class="btn-secondary"><?= __('btn_cancel') ?></a>
            </div>
          </div>
        </div>

        <div class="admin-card">
          <div class="admin-card-header"><h3><i class="fas fa-image"></i> <?= __('image_label') ?></h3></div>
          <div class="admin-card-body">
            <div class="form-group">
              <label>ফাইল আপলোড করুন</label>
              <input type="file" name="image_file" id="imageFile" class="form-control form-control-file" accept="image/jpeg,image/png,image/webp" onchange="previewUpload(this)">
              <p style="font-size:11px;color:#888;margin-top:4px">JPG, PNG বা WebP (সর্বোচ্চ ৫ MB)</p>
            </div>
            <div class="form-group">
              <label><?= __('image_url_label') ?> (অথবা URL)</label>
              <input type="url" name="image" id="imageUrl" class="form-control" placeholder="https://...">
            </div>
            <div class="img-preview" id="imgPreviewWrap" style="display:none;margin-top:8px">
              <img id="imagePreview" src="" alt="Preview" style="max-width:100%;border-radius:6px">
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
        var wrap = document.getElementById('imgPreviewWrap');
        var img  = document.getElementById('imagePreview');
        img.src = URL.createObjectURL(input.files[0]);
        wrap.style.display = 'block';
    }
}
// URL preview
var urlInput = document.getElementById('imageUrl');
if (urlInput) urlInput.addEventListener('input', function(){
    var wrap = document.getElementById('imgPreviewWrap');
    var img  = document.getElementById('imagePreview');
    if (this.value) { img.src = this.value; wrap.style.display = 'block'; }
});
// Auto slug from title
var titleInp = document.getElementById('postTitle');
var slugInp  = document.getElementById('postSlug');
if (titleInp && slugInp) {
    titleInp.addEventListener('input', function(){
        if (slugInp.dataset.locked) return;
        slugInp.value = this.value.toLowerCase().replace(/[^\wঀ-৿\s-]/g,'').replace(/[\s]+/g,'-').trim();
    });
    slugInp.addEventListener('input', function(){ this.dataset.locked = '1'; });
}
</script>
<?php include 'includes/admin_footer.php'; ?>

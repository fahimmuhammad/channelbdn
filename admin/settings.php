<?php
require_once 'auth.php'; requirePermission('settings');
require_once dirname(__DIR__) . '/config.php';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['site_name','site_tagline','site_email','site_phone','facebook','twitter','youtube','instagram','linkedin','address','ads_email','editor_name','publisher_name','footer_about'];
    foreach ($fields as $f) {
        $k = $conn->real_escape_string($f);
        $v = $conn->real_escape_string(trim($_POST[$f] ?? ''));
        $conn->query("INSERT INTO settings (setting_key,setting_value) VALUES ('$k','$v') ON DUPLICATE KEY UPDATE setting_value='$v'");
    }
    $success = __('settings_saved');
}

$s = [];
$res = $conn->query("SELECT * FROM settings");
while ($row = $res->fetch_assoc()) $s[$row['setting_key']] = $row['setting_value'];
$admin_title = __('nav_settings');
include 'includes/admin_header.php';
?>
<div class="admin-main">
  <h1 class="page-title"><i class="fas fa-cog"></i> <?= __('site_settings') ?></h1>
  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
  <form method="POST">
    <div class="form-grid">
      <div>
        <div class="admin-card">
          <div class="admin-card-header"><h3><i class="fas fa-info-circle"></i> <?= __('site_info') ?></h3></div>
          <div class="admin-card-body">
            <div class="form-group"><label><?= __('site_name_label') ?></label><input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($s['site_name']??'') ?>"></div>
            <div class="form-group"><label><?= __('tagline') ?></label><input type="text" name="site_tagline" class="form-control" value="<?= htmlspecialchars($s['site_tagline']??'') ?>"></div>
            <div class="form-group"><label><?= __('email') ?></label><input type="email" name="site_email" class="form-control" value="<?= htmlspecialchars($s['site_email']??'') ?>"></div>
            <div class="form-group"><label><?= __('phone') ?></label><input type="text" name="site_phone" class="form-control" value="<?= htmlspecialchars($s['site_phone']??'') ?>"></div>
            <div class="form-group"><label><?= __('address') ?></label><input type="text" name="address" class="form-control" value="<?= htmlspecialchars($s['address']??'') ?>"></div>
          </div>
        </div>
        <div class="admin-card" style="margin-top:16px">
          <div class="admin-card-header"><h3><i class="fas fa-users"></i> <?= __('editorial_info') ?></h3></div>
          <div class="admin-card-body">
            <div class="form-group"><label><?= __('editor_name') ?></label><input type="text" name="editor_name" class="form-control" value="<?= htmlspecialchars($s['editor_name']??'') ?>"></div>
            <div class="form-group"><label><?= __('publisher_name') ?></label><input type="text" name="publisher_name" class="form-control" value="<?= htmlspecialchars($s['publisher_name']??'') ?>"></div>
            <div class="form-group"><label><?= __('ads_email') ?></label><input type="email" name="ads_email" class="form-control" value="<?= htmlspecialchars($s['ads_email']??'') ?>"></div>
            <div class="form-group"><label><?= __('footer_about') ?></label><textarea name="footer_about" class="form-control" rows="3"><?= htmlspecialchars($s['footer_about']??'') ?></textarea></div>
          </div>
        </div>
      </div>
      <div>
        <div class="admin-card">
          <div class="admin-card-header"><h3><i class="fas fa-share-alt"></i> <?= __('social_media') ?></h3></div>
          <div class="admin-card-body">
            <div class="form-group"><label><i class="fab fa-facebook" style="color:#1877f2"></i> <?= __('facebook_url') ?></label><input type="url" name="facebook" class="form-control" value="<?= htmlspecialchars($s['facebook']??'') ?>"></div>
            <div class="form-group"><label><i class="fab fa-x-twitter"></i> <?= __('twitter_url') ?></label><input type="url" name="twitter" class="form-control" value="<?= htmlspecialchars($s['twitter']??'') ?>"></div>
            <div class="form-group"><label><i class="fab fa-youtube" style="color:#ff0000"></i> <?= __('youtube_url') ?></label><input type="url" name="youtube" class="form-control" value="<?= htmlspecialchars($s['youtube']??'') ?>"></div>
            <div class="form-group"><label><i class="fab fa-instagram" style="color:#e1306c"></i> <?= __('instagram_url') ?></label><input type="url" name="instagram" class="form-control" value="<?= htmlspecialchars($s['instagram']??'') ?>"></div>
            <div class="form-group"><label><i class="fab fa-linkedin" style="color:#0a66c2"></i> <?= __('linkedin_url') ?></label><input type="url" name="linkedin" class="form-control" value="<?= htmlspecialchars($s['linkedin']??'') ?>"></div>
            <div class="form-actions"><button type="submit" class="btn-primary"><i class="fas fa-save"></i> <?= __('btn_save') ?></button></div>
          </div>
        </div>
        <div class="admin-card" style="margin-top:16px">
          <div class="admin-card-header"><h3><i class="fas fa-link"></i> <?= __('quick_links') ?></h3></div>
          <div class="admin-card-body" style="display:flex;flex-direction:column;gap:8px">
            <a href="<?= SITE_URL ?>/admin/ads.php" class="btn-secondary" style="text-align:center"><i class="fas fa-ad"></i> <?= __('nav_ads') ?></a>
            <a href="<?= SITE_URL ?>/admin/polls.php" class="btn-secondary" style="text-align:center"><i class="fas fa-poll"></i> <?= __('nav_polls') ?></a>
            <a href="<?= SITE_URL ?>/admin/gallery.php" class="btn-secondary" style="text-align:center"><i class="fas fa-camera"></i> <?= __('nav_gallery') ?></a>
            <a href="<?= SITE_URL ?>/admin/videos.php" class="btn-secondary" style="text-align:center"><i class="fas fa-video"></i> <?= __('nav_videos') ?></a>
            <a href="<?= SITE_URL ?>/admin/homepage.php" class="btn-secondary" style="text-align:center"><i class="fas fa-th-large"></i> <?= __('nav_homepage') ?></a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<?php include 'includes/admin_footer.php'; ?>

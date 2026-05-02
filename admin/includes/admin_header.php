<?php
if (!isLoggedIn()) { header('Location: ' . SITE_URL . '/admin/login.php'); exit; }
require_once __DIR__ . '/lang.php';
$_cur_lang = $_SESSION['admin_lang'] ?? 'bn';
?>
<!DOCTYPE html>
<html lang="<?= $_cur_lang === 'en' ? 'en' : 'bn' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($admin_title) ? htmlspecialchars($admin_title) . ' | ' : '' ?><?= __('admin_panel') ?> | <?= SITE_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/css/admin.css">
    <script>(function(){ if(localStorage.getItem('adminDark')==='1') document.documentElement.setAttribute('data-theme','dark'); })();</script>
</head>
<body>
<div class="admin-wrapper">
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
      <h2><?= SITE_NAME ?></h2>
      <p><?= __('admin_panel') ?></p>
    </div>
    <nav class="sidebar-nav">
      <?php $cur = basename($_SERVER['PHP_SELF']); ?>

      <div class="nav-section-label"><?= __('nav_main') ?></div>
      <a href="<?= SITE_URL ?>/admin/" class="<?= $cur=='index.php'?'active':'' ?>"><i class="fas fa-tachometer-alt"></i> <?= __('dashboard') ?></a>

      <div class="nav-section-label"><?= __('nav_news') ?></div>
      <a href="<?= SITE_URL ?>/admin/posts.php" class="<?= in_array($cur,['posts.php','edit_post.php'])?'active':'' ?>"><i class="fas fa-newspaper"></i> <?= __('nav_posts') ?></a>
      <a href="<?= SITE_URL ?>/admin/add_post.php" class="<?= $cur=='add_post.php'?'active':'' ?>"><i class="fas fa-plus-circle"></i> <?= __('nav_add_post') ?></a>
      <a href="<?= SITE_URL ?>/admin/categories.php" class="<?= $cur=='categories.php'?'active':'' ?>"><i class="fas fa-list"></i> <?= __('nav_categories') ?></a>

      <div class="nav-section-label"><?= __('nav_media') ?></div>
      <a href="<?= SITE_URL ?>/admin/videos.php" class="<?= $cur=='videos.php'?'active':'' ?>"><i class="fas fa-video"></i> <?= __('nav_videos') ?></a>
      <a href="<?= SITE_URL ?>/admin/gallery.php" class="<?= $cur=='gallery.php'?'active':'' ?>"><i class="fas fa-camera"></i> <?= __('nav_gallery') ?></a>

      <div class="nav-section-label"><?= __('nav_marketing') ?></div>
      <a href="<?= SITE_URL ?>/admin/ads.php" class="<?= $cur=='ads.php'?'active':'' ?>"><i class="fas fa-ad"></i> <?= __('nav_ads') ?></a>
      <a href="<?= SITE_URL ?>/admin/polls.php" class="<?= $cur=='polls.php'?'active':'' ?>"><i class="fas fa-poll"></i> <?= __('nav_polls') ?></a>
      <a href="<?= SITE_URL ?>/admin/comments.php" class="<?= $cur=='comments.php'?'active':'' ?>"><i class="far fa-comments"></i> মন্তব্য</a>
      <a href="<?= SITE_URL ?>/admin/newsletter.php" class="<?= $cur=='newsletter.php'?'active':'' ?>"><i class="fas fa-envelope-open-text"></i> নিউজলেটার</a>

      <div class="nav-section-label">পাঠক</div>
      <a href="<?= SITE_URL ?>/admin/readers.php" class="<?= $cur=='readers.php'?'active':'' ?>"><i class="fas fa-user-friends"></i> পাঠক অ্যাকাউন্ট</a>

      <div class="nav-section-label"><?= __('nav_site') ?></div>
      <a href="<?= SITE_URL ?>/admin/homepage.php" class="<?= $cur=='homepage.php'?'active':'' ?>"><i class="fas fa-th-large"></i> <?= __('nav_homepage') ?></a>
      <a href="<?= SITE_URL ?>/admin/settings.php" class="<?= $cur=='settings.php'?'active':'' ?>"><i class="fas fa-cog"></i> <?= __('nav_settings') ?></a>

      <?php if (hasPermission('users')): ?>
      <div class="nav-section-label"><?= __('nav_users_section') ?></div>
      <a href="<?= SITE_URL ?>/admin/users.php" class="<?= $cur=='users.php'?'active':'' ?>"><i class="fas fa-users"></i> <?= __('nav_users') ?></a>
      <a href="<?= SITE_URL ?>/admin/activity_log.php" class="<?= $cur=='activity_log.php'?'active':'' ?>"><i class="fas fa-history"></i> <?= __('nav_activity_log') ?></a>
      <?php endif; ?>

      <div class="nav-section-label"><?= __('nav_general') ?></div>
      <a href="<?= SITE_URL ?>/" target="_blank"><i class="fas fa-external-link-alt"></i> <?= __('nav_view_site') ?></a>
      <a href="<?= SITE_URL ?>/admin/logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> <?= __('nav_logout') ?></a>
    </nav>
  </aside>

  <div class="admin-content">
    <div class="admin-topbar">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
      <div class="topbar-right">
        <!-- Dark mode toggle -->
        <button class="dm-admin-btn" id="adminDmBtn" title="ডার্ক মোড"><i class="fas fa-moon" id="adminDmIcon"></i></button>
        <!-- Language toggle -->
        <div class="admin-lang-toggle">
          <a href="<?= SITE_URL ?>/admin/setlang.php?lang=bn" class="lang-opt <?= $_cur_lang==='bn'?'active':'' ?>" title="বাংলা">বাং</a>
          <span class="lang-sep">|</span>
          <a href="<?= SITE_URL ?>/admin/setlang.php?lang=en" class="lang-opt <?= $_cur_lang==='en'?'active':'' ?>" title="English">EN</a>
        </div>
        <a href="<?= SITE_URL ?>/" target="_blank" style="font-size:12px;color:#888;margin-right:12px"><i class="fas fa-eye"></i> <?= __('view_site') ?></a>
        <span class="admin-user">
          <i class="fas fa-user-circle"></i>
          <?= htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin_user']) ?>
          <span class="role-badge role-<?= getUserRole() ?>"><?= getUserRole() ?></span>
        </span>
      </div>
    </div>
<script>
(function(){
    var btn  = document.getElementById('adminDmBtn');
    var ico  = document.getElementById('adminDmIcon');
    function sync(){
        var dark = document.documentElement.getAttribute('data-theme') === 'dark';
        ico.className = dark ? 'fas fa-sun' : 'fas fa-moon';
        btn.title = dark ? 'লাইট মোড' : 'ডার্ক মোড';
    }
    sync();
    btn.addEventListener('click', function(){
        var dark = document.documentElement.getAttribute('data-theme') === 'dark';
        if(dark){
            document.documentElement.removeAttribute('data-theme');
            localStorage.setItem('adminDark','0');
        } else {
            document.documentElement.setAttribute('data-theme','dark');
            localStorage.setItem('adminDark','1');
        }
        sync();
    });
})();
</script>

<?php
require_once 'reader_auth.php';
require_once 'includes/functions.php';

if (!readerIsLoggedIn()) {
    header('Location: ' . SITE_URL . '/reader_login.php?redirect=' . urlencode(SITE_URL . '/reader_profile.php'));
    exit;
}
$reader = readerGet();
if (!$reader) { readerLogout(); header('Location: ' . SITE_URL . '/'); exit; }

$tab = $_GET['tab'] ?? 'bookmarks';
$msg = ''; $err = '';

/* ── settings update ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $bio  = trim($_POST['bio']  ?? '');
        if (strlen($name) < 2) { $err = 'নাম কমপক্ষে ২ অক্ষর হতে হবে।'; }
        else {
            $nm = $conn->real_escape_string($name);
            $bi = $conn->real_escape_string($bio);
            $conn->query("UPDATE readers SET name='$nm', bio='$bi' WHERE id={$reader['id']}");
            $_SESSION['reader_name'] = $name;
            $reader['name'] = $name; $reader['bio'] = $bio;
            $msg = 'প্রোফাইল আপডেট হয়েছে।';
        }
    } elseif ($_POST['action'] === 'change_password') {
        $cur = $_POST['cur_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $rid = (int)$reader['id'];
        $r   = $conn->query("SELECT password FROM readers WHERE id=$rid");
        $row = $r->fetch_assoc();
        if (!password_verify($cur, $row['password'])) { $err = 'বর্তমান পাসওয়ার্ড ভুল।'; }
        elseif (strlen($new) < 6) { $err = 'নতুন পাসওয়ার্ড কমপক্ষে ৬ অক্ষর হতে হবে।'; }
        else {
            $ph = $conn->real_escape_string(password_hash($new, PASSWORD_DEFAULT));
            $conn->query("UPDATE readers SET password='$ph' WHERE id=$rid");
            $msg = 'পাসওয়ার্ড পরিবর্তন হয়েছে।';
        }
    }
    $tab = 'settings';
}

/* ── fetch tab data ── */
$rid = (int)$reader['id'];

$bookmarks = [];
if ($tab === 'bookmarks') {
    $r = $conn->query("SELECT p.*, c.name as category_name, c.slug as category_slug
                       FROM reader_bookmarks rb
                       JOIN posts p ON rb.post_id=p.id
                       JOIN categories c ON p.category_id=c.id
                       WHERE rb.reader_id=$rid AND p.status='published'
                       ORDER BY rb.created_at DESC LIMIT 30");
    $bookmarks = $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
}

$history = [];
if ($tab === 'history') {
    $r = $conn->query("SELECT p.*, c.name as category_name, c.slug as category_slug, rh.viewed_at
                       FROM reader_history rh
                       JOIN posts p ON rh.post_id=p.id
                       JOIN categories c ON p.category_id=c.id
                       WHERE rh.reader_id=$rid AND p.status='published'
                       ORDER BY rh.viewed_at DESC LIMIT 50");
    $history = $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
}

$my_comments = [];
if ($tab === 'comments') {
    $r = $conn->query("SELECT rc.*, p.title as post_title, p.slug as post_slug
                       FROM reader_comments rc
                       JOIN posts p ON rc.post_id=p.id
                       WHERE rc.reader_id=$rid
                       ORDER BY rc.created_at DESC LIMIT 30");
    $my_comments = $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
}

$newsletter = null;
if ($tab === 'newsletter') {
    $em = $conn->real_escape_string($reader['email']);
    $r  = $conn->query("SELECT * FROM newsletter_subscribers WHERE email='$em' LIMIT 1");
    $newsletter = ($r && $r->num_rows) ? $r->fetch_assoc() : null;
}

$page_title = 'আমার প্রোফাইল';
require_once 'includes/header.php';
?>
<style>
.profile-wrap { max-width:960px; margin:32px auto 60px; padding:0 16px; display:grid; grid-template-columns:240px 1fr; gap:24px; }
.profile-sidebar { background:var(--card-bg,#fff); border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.07); padding:24px; height:fit-content; }
[data-theme="dark"] .profile-sidebar { background:#1e2130; }
.profile-avatar { width:80px; height:80px; border-radius:50%; background:#e8001c; color:#fff; font-size:2rem; font-weight:800; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; }
.profile-name { font-size:1.1rem; font-weight:700; text-align:center; color:var(--text-color,#1a1a1a); }
[data-theme="dark"] .profile-name { color:#e4e6ef; }
.profile-email { font-size:.8rem; color:#888; text-align:center; margin-top:4px; margin-bottom:20px; }
.profile-nav a { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; font-size:.9rem; font-weight:600; color:#555; transition:all .2s; margin-bottom:4px; }
[data-theme="dark"] .profile-nav a { color:#9aa0b8; }
.profile-nav a:hover { background:#fff5f5; color:#e8001c; }
[data-theme="dark"] .profile-nav a:hover { background:rgba(232,0,28,.1); color:#e8001c; }
.profile-nav a.active { background:#e8001c; color:#fff; }
.profile-nav a i { width:18px; text-align:center; }
.profile-logout { display:block; text-align:center; margin-top:20px; padding:9px; border-radius:8px; border:1px solid #ddd; color:#888; font-size:.85rem; transition:all .2s; }
[data-theme="dark"] .profile-logout { border-color:#2c3145; color:#9aa0b8; }
.profile-logout:hover { background:#fff0f0; color:#e8001c; border-color:#e8001c; }

.profile-main { background:var(--card-bg,#fff); border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.07); overflow:hidden; }
[data-theme="dark"] .profile-main { background:#1e2130; }
.profile-main-header { padding:20px 24px; border-bottom:1px solid var(--border-color,#f0f0f0); font-size:1.05rem; font-weight:700; color:var(--text-color,#1a1a1a); display:flex; align-items:center; gap:10px; }
[data-theme="dark"] .profile-main-header { border-color:#2c3145; color:#e4e6ef; }
.profile-main-header i { color:#e8001c; }
.profile-main-body { padding:24px; }

.post-row { display:flex; gap:14px; padding:14px 0; border-bottom:1px solid var(--border-color,#f0f0f0); }
[data-theme="dark"] .post-row { border-color:#2c3145; }
.post-row:last-child { border-bottom:none; }
.post-row img { width:80px; height:58px; object-fit:cover; border-radius:6px; flex-shrink:0; }
.post-row-info { flex:1; min-width:0; }
.post-row-cat { font-size:.72rem; font-weight:700; color:#e8001c; margin-bottom:4px; }
.post-row-title { font-size:.9rem; font-weight:700; color:var(--text-color,#1a1a1a); line-height:1.4; }
[data-theme="dark"] .post-row-title { color:#e4e6ef; }
.post-row-title:hover { color:#e8001c; }
.post-row-meta { font-size:.75rem; color:#888; margin-top:4px; }

.comment-row { padding:14px 0; border-bottom:1px solid var(--border-color,#f0f0f0); }
[data-theme="dark"] .comment-row { border-color:#2c3145; }
.comment-row:last-child { border-bottom:none; }
.comment-post-title { font-size:.85rem; font-weight:700; color:#e8001c; margin-bottom:6px; }
.comment-text { font-size:.9rem; color:var(--text-color,#333); line-height:1.6; }
[data-theme="dark"] .comment-text { color:#e4e6ef; }
.comment-status { display:inline-block; font-size:.72rem; font-weight:700; padding:2px 8px; border-radius:10px; margin-left:8px; }
.cs-pending  { background:rgba(255,152,0,.12);  color:#e65100; }
.cs-approved { background:rgba(76,175,80,.12);  color:#2e7d32; }
.cs-rejected { background:rgba(244,67,54,.12);  color:#c62828; }
[data-theme="dark"] .cs-pending  { background:rgba(255,152,0,.2);  color:#ffb74d; }
[data-theme="dark"] .cs-approved { background:rgba(76,175,80,.2);  color:#a5d6a7; }
[data-theme="dark"] .cs-rejected { background:rgba(244,67,54,.2); color:#ef9a9a; }

.empty-state { text-align:center; padding:48px 20px; color:#888; }
.empty-state i { font-size:3rem; margin-bottom:16px; color:#ddd; display:block; }
[data-theme="dark"] .empty-state i { color:#2c3145; }
.ra-form-group { margin-bottom:16px; }
.ra-form-group label { display:block; font-size:.85rem; font-weight:600; color:#555; margin-bottom:5px; }
[data-theme="dark"] .ra-form-group label { color:#9aa0b8; }
.ra-form-group input, .ra-form-group textarea {
    width:100%; padding:10px 14px; border:1.5px solid #ddd; border-radius:8px;
    font-size:.9rem; font-family:'Hind Siliguri',sans-serif; outline:none;
    background:#fff; color:#333; transition:border-color .2s;
}
[data-theme="dark"] .ra-form-group input, [data-theme="dark"] .ra-form-group textarea { background:#13161f; border-color:#2c3145; color:#e4e6ef; }
.ra-form-group input:focus, .ra-form-group textarea:focus { border-color:#e8001c; }
.ra-btn-sm { padding:9px 20px; background:#e8001c; color:#fff; border:none; border-radius:8px; font-size:.9rem; font-weight:700; cursor:pointer; font-family:'Hind Siliguri',sans-serif; transition:background .2s; }
.ra-btn-sm:hover { background:#c20017; }
.alert-msg { padding:10px 14px; border-radius:8px; font-size:.85rem; margin-bottom:16px; }
.alert-ok  { background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; }
.alert-err { background:#fdf2f0; color:#e8001c; border:1px solid #f5b7b1; }
[data-theme="dark"] .alert-ok  { background:#1b2e1d; color:#a5d6a7; border-color:#2e5c31; }
[data-theme="dark"] .alert-err { background:#2a1215; color:#ef9a9a; border-color:#5c1e22; }

.nl-cat-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin:14px 0; }
.nl-cat-item { display:flex; align-items:center; gap:8px; font-size:.88rem; cursor:pointer; }
.nl-cat-item input { accent-color:#e8001c; width:16px; height:16px; }

@media(max-width:768px){
    .profile-wrap { grid-template-columns:1fr; }
    .profile-sidebar { display:flex; gap:0; flex-wrap:wrap; align-items:center; padding:16px; }
    .profile-avatar { width:56px; height:56px; font-size:1.4rem; margin:0 14px 0 0; }
    .profile-nav { display:flex; gap:4px; flex-wrap:wrap; width:100%; margin-top:12px; }
    .profile-nav a { padding:7px 12px; font-size:.82rem; margin-bottom:0; }
    .profile-logout { margin-top:0; padding:7px 12px; font-size:.82rem; }
}
</style>

<div class="container">
<div class="profile-wrap">

    <!-- Sidebar -->
    <div class="profile-sidebar">
        <div class="profile-avatar"><?= mb_strtoupper(mb_substr($reader['name'],0,1)) ?></div>
        <div class="profile-name"><?= htmlspecialchars($reader['name']) ?></div>
        <div class="profile-email"><?= htmlspecialchars($reader['email']) ?></div>
        <nav class="profile-nav">
            <a href="?tab=bookmarks" class="<?= $tab==='bookmarks'?'active':'' ?>"><i class="fas fa-bookmark"></i> বুকমার্ক</a>
            <a href="?tab=history"   class="<?= $tab==='history'  ?'active':'' ?>"><i class="fas fa-history"></i> পড়ার ইতিহাস</a>
            <a href="?tab=comments"  class="<?= $tab==='comments' ?'active':'' ?>"><i class="fas fa-comments"></i> আমার মন্তব্য</a>
            <a href="?tab=newsletter"class="<?= $tab==='newsletter'?'active':'' ?>"><i class="fas fa-envelope"></i> নিউজলেটার</a>
            <a href="?tab=settings"  class="<?= $tab==='settings' ?'active':'' ?>"><i class="fas fa-cog"></i> সেটিংস</a>
        </nav>
        <a href="<?= SITE_URL ?>/reader_logout.php" class="profile-logout"><i class="fas fa-sign-out-alt"></i> লগআউট</a>
    </div>

    <!-- Main -->
    <div class="profile-main">

        <?php if ($tab === 'bookmarks'): ?>
        <div class="profile-main-header"><i class="fas fa-bookmark"></i> সংরক্ষিত সংবাদ</div>
        <div class="profile-main-body">
            <?php if (empty($bookmarks)): ?>
            <div class="empty-state"><i class="fas fa-bookmark"></i>কোনো বুকমার্ক নেই।<br><small>সংবাদ পড়ার সময় বুকমার্ক আইকনে ক্লিক করুন।</small></div>
            <?php else: foreach ($bookmarks as $p): ?>
            <div class="post-row">
                <img src="<?= htmlspecialchars($p['image']) ?>" alt="" loading="lazy">
                <div class="post-row-info">
                    <div class="post-row-cat"><?= htmlspecialchars($p['category_name']) ?></div>
                    <a href="<?= SITE_URL ?>/single.php?slug=<?= $p['slug'] ?>" class="post-row-title"><?= htmlspecialchars($p['title']) ?></a>
                    <div class="post-row-meta"><i class="far fa-clock"></i> <?= timeAgo($p['created_at']) ?></div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <?php elseif ($tab === 'history'): ?>
        <div class="profile-main-header"><i class="fas fa-history"></i> পড়ার ইতিহাস</div>
        <div class="profile-main-body">
            <?php if (empty($history)): ?>
            <div class="empty-state"><i class="fas fa-history"></i>কোনো ইতিহাস নেই।</div>
            <?php else: foreach ($history as $p): ?>
            <div class="post-row">
                <img src="<?= htmlspecialchars($p['image']) ?>" alt="" loading="lazy">
                <div class="post-row-info">
                    <div class="post-row-cat"><?= htmlspecialchars($p['category_name']) ?></div>
                    <a href="<?= SITE_URL ?>/single.php?slug=<?= $p['slug'] ?>" class="post-row-title"><?= htmlspecialchars($p['title']) ?></a>
                    <div class="post-row-meta"><i class="far fa-eye"></i> <?= timeAgo($p['viewed_at']) ?></div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <?php elseif ($tab === 'comments'): ?>
        <div class="profile-main-header"><i class="fas fa-comments"></i> আমার মন্তব্য</div>
        <div class="profile-main-body">
            <?php if (empty($my_comments)): ?>
            <div class="empty-state"><i class="fas fa-comments"></i>কোনো মন্তব্য নেই।</div>
            <?php else: foreach ($my_comments as $c): ?>
            <div class="comment-row">
                <div class="comment-post-title">
                    <a href="<?= SITE_URL ?>/single.php?slug=<?= $c['post_slug'] ?>"><?= htmlspecialchars($c['post_title']) ?></a>
                    <span class="comment-status cs-<?= $c['status'] ?>"><?= $c['status']==='pending'?'অপেক্ষমান':($c['status']==='approved'?'অনুমোদিত':'প্রত্যাখ্যাত') ?></span>
                </div>
                <div class="comment-text"><?= nl2br(htmlspecialchars($c['comment'])) ?></div>
                <div class="post-row-meta" style="margin-top:6px"><i class="far fa-clock"></i> <?= timeAgo($c['created_at']) ?></div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <?php elseif ($tab === 'newsletter'): ?>
        <div class="profile-main-header"><i class="fas fa-envelope"></i> নিউজলেটার সাবস্ক্রিপশন</div>
        <div class="profile-main-body">
            <?php
            $em = $conn->real_escape_string($reader['email']);
            $nlrow = $conn->query("SELECT * FROM newsletter_subscribers WHERE email='$em' LIMIT 1");
            $nl = ($nlrow && $nlrow->num_rows) ? $nlrow->fetch_assoc() : null;
            $saved_cats = $nl ? array_filter(explode(',', $nl['categories'] ?? '')) : [];
            $all_cats = getCategories();
            if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['nl_save'])) {
                $cats = implode(',', array_map('intval', $_POST['nl_cats'] ?? []));
                $nm   = $conn->real_escape_string($reader['name']);
                $rid2 = (int)$reader['id'];
                if ($nl) {
                    $conn->query("UPDATE newsletter_subscribers SET categories='$cats',is_active=1,reader_id=$rid2 WHERE email='$em'");
                } else {
                    $conn->query("INSERT INTO newsletter_subscribers (email,reader_id,name,categories) VALUES ('$em',$rid2,'$nm','$cats')");
                }
                $saved_cats = array_filter(explode(',', $cats));
                $msg = 'নিউজলেটার পছন্দ সংরক্ষিত হয়েছে।';
            }
            if (!empty($msg)): ?><div class="alert-msg alert-ok"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>
            <p style="font-size:.88rem;color:#888;margin-bottom:16px">কোন বিভাগের সংবাদ ইমেইলে পেতে চান তা বেছে নিন:</p>
            <form method="POST">
                <input type="hidden" name="nl_save" value="1">
                <div class="nl-cat-grid">
                <?php foreach ($all_cats as $cat): ?>
                    <label class="nl-cat-item">
                        <input type="checkbox" name="nl_cats[]" value="<?= $cat['id'] ?>" <?= in_array($cat['id'], $saved_cats)?'checked':'' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </label>
                <?php endforeach; ?>
                </div>
                <button type="submit" class="ra-btn-sm"><i class="fas fa-save"></i> সংরক্ষণ করুন</button>
                <?php if ($nl && $nl['is_active']): ?>
                <a href="?tab=newsletter&unsub=1" onclick="return confirm('সাবস্ক্রিপশন বাতিল করবেন?')" style="margin-left:12px;color:#888;font-size:.85rem">আনসাবস্ক্রাইব</a>
                <?php endif; ?>
            </form>
            <?php
            if (isset($_GET['unsub'])) {
                $conn->query("UPDATE newsletter_subscribers SET is_active=0 WHERE email='$em'");
                echo '<div class="alert-msg alert-ok" style="margin-top:16px">সাবস্ক্রিপশন বাতিল হয়েছে।</div>';
            }
            ?>
        </div>

        <?php elseif ($tab === 'settings'): ?>
        <div class="profile-main-header"><i class="fas fa-cog"></i> সেটিংস</div>
        <div class="profile-main-body">
            <?php if ($msg): ?><div class="alert-msg alert-ok"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>
            <?php if ($err): ?><div class="alert-msg alert-err"><i class="fas fa-exclamation-circle"></i> <?= $err ?></div><?php endif; ?>
            <h4 style="font-size:.95rem;font-weight:700;margin-bottom:16px;color:var(--text-color,#222)">প্রোফাইল সম্পাদনা</h4>
            <form method="POST" style="max-width:420px">
                <input type="hidden" name="action" value="update_profile">
                <div class="ra-form-group"><label>পুরো নাম</label><input type="text" name="name" value="<?= htmlspecialchars($reader['name']) ?>" required></div>
                <div class="ra-form-group"><label>ইমেইল</label><input type="email" value="<?= htmlspecialchars($reader['email']) ?>" disabled style="opacity:.6;cursor:not-allowed"></div>
                <div class="ra-form-group"><label>পরিচিতি (ঐচ্ছিক)</label><textarea name="bio" rows="3" placeholder="নিজের সম্পর্কে কিছু লিখুন..."><?= htmlspecialchars($reader['bio'] ?? '') ?></textarea></div>
                <button type="submit" class="ra-btn-sm"><i class="fas fa-save"></i> আপডেট করুন</button>
            </form>

            <hr style="border:none;border-top:1px solid var(--border-color,#f0f0f0);margin:28px 0">
            <h4 style="font-size:.95rem;font-weight:700;margin-bottom:16px;color:var(--text-color,#222)">পাসওয়ার্ড পরিবর্তন</h4>
            <form method="POST" style="max-width:420px">
                <input type="hidden" name="action" value="change_password">
                <div class="ra-form-group"><label>বর্তমান পাসওয়ার্ড</label><input type="password" name="cur_password" required></div>
                <div class="ra-form-group"><label>নতুন পাসওয়ার্ড</label><input type="password" name="new_password" minlength="6" required></div>
                <button type="submit" class="ra-btn-sm"><i class="fas fa-key"></i> পরিবর্তন করুন</button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</div>
</div>

<?php require_once 'includes/footer.php'; ?>

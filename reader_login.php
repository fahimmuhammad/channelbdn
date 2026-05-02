<?php
require_once 'reader_auth.php';
require_once 'includes/functions.php';

if (readerIsLoggedIn()) { header('Location: ' . SITE_URL . '/reader_profile.php'); exit; }

$tab    = $_GET['tab'] ?? 'login';
$redirect = $_GET['redirect'] ?? SITE_URL . '/';
$error  = '';
$success= '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tab = $_POST['tab'] ?? 'login';
    if ($tab === 'login') {
        $res = readerLogin($_POST['email'] ?? '', $_POST['password'] ?? '');
        if (isset($res['success'])) { header('Location: ' . $redirect); exit; }
        $error = $res['error'];
    } else {
        $res = readerRegister($_POST['name'] ?? '', $_POST['email'] ?? '', $_POST['password'] ?? '');
        if (isset($res['success'])) { header('Location: ' . $redirect); exit; }
        $error = $res['error'];
    }
}

$page_title = $tab === 'register' ? 'নিবন্ধন করুন' : 'লগইন করুন';
require_once 'includes/header.php';
?>
<style>
.reader-auth-wrap { max-width:440px; margin:40px auto 60px; padding:0 16px; }
.reader-auth-card { background:var(--card-bg,#fff); border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,.1); overflow:hidden; }
[data-theme="dark"] .reader-auth-card { background:#1e2130; box-shadow:0 4px 24px rgba(0,0,0,.4); }
.reader-auth-tabs { display:flex; }
.rat { flex:1; padding:16px; text-align:center; font-weight:700; font-size:.95rem; cursor:pointer;
       border:none; background:var(--bg-alt,#f8f8f8); color:#888; transition:all .2s;
       font-family:'Hind Siliguri',sans-serif; }
[data-theme="dark"] .rat { background:#252836; color:#9aa0b8; }
.rat.active { background:#e8001c; color:#fff; }
.reader-auth-body { padding:32px 28px; }
.ra-title { font-size:1.2rem; font-weight:700; color:var(--text-color,#1a1a1a); margin-bottom:6px; }
[data-theme="dark"] .ra-title { color:#e4e6ef; }
.ra-sub { font-size:.85rem; color:#888; margin-bottom:24px; }
.ra-group { margin-bottom:16px; }
.ra-group label { display:block; font-size:.82rem; font-weight:600; color:#555; margin-bottom:5px; }
[data-theme="dark"] .ra-group label { color:#9aa0b8; }
.ra-group input { width:100%; padding:11px 14px; border:1.5px solid #ddd; border-radius:8px;
                  font-size:.95rem; font-family:'Hind Siliguri',sans-serif; outline:none;
                  background:#fff; color:#333; transition:border-color .2s; }
[data-theme="dark"] .ra-group input { background:#13161f; border-color:#2c3145; color:#e4e6ef; }
.ra-group input:focus { border-color:#e8001c; }
.ra-btn { width:100%; padding:12px; background:#e8001c; color:#fff; border:none; border-radius:8px;
          font-size:1rem; font-weight:700; cursor:pointer; font-family:'Hind Siliguri',sans-serif;
          transition:background .2s; margin-top:4px; }
.ra-btn:hover { background:#c20017; }
.ra-error { background:#fdf2f0; border:1px solid #f5b7b1; color:#e8001c; padding:10px 14px;
            border-radius:8px; font-size:.85rem; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
[data-theme="dark"] .ra-error { background:#2a1215; border-color:#5c1e22; }
.ra-switch { text-align:center; margin-top:18px; font-size:.85rem; color:#888; }
.ra-switch a { color:#e8001c; font-weight:600; }
</style>

<div class="container">
<div class="reader-auth-wrap">
    <div class="reader-auth-card">
        <div class="reader-auth-tabs">
            <button class="rat <?= $tab==='login'?'active':'' ?>" onclick="switchTab('login')">লগইন</button>
            <button class="rat <?= $tab==='register'?'active':'' ?>" onclick="switchTab('register')">নিবন্ধন</button>
        </div>

        <!-- Login -->
        <div class="reader-auth-body" id="tabLogin" style="<?= $tab==='register'?'display:none':'' ?>">
            <div class="ra-title"><i class="fas fa-user-circle" style="color:#e8001c"></i> লগইন করুন</div>
            <div class="ra-sub">আপনার অ্যাকাউন্টে প্রবেশ করুন</div>
            <?php if ($error && $tab==='login'): ?><div class="ra-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
            <form method="POST">
                <input type="hidden" name="tab" value="login">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                <div class="ra-group"><label>ইমেইল</label><input type="email" name="email" placeholder="your@email.com" required autofocus></div>
                <div class="ra-group"><label>পাসওয়ার্ড</label><input type="password" name="password" placeholder="••••••••" required></div>
                <button type="submit" class="ra-btn"><i class="fas fa-sign-in-alt"></i> লগইন</button>
            </form>
            <div class="ra-switch">অ্যাকাউন্ট নেই? <a href="?tab=register&redirect=<?= urlencode($redirect) ?>" onclick="switchTab('register');return false">নিবন্ধন করুন</a></div>
        </div>

        <!-- Register -->
        <div class="reader-auth-body" id="tabRegister" style="<?= $tab==='login'?'display:none':'' ?>">
            <div class="ra-title"><i class="fas fa-user-plus" style="color:#e8001c"></i> নিবন্ধন করুন</div>
            <div class="ra-sub">বিনামূল্যে অ্যাকাউন্ট তৈরি করুন</div>
            <?php if ($error && $tab==='register'): ?><div class="ra-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
            <form method="POST">
                <input type="hidden" name="tab" value="register">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                <div class="ra-group"><label>পুরো নাম</label><input type="text" name="name" placeholder="আপনার নাম" required></div>
                <div class="ra-group"><label>ইমেইল</label><input type="email" name="email" placeholder="your@email.com" required></div>
                <div class="ra-group"><label>পাসওয়ার্ড <span style="color:#888;font-weight:400">(কমপক্ষে ৬ অক্ষর)</span></label><input type="password" name="password" placeholder="••••••••" required minlength="6"></div>
                <button type="submit" class="ra-btn"><i class="fas fa-user-plus"></i> নিবন্ধন করুন</button>
            </form>
            <div class="ra-switch">ইতিমধ্যে অ্যাকাউন্ট আছে? <a href="?tab=login&redirect=<?= urlencode($redirect) ?>" onclick="switchTab('login');return false">লগইন করুন</a></div>
        </div>
    </div>
</div>
</div>

<script>
function switchTab(t){
    document.getElementById('tabLogin').style.display    = t==='login'    ? '' : 'none';
    document.getElementById('tabRegister').style.display = t==='register' ? '' : 'none';
    document.querySelectorAll('.rat').forEach(function(b,i){
        b.classList.toggle('active', (i===0&&t==='login')||(i===1&&t==='register'));
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>

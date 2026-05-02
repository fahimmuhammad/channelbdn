<?php
require_once 'auth.php';
require_once __DIR__ . '/includes/lang.php';
if (isLoggedIn()) { header('Location: ' . SITE_URL . '/admin/'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $result = adminLogin($username, $password);
    if ($result === true) {
        header('Location: ' . SITE_URL . '/admin/');
        exit;
    } elseif ($result === 'inactive') {
        $error = __('login_restricted');
    } else {
        $error = __('login_error');
    }
}
$_cur_lang = $_SESSION['admin_lang'] ?? 'bn';
?>
<!DOCTYPE html>
<html lang="<?= $_cur_lang === 'en' ? 'en' : 'bn' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('admin_panel') ?> | <?= SITE_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Hind Siliguri', sans-serif; background: linear-gradient(135deg, #2c3e50, #e8001c); min-height: 100vh; display: flex; align-items: center; justify-content: center; transition: background 0.3s; }
        .login-box { background: #fff; border-radius: 10px; padding: 42px 38px; width: 100%; max-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative; transition: background 0.3s, border-color 0.3s; }
        .login-logo { text-align: center; margin-bottom: 28px; }
        .login-logo h1 { font-size: 32px; font-weight: 800; color: #e8001c; }
        .login-logo p { font-size: 13px; color: #888; margin-top: 4px; }
        .login-box h2 { font-size: 20px; font-weight: 700; color: #2c3e50; margin-bottom: 22px; text-align: center; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px; }
        .form-group input { width: 100%; padding: 11px 14px; border: 1.5px solid #ddd; border-radius: 6px; font-family: 'Hind Siliguri', sans-serif; font-size: 14px; outline: none; transition: border-color 0.2s, background 0.2s, color 0.2s; background: #fff; color: #333; }
        .form-group input:focus { border-color: #e8001c; }
        .btn-login { width: 100%; padding: 12px; background: #e8001c; color: #fff; border: none; border-radius: 6px; font-family: 'Hind Siliguri', sans-serif; font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        .btn-login:hover { background: #c20017; }
        .error-msg { background: #fdf2f0; border: 1px solid #f5b7b1; color: #e8001c; padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 16px; }
        .back-link { text-align: center; margin-top: 16px; font-size: 13px; color: #888; }
        .back-link a { color: #e8001c; text-decoration: none; }
        .login-lang { text-align:right; margin-bottom:16px; }
        .login-lang a { font-size:12px; font-weight:700; color:#888; padding:2px 7px; border:1px solid #ddd; border-radius:4px; margin-left:4px; text-decoration:none; }
        .login-lang a.active { background:#e8001c; color:#fff; border-color:#e8001c; }

        /* Dark mode toggle button */
        .dm-toggle { position: absolute; top: 16px; right: 16px; background: none; border: 1.5px solid #ddd; border-radius: 8px; width: 34px; height: 34px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 15px; color: #888; transition: all 0.2s; }
        .dm-toggle:hover { border-color: #e8001c; color: #e8001c; }

        /* Dark mode overrides */
        body.dark { background: linear-gradient(135deg, #0d1117, #1a1d2e); }
        body.dark .login-box { background: #1e2130; border: 1px solid #2c3145; box-shadow: 0 20px 60px rgba(0,0,0,0.6); }
        body.dark .login-logo p { color: #9aa0b8; }
        body.dark .login-box h2 { color: #e4e6ef; }
        body.dark .form-group label { color: #9aa0b8; }
        body.dark .form-group input { background: #13161f; border-color: #2c3145; color: #e4e6ef; }
        body.dark .form-group input::placeholder { color: #636880; }
        body.dark .form-group input:focus { border-color: #e8001c; }
        body.dark .back-link { color: #636880; }
        body.dark .login-lang a { color: #9aa0b8; border-color: #2c3145; }
        body.dark .login-lang a.active { background: #e8001c; color: #fff; border-color: #e8001c; }
        body.dark .dm-toggle { border-color: #2c3145; color: #9aa0b8; }
        body.dark .dm-toggle:hover { border-color: #e8001c; color: #e8001c; }
        body.dark .error-msg { background: #2a1215; border-color: #5c1e22; }
    </style>
    <script>(function(){ if(localStorage.getItem('adminDark')==='1') document.documentElement.classList.add('pre-dark'); })();</script>
</head>
<body>
    <div class="login-box">
        <button class="dm-toggle" id="dmToggle" title="ডার্ক মোড"><i class="fas fa-moon" id="dmIcon"></i></button>
        <div class="login-logo">
            <h1><?= SITE_NAME ?></h1>
            <p><?= __('admin_panel') ?></p>
        </div>
        <div class="login-lang">
            <a href="setlang.php?lang=bn" class="<?= $_cur_lang==='bn'?'active':'' ?>">বাং</a>
            <a href="setlang.php?lang=en" class="<?= $_cur_lang==='en'?'active':'' ?>">EN</a>
        </div>
        <h2><i class="fas fa-lock"></i> <?= __('login_heading') ?></h2>
        <?php if ($error): ?><div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label><?= __('username') ?></label>
                <input type="text" name="username" placeholder="admin" required autofocus>
            </div>
            <div class="form-group">
                <label><?= __('password') ?></label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login"><?= __('login_btn') ?> <i class="fas fa-arrow-right"></i></button>
        </form>
        <div class="back-link"><a href="<?= SITE_URL ?>/"><?= __('back_to_site') ?></a></div>
    </div>
<script>
(function(){
    var body = document.body;
    var btn  = document.getElementById('dmToggle');
    var ico  = document.getElementById('dmIcon');
    function applyDark(dark){
        body.classList.toggle('dark', dark);
        ico.className = dark ? 'fas fa-sun' : 'fas fa-moon';
        btn.title = dark ? 'লাইট মোড' : 'ডার্ক মোড';
    }
    applyDark(localStorage.getItem('adminDark') === '1');
    btn.addEventListener('click', function(){
        var dark = !body.classList.contains('dark');
        localStorage.setItem('adminDark', dark ? '1' : '0');
        applyDark(dark);
    });
})();
</script>
</body>
</html>

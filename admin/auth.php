<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once __DIR__ . '/includes/lang.php';

// Permission matrix: role => allowed actions
$ROLE_PERMISSIONS = [
    'admin'      => ['*'],
    'editor'     => ['posts','add_post','edit_post','delete_post','categories','gallery','videos','homepage','polls'],
    'reporter'   => ['posts','add_post','edit_own_post'],
    'moderator'  => ['posts','edit_post','categories'],
    'ad_manager' => ['posts','ads'],
];

function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

function getUserRole() {
    return $_SESSION['admin_role'] ?? '';
}

function hasPermission($action) {
    global $ROLE_PERMISSIONS;
    $role = getUserRole();
    if (!$role || !isset($ROLE_PERMISSIONS[$role])) return false;
    $perms = $ROLE_PERMISSIONS[$role];
    return in_array('*', $perms) || in_array($action, $perms);
}

function requirePermission($action) {
    requireLogin();
    if (!hasPermission($action)) {
        http_response_code(403);
        include dirname(__FILE__) . '/includes/admin_header.php';
        echo '<div class="admin-main"><div class="alert alert-danger"><i class="fas fa-ban"></i> আপনার এই পৃষ্ঠা দেখার অনুমতি নেই।</div></div>';
        include dirname(__FILE__) . '/includes/admin_footer.php';
        exit;
    }
}

function logActivity($action, $details = '') {
    global $conn;
    if (!isLoggedIn()) return;
    $user_id  = (int)$_SESSION['admin_id'];
    $username = $conn->real_escape_string($_SESSION['admin_user'] ?? '');
    $role     = $conn->real_escape_string($_SESSION['admin_role'] ?? '');
    $action   = $conn->real_escape_string($action);
    $details  = $conn->real_escape_string($details);
    $ip       = $conn->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '');
    $conn->query("INSERT INTO activity_log (user_id, username, role, action, details, ip_address)
                  VALUES ($user_id, '$username', '$role', '$action', '$details', '$ip')");
}

function adminLogin($username, $password) {
    global $conn;
    $u = $conn->real_escape_string(trim($username));
    $result = $conn->query("SELECT * FROM users WHERE username='$u' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            if (!$user['is_active']) {
                return 'inactive';
            }
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_user'] = $user['username'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['admin_name'] = $user['full_name'] ?: $user['username'];
            $conn->query("UPDATE users SET last_login=NOW() WHERE id=" . (int)$user['id']);
            logActivity('login', 'লগইন সফল');
            return true;
        }
    }
    return false;
}

function adminLogout() {
    if (isLoggedIn()) {
        logActivity('logout', 'লগআউট');
    }
    session_destroy();
    header('Location: ' . SITE_URL . '/admin/login.php');
    exit;
}

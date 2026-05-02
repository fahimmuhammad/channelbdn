<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$_lang_code = $_SESSION['admin_lang'] ?? 'bn';
if (!in_array($_lang_code, ['bn', 'en'])) $_lang_code = 'bn';
$_lang = require dirname(__DIR__) . '/lang/' . $_lang_code . '.php';

function __($key, ...$args) {
    global $_lang;
    $str = $_lang[$key] ?? $key;
    return $args ? vsprintf($str, $args) : $str;
}
function _e($key, ...$args) { echo __($key, ...$args); }

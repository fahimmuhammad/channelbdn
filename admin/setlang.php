<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
$lang = $_GET['lang'] ?? 'bn';
$_SESSION['admin_lang'] = in_array($lang, ['bn', 'en']) ? $lang : 'bn';
$back = $_SERVER['HTTP_REFERER'] ?? SITE_URL . '/admin/';
header('Location: ' . $back);
exit;

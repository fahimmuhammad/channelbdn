<?php
require_once 'reader_auth.php';
readerLogout();
$redirect = $_GET['redirect'] ?? SITE_URL . '/';
header('Location: ' . $redirect);
exit;

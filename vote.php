<?php
require_once 'config.php';
require_once 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $poll_id = (int)($_POST['poll_id'] ?? 0);
    $option  = (int)($_POST['option']  ?? 0);
    if ($poll_id && $option >= 1 && $option <= 3) {
        votePoll($poll_id, $option);
    }
}
header('Location: ' . SITE_URL . '/');
exit;

<?php
require_once 'config.php';
require_once 'includes/functions.php';

$slug = trim($_GET['slug'] ?? '');
$post = $slug ? getPostBySlug($slug) : null;

$site_name = getSetting('site_name') ?: SITE_NAME;
$title     = $post ? $post['title'] : $site_name;
$img_url   = ($post && $post['image']) ? $post['image'] : '';

// Canvas 1200x630
$w = 1200; $h = 630;
$img = imagecreatetruecolor($w, $h);

$bg_dark  = imagecolorallocate($img, 15, 17, 23);
$red      = imagecolorallocate($img, 232, 0, 28);
$white    = imagecolorallocate($img, 255, 255, 255);
$overlay  = imagecolorallocatealpha($img, 0, 0, 0, 50);

// Try to load featured image as background
$bg_loaded = false;
if ($img_url) {
    $ext = strtolower(pathinfo(parse_url($img_url, PHP_URL_PATH), PATHINFO_EXTENSION));
    $src = null;
    if (in_array($ext, ['jpg','jpeg'])) $src = @imagecreatefromjpeg($img_url);
    elseif ($ext === 'png')             $src = @imagecreatefrompng($img_url);
    elseif ($ext === 'webp')            $src = @imagecreatefromwebp($img_url);
    if ($src) {
        imagecopyresampled($img, $src, 0, 0, 0, 0, $w, $h, imagesx($src), imagesy($src));
        imagedestroy($src);
        $bg_loaded = true;
    }
}

if (!$bg_loaded) {
    // Gradient-style dark background
    for ($y = 0; $y < $h; $y++) {
        $c = imagecolorallocate($img, 15, 17 + (int)($y * 0.06), 40 + (int)($y * 0.04));
        imagefilledrectangle($img, 0, $y, $w, $y, $c);
    }
}

// Dark overlay over background image
imagefilledrectangle($img, 0, 0, $w, $h, imagecolorallocatealpha($img, 0, 0, 0, 70));

// Red accent strip at bottom
imagefilledrectangle($img, 0, $h - 8, $w, $h, $red);

// Red accent bar top-left for branding
imagefilledrectangle($img, 0, 0, 6, $h, $red);

// Site name box
imagefilledrectangle($img, 30, 30, 30 + 220, 30 + 44, $red);

// Try to render text with TTF font
$font_path = __DIR__ . '/assets/fonts/NotoSansBengali-Bold.ttf';
$font_exists = file_exists($font_path);

if ($font_exists) {
    // Site name
    imagettftext($img, 18, 0, 40, 62, $white, $font_path, $site_name);
    // Title (word-wrap at ~38 chars per line, max 3 lines)
    $lines = [];
    $words = explode(' ', $title);
    $line  = '';
    foreach ($words as $word) {
        if (mb_strlen($line . ' ' . $word) > 38) { $lines[] = $line; $line = $word; }
        else { $line = $line ? $line . ' ' . $word : $word; }
    }
    if ($line) $lines[] = $line;
    $lines = array_slice($lines, 0, 3);
    $y_start = 200;
    foreach ($lines as $i => $ln) {
        imagettftext($img, 38, 0, 40, $y_start + ($i * 60), $white, $font_path, $ln);
    }
} else {
    // Fallback ASCII rendering
    imagestring($img, 5, 40, 35, $site_name, $white);
    $ascii_title = mb_substr($title, 0, 60);
    imagestring($img, 5, 40, 200, $ascii_title, $white);
}

// URL watermark at bottom-right
$url = parse_url(SITE_URL, PHP_URL_HOST);
imagestring($img, 3, $w - 200, $h - 30, $url, imagecolorallocate($img, 170, 170, 170));

header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=86400');
imagejpeg($img, null, 88);
imagedestroy($img);

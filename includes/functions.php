<?php
require_once dirname(__DIR__) . '/config.php';

// Ensure scheduled_at column exists (runs once, safe to repeat)
global $conn;
$conn->query("ALTER TABLE posts ADD COLUMN IF NOT EXISTS scheduled_at DATETIME NULL DEFAULT NULL");
// Auto-publish scheduled posts whose time has arrived
$conn->query("UPDATE posts SET status='published' WHERE status='scheduled' AND scheduled_at IS NOT NULL AND scheduled_at <= NOW()");

function handleImageUpload($file) {
    $allowed   = ['image/jpeg','image/png','image/webp'];
    $max_bytes = 5 * 1024 * 1024;
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if (!in_array($file['type'], $allowed)) return false;
    if ($file['size'] > $max_bytes) return false;

    $ext     = pathinfo($file['name'], PATHINFO_EXTENSION);
    $ext     = strtolower($ext === 'jpeg' ? 'jpg' : $ext);
    $name    = uniqid('img_', true) . '.' . $ext;
    $dir     = dirname(__DIR__) . '/assets/uploads/';
    $dest    = $dir . $name;

    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $dest)) return false;
    return SITE_URL . '/assets/uploads/' . $name;
}

function getPosts($limit=10,$category_id=null,$featured=null,$breaking=null,$offset=0){
    global $conn;
    $sql="SELECT p.*,c.name as category_name,c.slug as category_slug FROM posts p JOIN categories c ON p.category_id=c.id WHERE p.status='published'";
    if($category_id) $sql.=" AND p.category_id=$category_id";
    if($featured!==null) $sql.=" AND p.is_featured=$featured";
    if($breaking!==null) $sql.=" AND p.is_breaking=$breaking";
    $sql.=" ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";
    $r=$conn->query($sql); return $r?$r->fetch_all(MYSQLI_ASSOC):[];
}

function getPostBySlug($slug){
    global $conn; $slug=$conn->real_escape_string($slug);
    $r=$conn->query("SELECT p.*,c.name as category_name,c.slug as category_slug FROM posts p JOIN categories c ON p.category_id=c.id WHERE p.slug='$slug' AND p.status='published'");
    return $r&&$r->num_rows>0?$r->fetch_assoc():null;
}

function getPostsByCategory($category_slug,$limit=12,$offset=0){
    global $conn; $slug=$conn->real_escape_string($category_slug);
    $r=$conn->query("SELECT p.*,c.name as category_name,c.slug as category_slug FROM posts p JOIN categories c ON p.category_id=c.id WHERE c.slug='$slug' AND p.status='published' ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset");
    return $r?$r->fetch_all(MYSQLI_ASSOC):[];
}

function getCategories(){
    global $conn;
    $r=$conn->query("SELECT * FROM categories ORDER BY sort_order ASC");
    return $r?$r->fetch_all(MYSQLI_ASSOC):[];
}

function getCategoryBySlug($slug){
    global $conn; $slug=$conn->real_escape_string($slug);
    $r=$conn->query("SELECT * FROM categories WHERE slug='$slug'");
    return $r&&$r->num_rows>0?$r->fetch_assoc():null;
}

function getPopularPosts($limit=5){
    global $conn;
    $r=$conn->query("SELECT p.*,c.name as category_name,c.slug as category_slug FROM posts p JOIN categories c ON p.category_id=c.id WHERE p.status='published' ORDER BY p.views DESC LIMIT $limit");
    return $r?$r->fetch_all(MYSQLI_ASSOC):[];
}

function getBreakingNews($limit=5){
    global $conn;
    $r=$conn->query("SELECT * FROM posts WHERE is_breaking=1 AND status='published' ORDER BY created_at DESC LIMIT $limit");
    return $r?$r->fetch_all(MYSQLI_ASSOC):[];
}

function getCuratedPosts($limit=4){
    global $conn;
    $r=$conn->query("SELECT p.*,c.name as category_name,c.slug as category_slug FROM posts p JOIN categories c ON p.category_id=c.id WHERE p.is_curated=1 AND p.status='published' ORDER BY p.created_at DESC LIMIT $limit");
    if(!$r){
        // is_curated column missing — fall back to featured posts
        $r=$conn->query("SELECT p.*,c.name as category_name,c.slug as category_slug FROM posts p JOIN categories c ON p.category_id=c.id WHERE p.is_featured=1 AND p.status='published' ORDER BY p.created_at DESC LIMIT $limit");
    }
    return $r?$r->fetch_all(MYSQLI_ASSOC):[];
}

function incrementViews($post_id){
    global $conn; $conn->query("UPDATE posts SET views=views+1 WHERE id=$post_id");
}

function getRelatedPosts($category_id,$post_id,$limit=4){
    global $conn;
    $r=$conn->query("SELECT p.*,c.name as category_name,c.slug as category_slug FROM posts p JOIN categories c ON p.category_id=c.id WHERE p.category_id=$category_id AND p.id!=$post_id AND p.status='published' ORDER BY p.created_at DESC LIMIT $limit");
    return $r?$r->fetch_all(MYSQLI_ASSOC):[];
}

function getSetting($key){
    global $conn; $key=$conn->real_escape_string($key);
    $r=$conn->query("SELECT setting_value FROM settings WHERE setting_key='$key'");
    if($r&&$r->num_rows>0){$row=$r->fetch_assoc();return $row['setting_value'];}
    return '';
}

function getAd($position){
    global $conn; $pos=$conn->real_escape_string($position);
    $r=$conn->query("SELECT * FROM ads WHERE position='$pos' AND is_active=1 ORDER BY RAND() LIMIT 1");
    return $r&&$r->num_rows>0?$r->fetch_assoc():null;
}

function getVideos($limit=5){
    global $conn;
    $r=$conn->query("SELECT * FROM videos WHERE is_active=1 ORDER BY created_at DESC LIMIT $limit");
    return $r?$r->fetch_all(MYSQLI_ASSOC):[];
}

function getGalleryPhotos($limit=5){
    global $conn;
    $r=$conn->query("SELECT * FROM photo_gallery WHERE is_active=1 ORDER BY sort_order ASC,created_at DESC LIMIT $limit");
    return $r?$r->fetch_all(MYSQLI_ASSOC):[];
}

function getActivePoll(){
    global $conn;
    $r=$conn->query("SELECT * FROM polls WHERE is_active=1 ORDER BY created_at DESC LIMIT 1");
    return $r&&$r->num_rows>0?$r->fetch_assoc():null;
}

function votePoll($poll_id,$option){
    global $conn;
    $poll_id=(int)$poll_id; $option=(int)$option;
    if($option<1||$option>3) return false;
    return $conn->query("UPDATE polls SET votes$option=votes$option+1 WHERE id=$poll_id");
}

function getHomepageSections(){
    global $conn;
    $r=$conn->query("SELECT * FROM homepage_sections ORDER BY sort_order ASC");
    if(!$r) return [];
    $sections=[];
    while($row=$r->fetch_assoc()) $sections[$row['section_key']]=$row;
    return $sections;
}

function isSectionActive($sections,$key){
    if(empty($sections)) return true;
    return !isset($sections[$key])||$sections[$key]['is_active']==1;
}

function timeAgo($datetime){
    $now=new DateTime(); $ago=new DateTime($datetime); $diff=$now->diff($ago);
    if($diff->y>0) return $diff->y.' বছর আগে';
    if($diff->m>0) return $diff->m.' মাস আগে';
    if($diff->d>0) return $diff->d.' দিন আগে';
    if($diff->h>0) return $diff->h.' ঘণ্টা আগে';
    if($diff->i>0) return $diff->i.' মিনিট আগে';
    return 'এইমাত্র';
}

function formatDate($datetime){
    $months=['জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
    $days=['রোববার','সোমবার','মঙ্গলবার','বুধবার','বৃহস্পতিবার','শুক্রবার','শনিবার'];
    $d=new DateTime($datetime);
    return $days[$d->format('w')].', '.$d->format('j').' '.$months[$d->format('n')-1].' '.$d->format('Y');
}

function getCurrentDateEnglish(){
    $d = new DateTime();
    return $d->format('l, d F Y');
}

function getCurrentDateBangabda() {
    $now   = new DateTime();
    $gy    = (int)$now->format('Y');
    $gm    = (int)$now->format('n');
    $gd    = (int)$now->format('j');
    $dow   = (int)$now->format('w');

    $days_bn   = ['রোববার','সোমবার','মঙ্গলবার','বুধবার','বৃহস্পতিবার','শুক্রবার','শনিবার'];
    $months_bn = ['বৈশাখ','জ্যৈষ্ঠ','আষাঢ়','শ্রাবণ','ভাদ্র','আশ্বিন','কার্তিক','অগ্রহায়ণ','পৌষ','মাঘ','ফাল্গুন','চৈত্র'];

    // Days in each Bengali month (Bangladesh National Calendar)
    // Falgun = 29 normally, 30 in leap year
    $ref_year  = ($gm > 4 || ($gm == 4 && $gd >= 14)) ? $gy : $gy - 1;
    $bn_year   = $ref_year - 593;
    $is_leap   = ($ref_year % 4 == 0 && $ref_year % 100 != 0) || $ref_year % 400 == 0;
    $month_days = [31,31,31,31,31,30,30,30,30,30, $is_leap ? 30 : 29, 30];

    // Days elapsed since Baishakh 1 (April 14) of ref_year
    $start = new DateTime($ref_year . '-04-14');
    $curr  = new DateTime($now->format('Y-m-d'));
    $elapsed = (int)$start->diff($curr)->days;

    $mi = 0;
    while ($mi < 11 && $elapsed >= $month_days[$mi]) {
        $elapsed -= $month_days[$mi];
        $mi++;
    }
    $bn_day = $elapsed + 1;

    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $tobn = function($n) use ($bn) {
        return preg_replace_callback('/\d/', fn($m) => $bn[$m[0]], (string)$n);
    };

    return $days_bn[$dow] . ', ' . $tobn($bn_day) . ' ' . $months_bn[$mi] . ' ' . $tobn($bn_year);
}

function getCurrentDateBengali(){
    $months=['জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
    $days=['রোববার','সোমবার','মঙ্গলবার','বুধবার','বৃহস্পতিবার','শুক্রবার','শনিবার'];
    $bn=['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $tobn=function($n) use ($bn){ return preg_replace_callback('/\d/',fn($m)=>$bn[$m[0]],(string)$n); };
    $d=new DateTime();
    return $days[$d->format('w')].', '.$tobn($d->format('j')).' '.$months[$d->format('n')-1].' '.$tobn($d->format('Y'));
}

function getTotalPostsByCategory($category_slug){
    global $conn; $slug=$conn->real_escape_string($category_slug);
    $r=$conn->query("SELECT COUNT(*) as total FROM posts p JOIN categories c ON p.category_id=c.id WHERE c.slug='$slug' AND p.status='published'");
    $row=$r->fetch_assoc(); return $row['total'];
}

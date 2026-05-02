<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'channelbdn');
define('SITE_URL', 'http://localhost/channelbdn');
define('SITE_NAME', 'চ্যানেল বিডিএন');
define('SITE_TAGLINE', 'সত্যের সন্ধানে সর্বদা');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

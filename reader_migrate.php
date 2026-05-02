<?php
/* Run once: http://localhost/channelbdn/reader_migrate.php  */
require_once 'config.php';
global $conn;

$tables = [
"CREATE TABLE IF NOT EXISTS readers (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    avatar     VARCHAR(255) DEFAULT NULL,
    bio        TEXT         DEFAULT NULL,
    is_active  TINYINT      NOT NULL DEFAULT 1,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

"CREATE TABLE IF NOT EXISTS reader_bookmarks (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    reader_id  INT      NOT NULL,
    post_id    INT      NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_bm (reader_id, post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

"CREATE TABLE IF NOT EXISTS reader_comments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    reader_id  INT      NOT NULL,
    post_id    INT      NOT NULL,
    comment    TEXT     NOT NULL,
    status     ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_post (post_id),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

"CREATE TABLE IF NOT EXISTS reader_reactions (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    reader_id   INT         NOT NULL,
    post_id     INT         NOT NULL,
    reaction    VARCHAR(20) NOT NULL,
    created_at  DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_react (reader_id, post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

"CREATE TABLE IF NOT EXISTS reader_history (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    reader_id  INT      NOT NULL,
    post_id    INT      NOT NULL,
    viewed_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_hist (reader_id, post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

"CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(150) NOT NULL UNIQUE,
    reader_id  INT          DEFAULT NULL,
    name       VARCHAR(100) DEFAULT NULL,
    categories TEXT         DEFAULT NULL,
    is_active  TINYINT      NOT NULL DEFAULT 1,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

$ok = 0; $fail = 0;
foreach ($tables as $sql) {
    if ($conn->query($sql)) { $ok++; }
    else { echo "<p style='color:red'>Error: ".$conn->error."</p>"; $fail++; }
}
echo "<p style='color:green;font-family:sans-serif'>Done — $ok tables created, $fail failed. <a href='/channelbdn/'>Go to site</a></p>";

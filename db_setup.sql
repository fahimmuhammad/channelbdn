CREATE DATABASE IF NOT EXISTS channelbdn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE channelbdn;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    parent_id INT DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title TEXT NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT,
    excerpt TEXT,
    category_id INT NOT NULL,
    author VARCHAR(100) DEFAULT 'নিজস্ব প্রতিবেদক',
    image VARCHAR(255),
    status ENUM('published','draft') DEFAULT 'published',
    views INT DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    is_breaking TINYINT(1) DEFAULT 0,
    is_top TINYINT(1) DEFAULT 0,
    is_curated TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('admin','editor','reporter','moderator','ad_manager') DEFAULT 'reporter',
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(50),
    role VARCHAR(20),
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS polls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    option1 VARCHAR(255),
    option2 VARCHAR(255),
    option3 VARCHAR(255),
    votes1 INT DEFAULT 0,
    votes2 INT DEFAULT 0,
    votes3 INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS photo_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    caption TEXT,
    image_url VARCHAR(500) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    youtube_url VARCHAR(500) NOT NULL,
    thumbnail VARCHAR(500),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    position VARCHAR(50) NOT NULL,
    image_url VARCHAR(500),
    link_url VARCHAR(500) DEFAULT '#',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS homepage_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(50) NOT NULL UNIQUE,
    section_name VARCHAR(100) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO categories (name, slug, sort_order) VALUES
('জাতীয়', 'national', 1),
('রাজনীতি', 'politics', 2),
('আন্তর্জাতিক', 'international', 3),
('অর্থনীতি', 'economy', 4),
('খেলাধুলা', 'sports', 5),
('বিনোদন', 'entertainment', 6),
('প্রযুক্তি', 'technology', 7),
('শিক্ষা', 'education', 8),
('স্বাস্থ্য', 'health', 9),
('আইন ও আদালত', 'law-court', 10),
('লাইফস্টাইল', 'lifestyle', 11),
('মতামত', 'opinion', 12);

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('site_name', 'চ্যানেল বিডিএন'),
('site_tagline', 'সত্যের সন্ধানে সর্বদা'),
('site_email', 'info@channelbdn.com'),
('site_phone', '+880 1700-000000'),
('facebook', 'https://facebook.com/channelbdn'),
('twitter', 'https://twitter.com/channelbdn'),
('youtube', 'https://youtube.com/channelbdn'),
('instagram', ''),
('linkedin', ''),
('footer_about', 'বাংলাদেশ ও বিশ্বের সকল খবর, ব্রেকিং নিউজ, লাইভ নিউজ, রাজনীতি, বাণিজ্য, খেলা, বিনোদনসহ সকল সর্বশেষ সংবাদ সবার আগে পড়তে ক্লিক করুন।'),
('editor_name', 'সম্পাদক মহোদয়'),
('publisher_name', 'প্রকাশক মহোদয়'),
('address', '১২৩, মতিঝিল বাণিজ্যিক এলাকা, ঢাকা-১০০০'),
('ads_email', 'ads@channelbdn.com');

INSERT IGNORE INTO users (username, email, password, full_name, role, is_active) VALUES
('admin', 'admin@channelbdn.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin', 'admin', 1);

INSERT IGNORE INTO homepage_sections (section_key, section_name, is_active, sort_order) VALUES
('curated',       'বাছাইকৃত',    1, 1),
('national',      'জাতীয়',       1, 2),
('politics',      'রাজনীতি',     1, 3),
('business',      'বাণিজ্য',     1, 4),
('around_bd',     'সারাদেশ',     1, 5),
('world',         'বিশ্ব',        1, 6),
('opinion',       'মতামত',       1, 7),
('entertainment', 'বিনোদন',      1, 8),
('sports',        'খেলা',        1, 9),
('videos',        'ভিডিও',       1, 10),
('gallery',       'ফটোগ্যালারি', 1, 11);

INSERT IGNORE INTO polls (id, question, option1, option2, option3, votes1, votes2, votes3, is_active) VALUES
(1, 'বর্তমান দেশের পরিস্থিতি সম্পর্কে আপনার মতামত কী?', 'ভালো', 'মন্দ', 'মতামত নেই', 245, 189, 651, 1);

INSERT IGNORE INTO photo_gallery (id, title, caption, image_url, sort_order, is_active) VALUES
(1, 'পহেলা বৈশাখ উদযাপন', 'ঢাকায় বর্ণাঢ্য র‍্যালি', 'https://picsum.photos/seed/gallery1/900/500', 1, 1),
(2, 'জাতীয় সংসদ অধিবেশন', 'সংসদ ভবনে বিশেষ অধিবেশন', 'https://picsum.photos/seed/gallery2/400/250', 2, 1),
(3, 'ক্রিকেট উৎসব', 'মাঠে দর্শকদের উল্লাস', 'https://picsum.photos/seed/gallery3/400/250', 3, 1),
(4, 'সূর্যমুখী চাষ', 'কৃষকের সাফল্যের গল্প', 'https://picsum.photos/seed/gallery4/400/250', 4, 1),
(5, 'কর্ণফুলী নদীর পাড়', 'নৌকায় জেলেরা', 'https://picsum.photos/seed/gallery5/400/250', 5, 1);

INSERT IGNORE INTO videos (id, title, youtube_url, thumbnail, is_active) VALUES
(1, 'অস্ত্রের মজুত বাড়াচ্ছে পুলিশ', 'https://youtube.com', 'https://picsum.photos/seed/vid1/400/250', 1),
(2, 'গ্যাস নিয়ে দুঃসংবাদ', 'https://youtube.com', 'https://picsum.photos/seed/vid2/400/250', 1),
(3, 'বিএনপির নতুন সিদ্ধান্ত', 'https://youtube.com', 'https://picsum.photos/seed/vid3/400/250', 1),
(4, 'বাজেট বিশ্লেষণ ২০২৫', 'https://youtube.com', 'https://picsum.photos/seed/vid4/400/250', 1),
(5, 'পদ্মা সেতুর সাফল্য', 'https://youtube.com', 'https://picsum.photos/seed/vid5/400/250', 1);

INSERT INTO posts (title, slug, content, excerpt, category_id, author, image, status, views, is_featured, is_breaking, is_top, is_curated) VALUES
('বাংলাদেশের অর্থনীতিতে নতুন আশার আলো, রেমিট্যান্স প্রবাহ বাড়ছে', 'bangladesh-economy-remittance-2024', '<p>বাংলাদেশের অর্থনীতিতে এক নতুন আশার আলো দেখা দিচ্ছে।</p>', 'বাংলাদেশের অর্থনীতিতে এক নতুন আশার আলো দেখা দিচ্ছে। চলতি বছরে রেমিট্যান্স প্রবাহ উল্লেখযোগ্যভাবে বৃদ্ধি পেয়েছে।', 4, 'অর্থনৈতিক প্রতিবেদক', 'https://picsum.photos/seed/eco1/800/500', 'published', 1520, 1, 1, 1, 1),
('ঢাকায় মেট্রোরেলের নতুন রুট উদ্বোধন করলেন প্রধানমন্ত্রী', 'dhaka-metro-rail-new-route', '<p>ঢাকায় মেট্রোরেলের নতুন রুট আজ আনুষ্ঠানিকভাবে উদ্বোধন করা হয়েছে।</p>', 'ঢাকায় মেট্রোরেলের নতুন রুট আজ আনুষ্ঠানিকভাবে উদ্বোধন করা হয়েছে।', 1, 'নিজস্ব প্রতিবেদক', 'https://picsum.photos/seed/metro1/800/500', 'published', 2340, 1, 0, 1, 1),
('বিশ্বকাপ ক্রিকেটে বাংলাদেশের দুর্দান্ত জয়, ভারতকে হারাল টাইগাররা', 'bangladesh-cricket-beats-india', '<p>বিশ্বকাপ ক্রিকেটে বাংলাদেশ এক অবিশ্বাস্য জয় তুলে নিয়েছে।</p>', 'বিশ্বকাপ ক্রিকেটে বাংলাদেশ এক অবিশ্বাস্য জয় তুলে নিয়েছে।', 5, 'ক্রীড়া প্রতিবেদক', 'https://picsum.photos/seed/cricket1/800/500', 'published', 5670, 1, 1, 1, 1),
('দেশের শিক্ষাব্যবস্থায় আমূল পরিবর্তন আনছে নতুন কারিকুলাম', 'new-curriculum-education-reform', '<p>বাংলাদেশের শিক্ষাব্যবস্থায় আমূল পরিবর্তন আনতে নতুন কারিকুলাম চালু করা হচ্ছে।</p>', 'বাংলাদেশের শিক্ষাব্যবস্থায় আমূল পরিবর্তন আনতে নতুন কারিকুলাম চালু করা হচ্ছে।', 8, 'শিক্ষা প্রতিবেদক', 'https://picsum.photos/seed/edu1/800/500', 'published', 890, 0, 0, 0, 0),
('দেশে ডেঙ্গু আক্রান্তের সংখ্যা কমছে, স্বাস্থ্য বিভাগ জানাল', 'dengue-cases-decreasing', '<p>বাংলাদেশে ডেঙ্গু আক্রান্তের সংখ্যা ধীরে ধীরে কমছে।</p>', 'বাংলাদেশে ডেঙ্গু আক্রান্তের সংখ্যা ধীরে ধীরে কমছে বলে স্বাস্থ্য অধিদফতর জানিয়েছে।', 9, 'স্বাস্থ্য প্রতিবেদক', 'https://picsum.photos/seed/health1/800/500', 'published', 1200, 0, 0, 0, 0),
('মধ্যপ্রাচ্যে উত্তেজনা বাড়ছে, বিশ্ব নেতাদের উদ্বেগ', 'middle-east-tension-2024', '<p>মধ্যপ্রাচ্যে আবারও উত্তেজনা তীব্র আকার ধারণ করেছে।</p>', 'মধ্যপ্রাচ্যে আবারও উত্তেজনা তীব্র আকার ধারণ করেছে। বিশ্ব নেতারা উদ্বেগ প্রকাশ করেছেন।', 3, 'আন্তর্জাতিক ডেস্ক', 'https://picsum.photos/seed/intl1/800/500', 'published', 3400, 0, 1, 0, 0),
('সংসদে বিরোধী দলের তুমুল বিক্ষোভ, অধিবেশন স্থগিত', 'parliament-opposition-protest', '<p>জাতীয় সংসদে আজ বিরোধী দলের তুমুল বিক্ষোভের মুখে অধিবেশন স্থগিত হয়েছে।</p>', 'জাতীয় সংসদে আজ বিরোধী দলের তুমুল বিক্ষোভের মুখে অধিবেশন সাময়িকভাবে স্থগিত করা হয়েছে।', 2, 'রাজনৈতিক প্রতিবেদক', 'https://picsum.photos/seed/pol1/800/500', 'published', 4500, 0, 1, 0, 0),
('ঢাকায় বজ্রপাতে ৫ জনের মৃত্যু, সতর্কতা জারি', 'dhaka-lightning-deaths', '<p>ঢাকা ও আশপাশের এলাকায় বজ্রপাতে পাঁচজনের মৃত্যু হয়েছে।</p>', 'ঢাকা ও আশপাশের এলাকায় বজ্রপাতে পাঁচজনের মৃত্যু হয়েছে। আবহাওয়া অধিদফতর সতর্কতা জারি করেছে।', 1, 'নিজস্ব প্রতিবেদক', 'https://picsum.photos/seed/nat1/800/500', 'published', 2800, 0, 0, 0, 0),
('দেশে নতুন প্রযুক্তি স্টার্টআপে বিনিয়োগ বাড়ছে', 'tech-startup-investment-bd', '<p>বাংলাদেশে প্রযুক্তি খাতে স্টার্টআপ বিনিয়োগ ক্রমশ বাড়ছে।</p>', 'বাংলাদেশে প্রযুক্তি খাতে স্টার্টআপ বিনিয়োগ ক্রমশ বাড়ছে। চলতি বছরে ২০টি নতুন স্টার্টআপ বড় বিনিয়োগ পেয়েছে।', 7, 'প্রযুক্তি প্রতিবেদক', 'https://picsum.photos/seed/tech1/800/500', 'published', 1800, 0, 0, 0, 0),
('ঢালিউডের নতুন ছবি মুক্তি পেল, হলে উপচে পড়া ভিড়', 'dhallywood-new-movie-release', '<p>ঢালিউডের বহুল প্রতীক্ষিত নতুন ছবি আজ মুক্তি পেয়েছে।</p>', 'ঢালিউডের বহুল প্রতীক্ষিত নতুন ছবি আজ মুক্তি পেয়েছে। ঢাকার সিনেমা হলগুলোতে দর্শকদের উপচে পড়া ভিড় দেখা গেছে।', 6, 'বিনোদন প্রতিবেদক', 'https://picsum.photos/seed/ent1/800/500', 'published', 3200, 0, 0, 0, 0),
('আইনজীবী হত্যা মামলায় দুইজনের মৃত্যুদণ্ড', 'lawyer-murder-case-verdict', '<p>রাজধানীর একটি আদালত আইনজীবী হত্যা মামলায় দুই আসামিকে মৃত্যুদণ্ড দিয়েছেন।</p>', 'রাজধানীর একটি আদালত আইনজীবী হত্যা মামলায় দুই আসামিকে মৃত্যুদণ্ডের আদেশ দিয়েছেন।', 10, 'আইন প্রতিবেদক', 'https://picsum.photos/seed/law1/800/500', 'published', 2100, 0, 0, 0, 0),
('বিশ্ব অর্থনীতিতে মন্দার আশঙ্কা, আইএমএফের সতর্কবার্তা', 'world-economy-recession-imf', '<p>আন্তর্জাতিক মুদ্রা তহবিল বিশ্ব অর্থনীতিতে মন্দার আশঙ্কা প্রকাশ করেছে।</p>', 'আন্তর্জাতিক মুদ্রা তহবিল বিশ্ব অর্থনীতিতে মন্দার আশঙ্কা প্রকাশ করেছে।', 3, 'আন্তর্জাতিক ডেস্ক', 'https://picsum.photos/seed/intl2/800/500', 'published', 1900, 0, 0, 0, 0),
('ফুটবলে বাংলাদেশ দলের নতুন কোচ নিয়োগ', 'bangladesh-football-new-coach', '<p>বাংলাদেশ জাতীয় ফুটবল দলের নতুন কোচ নিয়োগ দেওয়া হয়েছে।</p>', 'বাংলাদেশ জাতীয় ফুটবল দলের নতুন কোচ হিসেবে স্পেনের একজন অভিজ্ঞ প্রশিক্ষককে নিয়োগ দেওয়া হয়েছে।', 5, 'ক্রীড়া প্রতিবেদক', 'https://picsum.photos/seed/sport2/800/500', 'published', 1400, 0, 0, 0, 0),
('গার্মেন্টস খাতে রপ্তানি আয়ে নতুন রেকর্ড', 'garments-export-record', '<p>বাংলাদেশের তৈরি পোশাক খাতে রপ্তানি আয় নতুন রেকর্ড স্থাপন করেছে।</p>', 'বাংলাদেশের তৈরি পোশাক খাতে রপ্তানি আয় নতুন রেকর্ড স্থাপন করেছে।', 4, 'অর্থনৈতিক প্রতিবেদক', 'https://picsum.photos/seed/eco2/800/500', 'published', 2200, 0, 0, 0, 0),
('রাজধানীতে ভূমিকম্প অনুভূত, কোনো ক্ষয়ক্ষতি নেই', 'dhaka-earthquake-no-damage', '<p>আজ ভোররাতে রাজধানী ঢাকাসহ দেশের বিভিন্ন এলাকায় মাঝারি মাত্রার ভূমিকম্প অনুভূত হয়েছে।</p>', 'আজ ভোররাতে রাজধানী ঢাকাসহ দেশের বিভিন্ন এলাকায় মাঝারি মাত্রার ভূমিকম্প অনুভূত হয়েছে।', 1, 'নিজস্ব প্রতিবেদক', 'https://picsum.photos/seed/nat2/800/500', 'published', 3800, 0, 1, 0, 0),
('সোশ্যাল মিডিয়ায় গুজব ছড়ানো রোধে নতুন আইন আসছে', 'social-media-rumor-new-law', '<p>সোশ্যাল মিডিয়ায় গুজব ও মিথ্যা তথ্য ছড়ানো রোধে সরকার নতুন আইন প্রণয়নের উদ্যোগ নিয়েছে।</p>', 'সোশ্যাল মিডিয়ায় গুজব ও মিথ্যা তথ্য ছড়ানো রোধে সরকার নতুন আইন প্রণয়নের উদ্যোগ নিয়েছে।', 7, 'প্রযুক্তি প্রতিবেদক', 'https://picsum.photos/seed/tech2/800/500', 'published', 2600, 0, 0, 0, 0),
('বাংলাদেশ ও ভারতের মধ্যে নতুন বাণিজ্য চুক্তি', 'bangladesh-india-trade-deal', '<p>বাংলাদেশ ও ভারতের মধ্যে একটি নতুন দ্বিপক্ষীয় বাণিজ্য চুক্তি স্বাক্ষরিত হয়েছে।</p>', 'বাংলাদেশ ও ভারতের মধ্যে একটি নতুন দ্বিপক্ষীয় বাণিজ্য চুক্তি স্বাক্ষরিত হয়েছে।', 3, 'আন্তর্জাতিক ডেস্ক', 'https://picsum.photos/seed/intl3/800/500', 'published', 1700, 0, 0, 0, 0),
('স্বাস্থ্য বীমায় নতুন সুবিধা, সকলকে অন্তর্ভুক্তির উদ্যোগ', 'health-insurance-new-benefits', '<p>দেশের স্বাস্থ্য বীমা ব্যবস্থায় নতুন সুবিধা যোগ করা হচ্ছে।</p>', 'দেশের স্বাস্থ্য বীমা ব্যবস্থায় নতুন সুবিধা যোগ করা হচ্ছে। সকল নাগরিককে অন্তর্ভুক্তির উদ্যোগ নেওয়া হয়েছে।', 9, 'স্বাস্থ্য প্রতিবেদক', 'https://picsum.photos/seed/health2/800/500', 'published', 980, 0, 0, 0, 0),
('বিশ্বের সেরা বিশ্ববিদ্যালয়ে বাংলাদেশি শিক্ষার্থীর সংখ্যা বাড়ছে', 'bangladeshi-students-top-universities', '<p>বিশ্বের শীর্ষ বিশ্ববিদ্যালয়গুলোতে বাংলাদেশি শিক্ষার্থীর সংখ্যা প্রতি বছর বাড়ছে।</p>', 'বিশ্বের শীর্ষ বিশ্ববিদ্যালয়গুলোতে বাংলাদেশি শিক্ষার্থীর সংখ্যা প্রতি বছর বাড়ছে।', 8, 'শিক্ষা প্রতিবেদক', 'https://picsum.photos/seed/edu2/800/500', 'published', 1500, 0, 0, 0, 0),
('রাজনৈতিক সংকট নিরসনে মধ্যস্থতার উদ্যোগ', 'political-crisis-mediation', '<p>দেশের চলমান রাজনৈতিক সংকট নিরসনে বিশিষ্ট নাগরিক সমাজ মধ্যস্থতার উদ্যোগ নিয়েছেন।</p>', 'দেশের চলমান রাজনৈতিক সংকট নিরসনে বিশিষ্ট নাগরিক সমাজ মধ্যস্থতার উদ্যোগ নিয়েছেন।', 2, 'রাজনৈতিক প্রতিবেদক', 'https://picsum.photos/seed/pol2/800/500', 'published', 2900, 0, 0, 0, 0),
('ঈদ ফ্যাশনে নতুন ট্রেন্ড, ডিজাইনাররা কী বলছেন', 'eid-fashion-trend', '<p>আসন্ন ঈদুল আজহাকে সামনে রেখে দেশের ফ্যাশন ডিজাইনাররা নতুন সংগ্রহ নিয়ে আসছেন।</p>', 'আসন্ন ঈদুল আজহাকে সামনে রেখে দেশের ফ্যাশন ডিজাইনাররা নতুন সংগ্রহ নিয়ে আসছেন।', 11, 'লাইফস্টাইল প্রতিবেদক', 'https://picsum.photos/seed/life1/800/500', 'published', 1100, 0, 0, 0, 0);

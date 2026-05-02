<?php
if (!defined('SITE_URL')) require_once 'includes/functions.php';
$page_title = 'পৃষ্ঠাটি পাওয়া যায়নি - ৪০৪';
include 'includes/header.php';
?>
<div class="main-content">
<div class="container" style="text-align:center;padding:80px 20px;">
  <i class="fas fa-exclamation-triangle" style="font-size:72px;color:#e0e0e0;display:block;margin-bottom:20px"></i>
  <h1 style="font-family:var(--font-heading);font-size:36px;color:var(--secondary);margin-bottom:10px">৪০৪ - পৃষ্ঠাটি পাওয়া যায়নি</h1>
  <p style="color:var(--text-gray);margin-bottom:24px">আপনি যে পৃষ্ঠাটি খুঁজছেন তা বিদ্যমান নেই বা সরিয়ে ফেলা হয়েছে।</p>
  <a href="<?= SITE_URL ?>/" style="display:inline-block;background:var(--primary);color:#fff;padding:12px 28px;border-radius:4px;font-size:15px;font-weight:600">হোমপেজে ফিরুন</a>
</div>
</div>
<?php include 'includes/footer.php'; ?>

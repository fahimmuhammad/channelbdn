<?php
require_once 'auth.php'; requireLogin();
require_once dirname(__DIR__) . '/config.php';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    if ($id && in_array($action, ['approve','reject','delete'])) {
        if ($action === 'approve') {
            $conn->query("UPDATE reader_comments SET status='approved' WHERE id=$id");
            $success = 'মন্তব্য অনুমোদিত হয়েছে।';
        } elseif ($action === 'reject') {
            $conn->query("UPDATE reader_comments SET status='rejected' WHERE id=$id");
            $success = 'মন্তব্য প্রত্যাখ্যান করা হয়েছে।';
        } elseif ($action === 'delete') {
            $conn->query("DELETE FROM reader_comments WHERE id=$id");
            $success = 'মন্তব্য মুছে ফেলা হয়েছে।';
        }
    }
}

$filter = $_GET['filter'] ?? 'pending';
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 25;
$offset = ($page - 1) * $limit;

$allowed_filters = ['pending','approved','rejected','all'];
if (!in_array($filter, $allowed_filters)) $filter = 'pending';

$where = $filter !== 'all' ? "WHERE rc.status='$filter'" : '';
$total = $conn->query("SELECT COUNT(*) as c FROM reader_comments rc $where")->fetch_assoc()['c'];
$pages = max(1, ceil($total / $limit));

$rows = $conn->query("SELECT rc.*, rd.name as reader_name, rd.email as reader_email,
                       p.title as post_title, p.slug as post_slug
                       FROM reader_comments rc
                       JOIN readers rd ON rc.reader_id = rd.id
                       JOIN posts p    ON rc.post_id   = p.id
                       $where
                       ORDER BY rc.created_at DESC
                       LIMIT $limit OFFSET $offset");

require_once 'includes/admin_header.php';
?>
<style>
.cm-filter-tabs { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
.cm-tab { padding:7px 18px; border-radius:20px; font-size:.82rem; font-weight:700;
    border:1.5px solid #ddd; background:transparent; cursor:pointer; text-decoration:none;
    color:var(--text-color,#333); transition:all .2s; font-family:'Hind Siliguri',sans-serif; }
.cm-tab.active, .cm-tab:hover { background:#e8001c; border-color:#e8001c; color:#fff; }
[data-theme="dark"] .cm-tab { border-color:#2c3145; color:#c0c7e0; }
.cm-card { background:var(--card-bg,#fff); border-radius:10px; border:1px solid var(--border-color,#eee);
    padding:16px 20px; margin-bottom:12px; }
[data-theme="dark"] .cm-card { background:#1e2130; border-color:#2c3145; }
.cm-card-top { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:8px; }
.cm-meta { font-size:.8rem; color:#888; font-family:'Hind Siliguri',sans-serif; }
.cm-meta strong { color:var(--text-color,#333); font-weight:700; }
[data-theme="dark"] .cm-meta strong { color:#e4e6ef; }
.cm-post-link { font-size:.78rem; color:#e8001c; text-decoration:none; font-family:'Hind Siliguri',sans-serif; }
.cm-text { font-size:.92rem; line-height:1.7; color:var(--text-color,#333);
    font-family:'Hind Siliguri',sans-serif; margin:8px 0 12px; }
[data-theme="dark"] .cm-text { color:#c0c7e0; }
.cm-actions { display:flex; gap:8px; }
.cm-status-badge { padding:3px 10px; border-radius:12px; font-size:.75rem; font-weight:700; }
.cm-status-badge.pending  { background:#fff3cd; color:#856404; }
.cm-status-badge.approved { background:#d1fae5; color:#065f46; }
.cm-status-badge.rejected { background:#fee2e2; color:#991b1b; }
[data-theme="dark"] .cm-status-badge.pending  { background:#332a00; color:#fbbf24; }
[data-theme="dark"] .cm-status-badge.approved { background:#022c22; color:#4ade80; }
[data-theme="dark"] .cm-status-badge.rejected { background:#2a0a0a; color:#f87171; }
</style>

<div class="admin-main">
<div class="admin-page-header">
    <h2 class="admin-page-title"><i class="far fa-comments"></i> মন্তব্য পরিচালনা</h2>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="cm-filter-tabs">
    <?php foreach (['pending'=>'অপেক্ষমান','approved'=>'অনুমোদিত','rejected'=>'প্রত্যাখ্যাত','all'=>'সবগুলো'] as $f => $label): ?>
    <a href="?filter=<?= $f ?>" class="cm-tab <?= $filter===$f?'active':'' ?>"><?= $label ?></a>
    <?php endforeach; ?>
    <span style="margin-left:auto;font-size:.82rem;color:#888;align-self:center;font-family:'Hind Siliguri',sans-serif;">মোট: <?= $total ?></span>
</div>

<?php if (!$rows || !$rows->num_rows): ?>
<div style="text-align:center;padding:40px;color:#999;font-family:'Hind Siliguri',sans-serif;">কোনো মন্তব্য পাওয়া যায়নি।</div>
<?php else: ?>
<?php while ($c = $rows->fetch_assoc()): ?>
<div class="cm-card">
    <div class="cm-card-top">
        <div>
            <div class="cm-meta">
                <strong><?= htmlspecialchars($c['reader_name']) ?></strong>
                <span style="margin:0 6px">·</span><?= htmlspecialchars($c['reader_email']) ?>
                <span style="margin:0 6px">·</span><?= date('d M Y, H:i', strtotime($c['created_at'])) ?>
            </div>
            <a href="<?= SITE_URL ?>/single.php?slug=<?= urlencode($c['post_slug']) ?>" class="cm-post-link" target="_blank">
                <i class="fas fa-external-link-alt" style="font-size:.7rem"></i> <?= htmlspecialchars(mb_substr($c['post_title'], 0, 60)) ?>...
            </a>
        </div>
        <span class="cm-status-badge <?= $c['status'] ?>">
            <?= ['pending'=>'অপেক্ষমান','approved'=>'অনুমোদিত','rejected'=>'প্রত্যাখ্যাত'][$c['status']] ?? $c['status'] ?>
        </span>
    </div>
    <div class="cm-text"><?= nl2br(htmlspecialchars($c['comment'])) ?></div>
    <div class="cm-actions">
        <?php if ($c['status'] !== 'approved'): ?>
        <form method="POST" style="display:inline">
            <input type="hidden" name="action" value="approve">
            <input type="hidden" name="id" value="<?= $c['id'] ?>">
            <input type="hidden" name="filter_back" value="<?= $filter ?>">
            <button class="btn-sm-text btn-green" type="submit"><i class="fas fa-check"></i> অনুমোদন</button>
        </form>
        <?php endif; ?>
        <?php if ($c['status'] !== 'rejected'): ?>
        <form method="POST" style="display:inline">
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="id" value="<?= $c['id'] ?>">
            <button class="btn-sm-text btn-red" type="submit"><i class="fas fa-times"></i> প্রত্যাখ্যান</button>
        </form>
        <?php endif; ?>
        <form method="POST" style="display:inline" onsubmit="return confirm('মন্তব্যটি মুছে ফেলবেন?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $c['id'] ?>">
            <button class="btn-sm-text" type="submit" style="color:#e8001c"><i class="fas fa-trash"></i> মুছুন</button>
        </form>
    </div>
</div>
<?php endwhile; ?>

<!-- Pagination -->
<?php if ($pages > 1): ?>
<div class="pagination" style="margin-top:20px">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
    <a class="page-link <?= $p===$page?'active':'' ?>" href="?filter=<?= $filter ?>&page=<?= $p ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>
</div>

<?php require_once 'includes/admin_footer.php'; ?>

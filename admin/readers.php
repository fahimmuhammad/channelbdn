<?php
require_once 'auth.php'; requireLogin();
require_once dirname(__DIR__) . '/config.php';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    if ($id) {
        if ($action === 'activate') {
            $conn->query("UPDATE readers SET is_active=1 WHERE id=$id");
            $success = 'অ্যাকাউন্ট সক্রিয় করা হয়েছে।';
        } elseif ($action === 'deactivate') {
            $conn->query("UPDATE readers SET is_active=0 WHERE id=$id");
            $success = 'অ্যাকাউন্ট নিষ্ক্রিয় করা হয়েছে।';
        } elseif ($action === 'delete') {
            $conn->query("DELETE FROM reader_bookmarks WHERE reader_id=$id");
            $conn->query("DELETE FROM reader_reactions WHERE reader_id=$id");
            $conn->query("DELETE FROM reader_comments WHERE reader_id=$id");
            $conn->query("DELETE FROM reader_history WHERE reader_id=$id");
            $conn->query("DELETE FROM readers WHERE id=$id");
            $success = 'পাঠক অ্যাকাউন্ট মুছে ফেলা হয়েছে।';
        }
    }
}

$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['q'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 20;
$offset = ($page - 1) * $limit;

$where = [];
if ($filter === 'active')   $where[] = 'is_active=1';
if ($filter === 'inactive') $where[] = 'is_active=0';
if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $where[] = "(name LIKE '%$s%' OR email LIKE '%$s%')";
}
$wClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = $conn->query("SELECT COUNT(*) as c FROM readers $wClause")->fetch_assoc()['c'];
$pages = max(1, ceil($total / $limit));

$rows = $conn->query(
    "SELECT r.*,
        (SELECT COUNT(*) FROM reader_bookmarks WHERE reader_id=r.id) as bm_count,
        (SELECT COUNT(*) FROM reader_comments WHERE reader_id=r.id) as cm_count,
        (SELECT COUNT(*) FROM reader_reactions WHERE reader_id=r.id) as rc_count,
        (SELECT COUNT(*) FROM reader_history WHERE reader_id=r.id) as hist_count
     FROM readers r $wClause
     ORDER BY r.created_at DESC
     LIMIT $limit OFFSET $offset"
);

require_once 'includes/admin_header.php';
?>
<div class="admin-main">
<div class="admin-page-header">
    <h2 class="admin-page-title"><i class="fas fa-user-friends"></i> পাঠক অ্যাকাউন্ট</h2>
    <span style="font-size:.85rem;color:var(--text-muted)">মোট: <?= $total ?> জন পাঠক</span>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

<!-- Filters + Search -->
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px;align-items:center">
    <?php foreach (['all'=>'সবাই','active'=>'সক্রিয়','inactive'=>'নিষ্ক্রিয়'] as $f=>$lbl): ?>
    <a href="?filter=<?= $f ?><?= $search?'&q='.urlencode($search):'' ?>"
       class="cm-tab <?= $filter===$f?'active':'' ?>"><?= $lbl ?></a>
    <?php endforeach; ?>
    <form method="GET" style="margin-left:auto;display:flex;gap:6px">
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
               placeholder="নাম বা ইমেইল খুঁজুন…"
               style="padding:7px 12px;border:1.5px solid var(--border-color,#ddd);border-radius:8px;
                      font-size:.85rem;font-family:'Hind Siliguri',sans-serif;
                      background:var(--card-bg,#fff);color:var(--text-color,#333);outline:none">
        <button type="submit" style="padding:7px 14px;background:#e8001c;color:#fff;border:none;
                border-radius:8px;cursor:pointer;font-size:.85rem"><i class="fas fa-search"></i></button>
    </form>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
    <thead>
        <tr>
            <th>#</th>
            <th>নাম ও ইমেইল</th>
            <th>নিবন্ধন</th>
            <th style="text-align:center">বুকমার্ক</th>
            <th style="text-align:center">মন্তব্য</th>
            <th style="text-align:center">প্রতিক্রিয়া</th>
            <th style="text-align:center">পঠিত</th>
            <th style="text-align:center">অবস্থা</th>
            <th style="text-align:center">কার্যক্রম</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!$rows || !$rows->num_rows): ?>
    <tr><td colspan="9" style="text-align:center;padding:30px;color:var(--text-muted)">কোনো পাঠক পাওয়া যায়নি।</td></tr>
    <?php else: ?>
    <?php while ($r = $rows->fetch_assoc()): ?>
    <tr>
        <td style="color:var(--text-muted)"><?= $r['id'] ?></td>
        <td>
            <div style="font-weight:700;color:var(--text-color,#1a1a1a)"><?= htmlspecialchars($r['name']) ?></div>
            <div style="font-size:.78rem;color:var(--text-muted)"><?= htmlspecialchars($r['email']) ?></div>
        </td>
        <td style="font-size:.82rem;color:var(--text-muted)"><?= date('d M Y', strtotime($r['created_at'])) ?></td>
        <td style="text-align:center;font-weight:700"><?= $r['bm_count'] ?></td>
        <td style="text-align:center;font-weight:700"><?= $r['cm_count'] ?></td>
        <td style="text-align:center;font-weight:700"><?= $r['rc_count'] ?></td>
        <td style="text-align:center;font-weight:700"><?= $r['hist_count'] ?></td>
        <td style="text-align:center">
            <span class="btn-status <?= $r['is_active'] ? 'active' : 'inactive' ?>">
                <?= $r['is_active'] ? 'সক্রিয়' : 'নিষ্ক্রিয়' ?>
            </span>
        </td>
        <td style="text-align:center">
            <div style="display:flex;gap:6px;justify-content:center">
                <?php if ($r['is_active']): ?>
                <form method="POST" style="display:inline">
                    <input type="hidden" name="action" value="deactivate">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <button class="btn-sm-text btn-red" type="submit"><i class="fas fa-ban"></i> নিষ্ক্রিয়</button>
                </form>
                <?php else: ?>
                <form method="POST" style="display:inline">
                    <input type="hidden" name="action" value="activate">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <button class="btn-sm-text btn-green" type="submit"><i class="fas fa-check"></i> সক্রিয়</button>
                </form>
                <?php endif; ?>
                <form method="POST" style="display:inline"
                      onsubmit="return confirm('এই পাঠকের সকল ডেটাসহ অ্যাকাউন্ট মুছে ফেলবেন?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <button class="btn-sm-text" type="submit" style="color:#e8001c">
                        <i class="fas fa-trash"></i> মুছুন
                    </button>
                </form>
            </div>
        </td>
    </tr>
    <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
</table>
</div>

<?php if ($pages > 1): ?>
<div class="pagination" style="margin-top:20px">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
    <a class="page-link <?= $p===$page?'active':'' ?>"
       href="?filter=<?= $filter ?>&q=<?= urlencode($search) ?>&page=<?= $p ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
</div>

<?php require_once 'includes/admin_footer.php'; ?>

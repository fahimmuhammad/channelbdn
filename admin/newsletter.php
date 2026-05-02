<?php
require_once 'auth.php'; requireLogin();
require_once dirname(__DIR__) . '/config.php';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    if ($id) {
        if ($action === 'activate') {
            $conn->query("UPDATE newsletter_subscribers SET is_active=1 WHERE id=$id");
            $success = 'সাবস্ক্রিপশন সক্রিয় করা হয়েছে।';
        } elseif ($action === 'deactivate') {
            $conn->query("UPDATE newsletter_subscribers SET is_active=0 WHERE id=$id");
            $success = 'সাবস্ক্রিপশন নিষ্ক্রিয় করা হয়েছে।';
        } elseif ($action === 'delete') {
            $conn->query("DELETE FROM newsletter_subscribers WHERE id=$id");
            $success = 'সাবস্ক্রাইবার মুছে ফেলা হয়েছে।';
        }
    }
}

$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['q'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 25;
$offset = ($page - 1) * $limit;

$where = [];
if ($filter === 'active')   $where[] = 'n.is_active=1';
if ($filter === 'inactive') $where[] = 'n.is_active=0';
if ($filter === 'reader')   $where[] = 'n.reader_id IS NOT NULL';
if ($filter === 'guest')    $where[] = 'n.reader_id IS NULL';
if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $where[] = "(n.email LIKE '%$s%' OR n.name LIKE '%$s%')";
}
$wClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = $conn->query("SELECT COUNT(*) as c FROM newsletter_subscribers n $wClause")->fetch_assoc()['c'];
$active_count = $conn->query("SELECT COUNT(*) as c FROM newsletter_subscribers WHERE is_active=1")->fetch_assoc()['c'];
$pages = max(1, ceil($total / $limit));

$rows = $conn->query(
    "SELECT n.*, r.name as reader_name
     FROM newsletter_subscribers n
     LEFT JOIN readers r ON n.reader_id = r.id
     $wClause
     ORDER BY n.created_at DESC
     LIMIT $limit OFFSET $offset"
);

require_once 'includes/admin_header.php';
?>
<div class="admin-main">
<div class="admin-page-header">
    <h2 class="admin-page-title"><i class="fas fa-envelope-open-text"></i> নিউজলেটার সাবস্ক্রাইবার</h2>
    <div style="display:flex;gap:14px;font-size:.85rem;color:var(--text-muted)">
        <span><i class="fas fa-users"></i> মোট: <?= $total ?></span>
        <span style="color:#22c55e"><i class="fas fa-check-circle"></i> সক্রিয়: <?= $active_count ?></span>
    </div>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

<!-- Stats cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:22px">
    <?php
    $stats = [
        ['সক্রিয় সদস্য', "SELECT COUNT(*) FROM newsletter_subscribers WHERE is_active=1", '#22c55e', 'fas fa-check-circle'],
        ['নিষ্ক্রিয় সদস্য', "SELECT COUNT(*) FROM newsletter_subscribers WHERE is_active=0", '#f87171', 'fas fa-times-circle'],
        ['নিবন্ধিত পাঠক', "SELECT COUNT(*) FROM newsletter_subscribers WHERE reader_id IS NOT NULL AND is_active=1", '#60a5fa', 'fas fa-user-check'],
        ['অতিথি সদস্য', "SELECT COUNT(*) FROM newsletter_subscribers WHERE reader_id IS NULL AND is_active=1", '#a78bfa', 'fas fa-user'],
    ];
    foreach ($stats as $st):
        $cnt = $conn->query($st[1])->fetch_row()[0];
    ?>
    <div style="background:var(--card-bg,#fff);border:1px solid var(--border-color,#eee);border-radius:10px;
                padding:14px 16px;display:flex;align-items:center;gap:12px">
        <i class="<?= $st[3] ?>" style="font-size:1.4rem;color:<?= $st[0] ?>"></i>
        <div>
            <div style="font-size:1.4rem;font-weight:800;color:<?= $st[2] ?>;line-height:1"><?= $cnt ?></div>
            <div style="font-size:.75rem;color:var(--text-muted)"><?= $st[0] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Filters + Search -->
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;align-items:center">
    <?php foreach (['all'=>'সবাই','active'=>'সক্রিয়','inactive'=>'নিষ্ক্রিয়','reader'=>'নিবন্ধিত','guest'=>'অতিথি'] as $f=>$lbl): ?>
    <a href="?filter=<?= $f ?><?= $search?'&q='.urlencode($search):'' ?>"
       class="cm-tab <?= $filter===$f?'active':'' ?>"><?= $lbl ?></a>
    <?php endforeach; ?>
    <form method="GET" style="margin-left:auto;display:flex;gap:6px">
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
               placeholder="ইমেইল বা নাম খুঁজুন…"
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
            <th>ইমেইল</th>
            <th>নাম</th>
            <th>ধরন</th>
            <th>পছন্দের বিভাগ</th>
            <th>সাবস্ক্রিপশন তারিখ</th>
            <th style="text-align:center">অবস্থা</th>
            <th style="text-align:center">কার্যক্রম</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!$rows || !$rows->num_rows): ?>
    <tr><td colspan="8" style="text-align:center;padding:30px;color:var(--text-muted)">কোনো সাবস্ক্রাইবার পাওয়া যায়নি।</td></tr>
    <?php else: ?>
    <?php while ($sub = $rows->fetch_assoc()): ?>
    <?php
        $cats = array_filter(explode(',', $sub['categories'] ?? ''));
        $catLabels = ['জাতীয়'=>'জাতীয়','রাজনীতি'=>'রাজনীতি','আন্তর্জাতিক'=>'আন্তর্জাতিক',
                      'খেলাধুলা'=>'খেলা','বিনোদন'=>'বিনোদন','প্রযুক্তি'=>'প্রযুক্তি'];
    ?>
    <tr>
        <td style="color:var(--text-muted)"><?= $sub['id'] ?></td>
        <td style="font-weight:600"><?= htmlspecialchars($sub['email']) ?></td>
        <td>
            <?php if ($sub['reader_name']): ?>
            <span><?= htmlspecialchars($sub['reader_name']) ?></span>
            <?php elseif ($sub['name']): ?>
            <span style="color:var(--text-muted)"><?= htmlspecialchars($sub['name']) ?></span>
            <?php else: ?>
            <span style="color:var(--text-muted)">—</span>
            <?php endif; ?>
        </td>
        <td>
            <?php if ($sub['reader_id']): ?>
            <span style="background:#dbeafe;color:#1d4ed8;padding:2px 8px;border-radius:10px;font-size:.75rem;font-weight:700">নিবন্ধিত</span>
            <?php else: ?>
            <span style="background:#f3f4f6;color:#6b7280;padding:2px 8px;border-radius:10px;font-size:.75rem;font-weight:700">অতিথি</span>
            <?php endif; ?>
        </td>
        <td style="font-size:.8rem">
            <?php if (!empty($cats)): ?>
                <?php foreach ($cats as $c): ?>
                <span style="display:inline-block;background:#fee2e2;color:#991b1b;padding:1px 7px;
                             border-radius:8px;margin:2px;font-size:.72rem"><?= htmlspecialchars($c) ?></span>
                <?php endforeach; ?>
            <?php else: ?>
            <span style="color:var(--text-muted)">সব বিভাগ</span>
            <?php endif; ?>
        </td>
        <td style="font-size:.82rem;color:var(--text-muted)"><?= date('d M Y', strtotime($sub['created_at'])) ?></td>
        <td style="text-align:center">
            <span class="btn-status <?= $sub['is_active'] ? 'active' : 'inactive' ?>">
                <?= $sub['is_active'] ? 'সক্রিয়' : 'নিষ্ক্রিয়' ?>
            </span>
        </td>
        <td style="text-align:center">
            <div style="display:flex;gap:6px;justify-content:center">
                <?php if ($sub['is_active']): ?>
                <form method="POST" style="display:inline">
                    <input type="hidden" name="action" value="deactivate">
                    <input type="hidden" name="id" value="<?= $sub['id'] ?>">
                    <button class="btn-sm-text btn-red" type="submit"><i class="fas fa-ban"></i> আনসাব</button>
                </form>
                <?php else: ?>
                <form method="POST" style="display:inline">
                    <input type="hidden" name="action" value="activate">
                    <input type="hidden" name="id" value="<?= $sub['id'] ?>">
                    <button class="btn-sm-text btn-green" type="submit"><i class="fas fa-check"></i> সক্রিয়</button>
                </form>
                <?php endif; ?>
                <form method="POST" style="display:inline"
                      onsubmit="return confirm('এই সাবস্ক্রাইবার মুছে ফেলবেন?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $sub['id'] ?>">
                    <button class="btn-sm-text" type="submit" style="color:#e8001c">
                        <i class="fas fa-trash"></i>
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

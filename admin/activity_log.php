<?php
require_once 'auth.php';
requirePermission('users');
$admin_title = __('activity_log');

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 30;
$offset  = ($page - 1) * $perPage;

$where = '1';
if (!empty($_GET['user']))   $where .= " AND username='" . $conn->real_escape_string($_GET['user']) . "'";
if (!empty($_GET['role']))   $where .= " AND role='" . $conn->real_escape_string($_GET['role']) . "'";
if (!empty($_GET['action'])) $where .= " AND action='" . $conn->real_escape_string($_GET['action']) . "'";
if (!empty($_GET['date']))   $where .= " AND DATE(created_at)='" . $conn->real_escape_string($_GET['date']) . "'";

$total = $conn->query("SELECT COUNT(*) as c FROM activity_log WHERE $where")->fetch_assoc()['c'];
$pages = max(1, ceil($total / $perPage));
$logs  = $conn->query("SELECT * FROM activity_log WHERE $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset")->fetch_all(MYSQLI_ASSOC);

$users_list  = $conn->query("SELECT DISTINCT username FROM activity_log ORDER BY username")->fetch_all(MYSQLI_ASSOC);
$action_list = $conn->query("SELECT DISTINCT action FROM activity_log ORDER BY action")->fetch_all(MYSQLI_ASSOC);

$role_labels = [
    'admin'      => ['label' => __('role_admin'),      'class' => 'role-admin'],
    'editor'     => ['label' => __('role_editor'),     'class' => 'role-editor'],
    'reporter'   => ['label' => __('role_reporter'),   'class' => 'role-reporter'],
    'moderator'  => ['label' => __('role_moderator'),  'class' => 'role-moderator'],
    'ad_manager' => ['label' => __('role_ad_manager'), 'class' => 'role-ad_manager'],
];

include 'includes/admin_header.php';
?>

<div class="admin-main">
  <h1 class="page-title"><i class="fas fa-history"></i> <?= __('activity_log') ?></h1>

  <!-- Filters -->
  <form method="GET" class="admin-card" style="margin-bottom:20px">
    <div class="admin-card-body" style="padding:16px 20px">
      <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
        <div>
          <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px"><?= __('col_user') ?></label>
          <select name="user" style="padding:7px 10px;border:1.5px solid #ddd;border-radius:6px;font-size:13px">
            <option value=""><?= __('all') ?></option>
            <?php foreach ($users_list as $ul): ?>
            <option value="<?= htmlspecialchars($ul['username']) ?>" <?= ($_GET['user'] ?? '') === $ul['username'] ? 'selected' : '' ?>><?= htmlspecialchars($ul['username']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px"><?= __('col_role') ?></label>
          <select name="role" style="padding:7px 10px;border:1.5px solid #ddd;border-radius:6px;font-size:13px">
            <option value=""><?= __('all') ?></option>
            <option value="admin" <?= ($_GET['role'] ?? '')==='admin'?'selected':'' ?>><?= __('role_admin') ?></option>
            <option value="editor" <?= ($_GET['role'] ?? '')==='editor'?'selected':'' ?>><?= __('role_editor') ?></option>
            <option value="reporter" <?= ($_GET['role'] ?? '')==='reporter'?'selected':'' ?>><?= __('role_reporter') ?></option>
            <option value="moderator" <?= ($_GET['role'] ?? '')==='moderator'?'selected':'' ?>><?= __('role_moderator') ?></option>
            <option value="ad_manager" <?= ($_GET['role'] ?? '')==='ad_manager'?'selected':'' ?>><?= __('role_ad_manager') ?></option>
          </select>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px"><?= __('col_action') ?></label>
          <select name="action" style="padding:7px 10px;border:1.5px solid #ddd;border-radius:6px;font-size:13px">
            <option value=""><?= __('all') ?></option>
            <?php foreach ($action_list as $al): ?>
            <option value="<?= htmlspecialchars($al['action']) ?>" <?= ($_GET['action'] ?? '') === $al['action'] ? 'selected' : '' ?>><?= htmlspecialchars($al['action']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px"><?= __('col_date') ?></label>
          <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" style="padding:7px 10px;border:1.5px solid #ddd;border-radius:6px;font-size:13px">
        </div>
        <button type="submit" class="btn-primary" style="padding:8px 18px"><i class="fas fa-filter"></i> <?= __('btn_filter') ?></button>
        <a href="activity_log.php" class="btn-cancel" style="font-size:13px"><?= __('btn_reset') ?></a>
      </div>
    </div>
  </form>

  <div class="admin-card">
    <div class="admin-card-header">
      <h3><i class="fas fa-list"></i> <?= sprintf(__('total_records'), $total) ?></h3>
    </div>
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr><th>#</th><th><?= __('col_user') ?></th><th><?= __('col_role') ?></th><th><?= __('col_action') ?></th><th><?= __('col_details') ?></th><th><?= __('col_ip') ?></th><th><?= __('col_time') ?></th></tr>
        </thead>
        <tbody>
          <?php if (!$logs): ?>
          <tr><td colspan="7" style="text-align:center;color:#888;padding:30px"><?= __('no_records') ?></td></tr>
          <?php endif; ?>
          <?php foreach ($logs as $log):
            $rl = $role_labels[$log['role']] ?? ['label'=>$log['role'],'class'=>'']; ?>
          <tr>
            <td style="color:var(--text-muted);font-size:12px"><?= $log['id'] ?></td>
            <td><strong><?= htmlspecialchars($log['username']) ?></strong></td>
            <td><span class="role-badge <?= $rl['class'] ?>"><?= $rl['label'] ?></span></td>
            <td><code><?= htmlspecialchars($log['action']) ?></code></td>
            <td style="font-size:13px;color:var(--text)"><?= htmlspecialchars($log['details']) ?></td>
            <td style="font-size:12px;color:var(--text-muted)"><?= htmlspecialchars($log['ip_address']) ?></td>
            <td style="font-size:12px;color:#888;white-space:nowrap"><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($pages > 1): ?>
    <div style="padding:16px 20px;display:flex;gap:6px;flex-wrap:wrap">
      <?php for ($i = 1; $i <= $pages; $i++):
        $q = array_merge($_GET, ['page' => $i]);
        $active = $i === $page; ?>
      <a href="?<?= http_build_query($q) ?>" class="page-link <?= $active?'active':'' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/admin_footer.php'; ?>

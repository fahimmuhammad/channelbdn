<?php
require_once 'auth.php'; requirePermission('polls');
require_once dirname(__DIR__) . '/config.php';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $q  = $conn->real_escape_string(trim($_POST['question'] ?? ''));
        $o1 = $conn->real_escape_string(trim($_POST['option1'] ?? ''));
        $o2 = $conn->real_escape_string(trim($_POST['option2'] ?? ''));
        $o3 = $conn->real_escape_string(trim($_POST['option3'] ?? ''));
        if ($q && $o1 && $o2) {
            $conn->query("UPDATE polls SET is_active=0");
            $r = $conn->query("INSERT INTO polls (question,option1,option2,option3,is_active) VALUES ('$q','$o1','$o2','$o3',1)");
            $success = $r ? __('poll_added') : __('error_prefix') . $conn->error;
        } else { $error = __('poll_error'); }
    } elseif ($action === 'activate') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("UPDATE polls SET is_active=0");
        $conn->query("UPDATE polls SET is_active=1 WHERE id=$id");
        $success = __('poll_activated');
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("DELETE FROM polls WHERE id=$id");
        $success = __('poll_deleted');
    } elseif ($action === 'reset') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("UPDATE polls SET votes1=0,votes2=0,votes3=0 WHERE id=$id");
        $success = __('votes_reset');
    }
}

$polls = $conn->query("SELECT * FROM polls ORDER BY created_at DESC");
$admin_title = __('nav_polls'); include 'includes/admin_header.php';
?>
<div class="admin-main">
  <h1 class="page-title"><i class="fas fa-poll"></i> <?= __('poll_management') ?></h1>
  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

  <div class="form-grid">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-list"></i> <?= __('all_polls') ?></h3></div>
      <div class="table-wrap">
        <table class="admin-table">
          <thead><tr><th>#</th><th><?= __('col_question') ?></th><th><?= __('col_votes') ?></th><th><?= __('col_status') ?></th><th><?= __('col_actions') ?></th></tr></thead>
          <tbody>
            <?php $i=1; while ($p = $polls->fetch_assoc()): $tv=$p['votes1']+$p['votes2']+$p['votes3']; ?>
            <tr>
              <td><?= $i++ ?></td>
              <td>
                <strong><?= htmlspecialchars(mb_substr($p['question'],0,60)) ?></strong>
                <div style="font-size:12px;color:#888;margin-top:4px">
                  <?= htmlspecialchars($p['option1']) ?> (<?= $p['votes1'] ?>) &nbsp;|&nbsp;
                  <?= htmlspecialchars($p['option2']) ?> (<?= $p['votes2'] ?>) &nbsp;|&nbsp;
                  <?= htmlspecialchars($p['option3']) ?> (<?= $p['votes3'] ?>)
                </div>
              </td>
              <td><span class="badge"><?= $tv ?></span></td>
              <td><span class="btn-status <?= $p['is_active']?'active':'inactive' ?>"><i class="fas fa-<?= $p['is_active']?'check-circle':'times-circle' ?>"></i> <?= $p['is_active'] ? __('status_active') : __('status_inactive') ?></span></td>
              <td style="display:flex;gap:4px;flex-wrap:wrap">
                <?php if (!$p['is_active']): ?>
                <form method="POST" style="display:inline"><input type="hidden" name="action" value="activate"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button type="submit" class="btn-sm-text btn-green"><?= __('btn_activate') ?></button></form>
                <?php endif; ?>
                <form method="POST" style="display:inline"><input type="hidden" name="action" value="reset"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button type="submit" class="btn-sm-text" data-confirm="<?= __('confirm_reset_poll') ?>"><?= __('btn_reset') ?></button></form>
                <form method="POST" style="display:inline"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button type="submit" class="btn-sm delete" data-confirm="<?= __('confirm_del_poll') ?>"><i class="fas fa-trash"></i></button></form>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div>
      <div class="admin-card">
        <div class="admin-card-header"><h3><i class="fas fa-plus"></i> <?= __('new_poll') ?></h3></div>
        <div class="admin-card-body">
          <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group"><label><?= __('question') ?> <span class="required">*</span></label><textarea name="question" class="form-control" rows="3" placeholder="<?= __('question_ph') ?>" required></textarea></div>
            <div class="form-group"><label><?= __('option1') ?> <span class="required">*</span></label><input type="text" name="option1" class="form-control" required></div>
            <div class="form-group"><label><?= __('option2') ?> <span class="required">*</span></label><input type="text" name="option2" class="form-control" required></div>
            <div class="form-group"><label><?= __('option3_optional') ?></label><input type="text" name="option3" class="form-control"></div>
            <p style="font-size:12px;color:#888;margin-bottom:10px"><?= __('poll_warning') ?></p>
            <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> <?= __('btn_add_activate') ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/admin_footer.php'; ?>

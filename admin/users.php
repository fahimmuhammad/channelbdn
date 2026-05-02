<?php
require_once 'auth.php';
requirePermission('users');
$admin_title = __('user_management');

$msg = $err = '';

// Add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $act = $_POST['action'];

    if ($act === 'add') {
        $un   = $conn->real_escape_string(trim($_POST['username']));
        $em   = $conn->real_escape_string(trim($_POST['email']));
        $fn   = $conn->real_escape_string(trim($_POST['full_name']));
        $role = $conn->real_escape_string($_POST['role']);
        $pw   = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        $roles_allowed = ['admin','editor','reporter','moderator','ad_manager'];
        if (!in_array($role, $roles_allowed)) {
            $err = __('invalid_role');
        } elseif ($conn->query("INSERT INTO users (username,email,password,full_name,role) VALUES ('$un','$em','$pw','$fn','$role')")) {
            logActivity('add_user', "নতুন ব্যবহারকারী যোগ: $un ($role)");
            $msg = __('user_added');
        } else {
            $err = __('user_add_failed');
        }
    }

    if ($act === 'toggle') {
        $id = (int)$_POST['id'];
        $conn->query("UPDATE users SET is_active = 1 - is_active WHERE id=$id");
        logActivity('toggle_user', "ব্যবহারকারী আইডি $id স্ট্যাটাস পরিবর্তন");
        $msg = __('status_updated_msg');
    }

    if ($act === 'delete') {
        $id = (int)$_POST['id'];
        if ($id === (int)$_SESSION['admin_id']) {
            $err = __('cannot_del_self');
        } else {
            $conn->query("DELETE FROM users WHERE id=$id");
            logActivity('delete_user', "ব্যবহারকারী আইডি $id মুছে ফেলা হয়েছে");
            $msg = __('user_deleted_msg');
        }
    }

    if ($act === 'edit') {
        $id   = (int)$_POST['id'];
        $fn   = $conn->real_escape_string(trim($_POST['full_name']));
        $em   = $conn->real_escape_string(trim($_POST['email']));
        $role = $conn->real_escape_string($_POST['role']);
        $roles_allowed = ['admin','editor','reporter','moderator','ad_manager'];
        if (!in_array($role, $roles_allowed)) {
            $err = __('invalid_role');
        } else {
            $sql = "UPDATE users SET full_name='$fn', email='$em', role='$role'";
            if (!empty(trim($_POST['password']))) {
                $pw = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
                $sql .= ", password='$pw'";
            }
            $conn->query($sql . " WHERE id=$id");
            logActivity('edit_user', "ব্যবহারকারী আইডি $id সম্পাদনা ($role)");
            $msg = __('user_updated_msg');
        }
    }
}

$users = $conn->query("SELECT * FROM users ORDER BY role, username")->fetch_all(MYSQLI_ASSOC);

$role_labels = [
    'admin'       => ['label' => __('role_admin'),      'class' => 'role-admin'],
    'editor'      => ['label' => __('role_editor'),     'class' => 'role-editor'],
    'reporter'    => ['label' => __('role_reporter'),   'class' => 'role-reporter'],
    'moderator'   => ['label' => __('role_moderator'),  'class' => 'role-moderator'],
    'ad_manager'  => ['label' => __('role_ad_manager'), 'class' => 'role-ad_manager'],
];

include 'includes/admin_header.php';
?>

<div class="admin-main">
  <h1 class="page-title"><i class="fas fa-users"></i> <?= __('user_management') ?></h1>

  <?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($err) ?></div><?php endif; ?>

  <!-- Add user form -->
  <div class="admin-card" style="margin-bottom:28px">
    <div class="admin-card-header"><h3><i class="fas fa-user-plus"></i> <?= __('add_user') ?></h3></div>
    <div class="admin-card-body">
      <form method="POST" class="row-form">
        <input type="hidden" name="action" value="add">
        <div class="form-row">
          <div class="form-col">
            <label><?= __('username') ?> *</label>
            <input type="text" name="username" required placeholder="username">
          </div>
          <div class="form-col">
            <label><?= __('full_name') ?></label>
            <input type="text" name="full_name" placeholder="Full Name">
          </div>
          <div class="form-col">
            <label><?= __('email') ?> *</label>
            <input type="email" name="email" required placeholder="email@example.com">
          </div>
        </div>
        <div class="form-row">
          <div class="form-col">
            <label><?= __('password') ?> *</label>
            <input type="password" name="password" required placeholder="min 8 characters">
          </div>
          <div class="form-col">
            <label><?= __('role') ?> *</label>
            <select name="role" required>
              <option value="reporter"><?= __('role_reporter') ?></option>
              <option value="editor"><?= __('role_editor') ?></option>
              <option value="moderator"><?= __('role_moderator') ?></option>
              <option value="ad_manager"><?= __('role_ad_manager') ?></option>
              <option value="admin"><?= __('role_admin') ?></option>
            </select>
          </div>
          <div class="form-col" style="display:flex;align-items:flex-end">
            <button type="submit" class="btn-primary" style="width:100%"><i class="fas fa-plus"></i> <?= __('btn_add') ?></button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Users table -->
  <div class="admin-card">
    <div class="admin-card-header"><h3><i class="fas fa-list"></i> <?= __('all_users') ?> (<?= count($users) ?>)</h3></div>
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>#</th><th><?= __('col_name_username') ?></th><th><?= __('email') ?></th><th><?= __('role') ?></th>
            <th><?= __('col_status') ?></th><th><?= __('col_last_login') ?></th><th><?= __('col_actions') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): $rl = $role_labels[$u['role']] ?? ['label'=>$u['role'],'class'=>'']; ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td>
              <strong><?= htmlspecialchars($u['full_name'] ?: $u['username']) ?></strong><br>
              <small style="color:#888">@<?= htmlspecialchars($u['username']) ?></small>
            </td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><span class="role-badge <?= $rl['class'] ?>"><?= $rl['label'] ?></span></td>
            <td>
              <form method="POST" style="display:inline">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <button type="submit" class="btn-status <?= $u['is_active'] ? 'active' : 'inactive' ?>" title="<?= $u['is_active'] ? __('activate_btn') : __('deactivate_btn') ?>">
                  <i class="fas fa-<?= $u['is_active'] ? 'check-circle' : 'times-circle' ?>"></i>
                  <?= $u['is_active'] ? __('status_active') : __('status_inactive') ?>
                </button>
              </form>
            </td>
            <td style="font-size:12px;color:var(--text-muted)">
              <?= $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : '—' ?>
            </td>
            <td>
              <button type="button" class="btn-sm edit" onclick="openEdit(<?= htmlspecialchars(json_encode($u)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <?php if ($u['id'] != $_SESSION['admin_id']): ?>
              <form method="POST" style="display:inline" onsubmit="return confirm('<?= __('confirm_del_user') ?>')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <button type="submit" class="btn-sm delete"><i class="fas fa-trash"></i></button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Edit modal -->
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center">
  <div class="admin-modal-inner">
    <h3 style="margin-bottom:20px;font-size:17px"><i class="fas fa-user-edit"></i> <?= __('edit_user') ?></h3>
    <form method="POST">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_id">
      <div class="form-row">
        <div class="form-col">
          <label><?= __('full_name') ?></label>
          <input type="text" name="full_name" id="edit_full_name">
        </div>
        <div class="form-col">
          <label><?= __('email') ?> *</label>
          <input type="email" name="email" id="edit_email" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-col">
          <label><?= __('role') ?> *</label>
          <select name="role" id="edit_role" required>
            <option value="reporter"><?= __('role_reporter') ?></option>
            <option value="editor"><?= __('role_editor') ?></option>
            <option value="moderator"><?= __('role_moderator') ?></option>
            <option value="ad_manager"><?= __('role_ad_manager') ?></option>
            <option value="admin"><?= __('role_admin') ?></option>
          </select>
        </div>
        <div class="form-col">
          <label><?= __('new_password_opt') ?></label>
          <input type="password" name="password" placeholder="<?= __('leave_blank_pw') ?>">
        </div>
      </div>
      <div style="display:flex;gap:12px;margin-top:20px">
        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> <?= __('btn_save') ?></button>
        <button type="button" onclick="closeEdit()" class="btn-cancel"><?= __('btn_cancel') ?></button>
      </div>
    </form>
  </div>
</div>

<style>
.form-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px; }
.form-col label { display: block; font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; }
.form-col input, .form-col select { width: 100%; padding: 9px 12px; border: 1.5px solid #ddd; border-radius: 6px; font-family: var(--font); font-size: 13px; }
.form-col input:focus, .form-col select:focus { border-color: var(--primary); outline: none; }
</style>

<script>
function openEdit(u) {
  document.getElementById('edit_id').value = u.id;
  document.getElementById('edit_full_name').value = u.full_name || '';
  document.getElementById('edit_email').value = u.email;
  document.getElementById('edit_role').value = u.role;
  const m = document.getElementById('editModal');
  m.style.display = 'flex';
}
function closeEdit() { document.getElementById('editModal').style.display = 'none'; }
document.getElementById('editModal').addEventListener('click', function(e){ if(e.target===this) closeEdit(); });
</script>

<?php include 'includes/admin_footer.php'; ?>

<?php
require_once __DIR__ . '/../inc/functions.php';
requireAdmin();
$page = 'users';
$title = 'Users - MICKY SHOP Admin';
$pdo = getDb();
$me = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = intval($_POST['user_id'] ?? 0);
    if ($userId !== intval($me['id'])) {
        if ($action === 'toggle_active') {
            $stmt = $pdo->prepare('UPDATE users SET is_active = 1 - is_active WHERE id = ?');
            $stmt->execute([$userId]);
        } elseif ($action === 'change_role') {
            $role = in_array($_POST['role'] ?? '', ['customer', 'trader', 'admin']) ? $_POST['role'] : 'customer';
            $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
            $stmt->execute([$role, $userId]);
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$userId]);
        }
    }
    flash('User updated.');
    header('Location: users.php');
    exit;
}

$users = getAllUsers();
$message = flash();
require __DIR__ . '/../inc/admin_header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2>Users <span class="muted" style="font-size:.9rem;font-weight:400;">(<?php echo count($users); ?>)</span></h2>
        </div>

        <?php if ($message): ?><div class="alert success"><?php echo e($message); ?></div><?php endif; ?>

        <div class="table-wrap">
            <div class="table-scroll">
                <table>
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Products</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>#<?php echo (int)$user['id']; ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <span class="nav-avatar" style="background:var(--primary);"><?php echo e(strtoupper(mb_substr($user['name'], 0, 1))); ?></span>
                                    <strong><?php echo e($user['name']); ?></strong>
                                    <?php if ($user['id'] == $me['id']): ?><span class="badge badge-plain">you</span><?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo e($user['email']); ?></td>
                            <td><?php echo e($user['phone'] ?: '&mdash;'); ?></td>
                            <td>
                                <?php if ($user['id'] == $me['id']): ?>
                                    <span class="badge badge-plain"><?php echo e($user['role']); ?></span>
                                <?php else: ?>
                                    <form method="post" style="display:contents;">
                                        <input type="hidden" name="action" value="change_role">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                                        <select name="role" onchange="this.form.submit()" style="width:auto;padding:6px 10px;">
                                            <?php foreach (['customer', 'trader', 'admin'] as $role): ?>
                                                <option value="<?php echo $role; ?>" <?php echo $user['role'] === $role ? 'selected' : ''; ?>><?php echo $role; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?php echo $user['is_active'] ? 'badge-active' : 'badge-blocked'; ?>"><?php echo $user['is_active'] ? 'active' : 'blocked'; ?></span>
                            </td>
                            <td><?php echo (int)$user['product_count']; ?></td>
                            <td>
                                <?php if ($user['id'] != $me['id']): ?>
                                <div class="flex" style="gap:6px;flex-wrap:nowrap;">
                                    <form method="post" style="display:contents;">
                                        <input type="hidden" name="action" value="toggle_active">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                                        <button class="icon-link" type="submit"><?php echo $user['is_active'] ? 'Block' : 'Unblock'; ?></button>
                                    </form>
                                    <form method="post" style="display:contents;" onsubmit="return confirm('Delete this user? Their orders and messages will be removed too.');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                                        <button class="icon-link danger" type="submit">Delete</button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../inc/admin_footer.php'; ?>

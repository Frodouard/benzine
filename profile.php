<?php
require 'config.php';

// Protect this page
if (!isLoggedIn()) {
    redirect('login.html');
}

$user = currentUser();

if (!$user) {
    // Session exists but user not found
    session_destroy();
    redirect('login.html');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 40px 20px;
    }
    .container { max-width: 700px; margin: 0 auto; }
    .card {
      background: white;
      border-radius: 16px;
      padding: 40px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }
    h1 { color: #2d3748; margin-bottom: 8px; }
    .subtitle { color: #718096; margin-bottom: 30px; }
    .info-row {
      display: flex;
      padding: 14px 0;
      border-bottom: 1px solid #e2e8f0;
    }
    .info-row:last-child { border-bottom: none; }
    .label { width: 140px; font-weight: 600; color: #4a5568; }
    .value { color: #2d3748; }
    .actions {
      margin-top: 30px;
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }
    .btn {
      padding: 12px 24px;
      border-radius: 10px;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      transition: 0.2s;
    }
    .btn-primary {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
    }
    .btn-danger {
      background: #e53e3e;
      color: white;
      border: none;
      cursor: pointer;
    }
    .btn:hover { opacity: 0.9; transform: translateY(-1px); }
    .avatar {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 32px;
      font-weight: bold;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="avatar">
        <?= strtoupper(substr($user['fullname'], 0, 1)) ?>
      </div>

      <h1>Welcome, <?= htmlspecialchars($user['fullname']) ?>!</h1>
      <p class="subtitle">This is your profile page</p>

      <div class="info-row">
        <div class="label">Username</div>
        <div class="value"><?= htmlspecialchars($user['username']) ?></div>
      </div>
      <div class="info-row">
        <div class="label">Email</div>
        <div class="value"><?= htmlspecialchars($user['email']) ?></div>
      </div>
      <div class="info-row">
        <div class="label">Phone</div>
        <div class="value"><?= htmlspecialchars($user['phone'] ?: '-') ?></div>
      </div>
      <div class="info-row">
        <div class="label">Gender</div>
        <div class="value"><?= htmlspecialchars($user['gender'] ?: '-') ?></div>
      </div>
      <div class="info-row">
        <div class="label">Joined</div>
        <div class="value"><?= htmlspecialchars($user['registered_at']) ?></div>
      </div>

      <div class="actions">
        <a href="index.html" class="btn btn-primary">Back to Home</a>
        <a href="logout.php" class="btn btn-primary" style="background:#4a5568;">Logout</a>
        <button class="btn btn-danger" onclick="deleteAccount()">Delete My Account</button>
      </div>
    </div>
  </div>

  <script>
    async function deleteAccount() {
      if (!confirm('Are you sure you want to delete your account? This cannot be undone.')) {
        return;
      }

      const res = await fetch('delete_user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: '<?= $user['id'] ?>' })
      });

      const data = await res.json();

      if (data.success) {
        alert('Account deleted successfully.');
        window.location.href = 'login.html';
      } else {
        alert(data.message || 'Failed to delete account');
      }
    }
  </script>
</body>
</html>
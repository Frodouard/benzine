<?php
require_once __DIR__ . '/../inc/functions.php';
requireAdmin();
$page = 'messages';
$title = 'Messages - MICKY SHOP Admin';
$pdo = getDb();
$adminId = $_SESSION['user']['id'];
$selectedId = intval($_GET['user'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['message'] ?? ''))) {
    $customerId = intval($_POST['customer_id'] ?? 0);
    if ($customerId) {
        sendMessage($adminId, $customerId, $_POST['message']);
        header('Location: messages.php?user=' . $customerId);
        exit;
    }
}

$customers = $pdo->prepare(
    'SELECT u.id, u.name, u.email, MAX(m.created_at) AS last_message
     FROM messages m
     JOIN users u ON u.id IN (m.sender_id, m.receiver_id)
     WHERE (m.sender_id = ? OR m.receiver_id = ?) AND u.id != ?
     GROUP BY u.id, u.name, u.email
     ORDER BY last_message DESC'
);
$customers->execute([$adminId, $adminId, $adminId]);
$customers = $customers->fetchAll();

$conversation = [];
if ($selectedId) {
    $conversation = getConversation($adminId, $selectedId);
}

function renderThread($messages, $adminId) {
    $html = '';
    foreach ($messages as $m) {
        $mine = $m['sender_id'] == $adminId;
        $class = $mine ? 'chat-bubble mine' : 'chat-bubble';
        $name = $mine ? 'Admin' : 'Customer';
        $html .= '<div class="' . $class . '"><span class="chat-sender">' . e($name) . '</span>'
               . '<span class="chat-text">' . e($m['message']) . '</span>'
               . '<span class="chat-time">' . e($m['created_at']) . '</span></div>';
    }
    return $html;
}

if (!empty($_GET['partial'])) {
    header('Content-Type: text/html; charset=utf-8');
    echo renderThread($conversation, $adminId);
    exit;
}
require __DIR__ . '/../inc/admin_header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2>Customer conversations</h2>
        </div>

        <?php if (!$customers): ?>
            <div class="empty-state">
                <div class="e-ico">💬</div>
                <h3>No conversations yet</h3>
                <p>Messages from the contact page or support chat will appear here.</p>
            </div>
        <?php else: ?>
            <div class="chat-layout">
                <ul class="chat-customers">
                    <?php foreach ($customers as $c): ?>
                        <li>
                            <a href="messages.php?user=<?php echo (int)$c['id']; ?>" <?php echo $c['id'] === $selectedId ? 'class="active"' : ''; ?>>
                                <span>
                                    <strong><?php echo e($c['name']); ?></strong>
                                    <span class="sub"><?php echo e($c['email']); ?></span>
                                </span>
                                <span class="sub"><?php echo e($c['last_message']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($selectedId): ?>
                    <div class="chat-box">
                        <div class="chat-head">
                            <span class="dot"></span>
                            <div><h3><?php echo e($conversation ? 'Reply to ' : ''); ?>Customer</h3><p>Real-time conversation</p></div>
                        </div>
                        <div id="chat-thread" class="chat-thread"><?php echo renderThread($conversation, $adminId); ?></div>
                        <form method="post" class="chat-form">
                            <input type="hidden" name="customer_id" value="<?php echo (int)$selectedId; ?>">
                            <input type="text" name="message" placeholder="Reply..." required autocomplete="off">
                            <button class="btn" type="submit">Send</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/../inc/admin_footer.php'; ?>
<script>
(function () {
    function loadMessages() {
        fetch('messages.php?partial=1&user=<?php echo $selectedId; ?>', { headers: { 'Cache-Control': 'no-cache' } })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var t = document.getElementById('chat-thread');
                if (t && t.innerHTML !== html) {
                    t.innerHTML = html;
                    t.scrollTop = t.scrollHeight;
                }
            })
            .catch(function () {});
    }
    setInterval(loadMessages, 3000);
    var t = document.getElementById('chat-thread');
    if (t) t.scrollTop = t.scrollHeight;
})();
</script>

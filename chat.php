<?php
require_once __DIR__ . '/inc/functions.php';
requireLogin();
$page = 'chat';
$title = 'Chat with MICKY SHOP';
$user = $_SESSION['user'];
$adminId = getAdminId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['message'] ?? ''))) {
    sendMessage($user['id'], $adminId, $_POST['message']);
    header('Location: chat.php');
    exit;
}

$messages = getConversation($user['id'], $adminId);

if (!empty($_GET['partial'])) {
    header('Content-Type: text/html; charset=utf-8');
    echo renderThread($messages, $user['id']);
    exit;
}

function renderThread($messages, $userId) {
    $html = '';
    foreach ($messages as $m) {
        $mine = $m['sender_id'] == $userId;
        $class = $mine ? 'chat-bubble mine' : 'chat-bubble';
        $name = $mine ? 'You' : 'MICKY SHOP';
        $html .= '<div class="' . $class . '"><span class="chat-sender">' . e($name) . '</span>'
               . '<span class="chat-text">' . e($m['message']) . '</span>'
               . '<span class="chat-time">' . e($m['created_at']) . '</span></div>';
    }
    return $html;
}
require __DIR__ . '/inc/header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2>Chat with MICKY SHOP</h2>
            <span class="badge badge-success">Online</span>
        </div>

        <div class="chat-box">
            <div class="chat-head">
                <span class="dot"></span>
                <div>
                    <h3>MICKY SHOP Support</h3>
                    <p>Typically replies within a few minutes</p>
                </div>
            </div>
            <div id="chat-thread" class="chat-thread"><?php echo renderThread($messages, $user['id']); ?></div>
            <form id="chat-form" method="post" class="chat-form">
                <input type="text" name="message" placeholder="Type a message..." required autocomplete="off">
                <button class="btn" type="submit">Send</button>
            </form>
        </div>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
<script>
(function () {
    function loadMessages() {
        fetch('chat.php?partial=1', { headers: { 'Cache-Control': 'no-cache' } })
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

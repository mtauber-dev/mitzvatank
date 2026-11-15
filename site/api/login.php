<?php
// Logs an admin in so the page can switch to edit mode.
require __DIR__ . '/../data/lib.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); echo '{"ok":false}'; exit; }
$in = json_decode((string)file_get_contents('php://input'), true);
if (!is_array($in)) $in = [];

if (!check_login((string)($in['user'] ?? ''), (string)($in['pass'] ?? ''))) {
    sleep(2);
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'That username or password is not right.']);
    exit;
}

start_admin_session();
session_regenerate_id(true);
$_SESSION['ok'] = true;
$_SESSION['user'] = strtolower(trim((string)$in['user']));
$_SESSION['csrf'] = bin2hex(random_bytes(16));
echo json_encode(['ok' => true, 'csrf' => $_SESSION['csrf']]);

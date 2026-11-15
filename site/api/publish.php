<?php
// Saves edits made on the page, then logs out so the next edit needs the password again.
require __DIR__ . '/../data/lib.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

function out(int $code, array $body): void { http_response_code($code); echo json_encode($body); exit; }

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') out(405, ['ok' => false]);
start_admin_session();
if (empty($_SESSION['ok'])) out(401, ['ok' => false, 'error' => 'You are logged out. Log in again to edit.']);
if (!hash_equals((string)($_SESSION['csrf'] ?? ''), (string)($_SERVER['HTTP_X_CSRF'] ?? ''))) out(403, ['ok' => false, 'error' => 'Please reload the page and log in again.']);

$in = json_decode((string)file_get_contents('php://input'), true);
if (!is_array($in)) out(400, ['ok' => false, 'error' => 'Nothing to publish.']);

$ok = with_lock(function () use ($in) {
    $c = read_json('content.json', []);
    foreach ((array)($in['content'] ?? []) as $k => $v) {
        if (!is_string($k) || !preg_match('/^[a-z0-9]{1,13}$/', $k) || !is_string($v)) continue;
        $v = clean_html($v);
        if ($v === '') unset($c[$k]); else $c[$k] = $v;
    }
    if (count($c) > 400) return false;
    $s = read_json('state.json', []);
    if (isset($in['raised']) && is_numeric($in['raised'])) $s['raised'] = max(0, (int)round((float)$in['raised']));
    if (isset($in['goal']) && is_numeric($in['goal']) && (float)$in['goal'] > 0) $s['goal'] = (int)round((float)$in['goal']);
    $s['updated'] = date('c');
    write_json('content.json', $c);
    write_json('state.json', $s);
    return true;
});

$_SESSION = [];
session_destroy();
if (!$ok) out(400, ['ok' => false, 'error' => 'Too many changes at once.']);
out(200, ['ok' => true]);

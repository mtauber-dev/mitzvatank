<?php
// Public: "Send us a message" form. Saves the message for the admin page and emails it.
require __DIR__ . '/../data/lib.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

function out(int $code, array $body): void { http_response_code($code); echo json_encode($body); exit; }

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') out(405, ['ok' => false]);
$in = json_decode((string)file_get_contents('php://input'), true);
if (!is_array($in)) $in = $_POST;
if (!empty($in['website'])) out(200, ['ok' => true]); // spam trap

$name    = clean($in['name'] ?? '', 80);
$reply   = clean($in['reply'] ?? '', 120);
$message = trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string)($in['message'] ?? '')) ?? '');
$message = function_exists('mb_substr') ? mb_substr($message, 0, 2000) : substr($message, 0, 2000);
if ($name === '' || $reply === '' || $message === '') out(400, ['ok' => false, 'error' => 'Please fill in your name, how to reach you, and a message.']);
if (!rate_ok('contact', 5)) out(429, ['ok' => false, 'error' => 'You\'ve sent a few messages already. Please try again in an hour.']);

$entry = ['id' => bin2hex(random_bytes(6)), 'at' => date('c'), 'name' => $name, 'reply' => $reply, 'message' => $message];
$saved = with_lock(function () use ($entry) {
    $all = read_json('messages.json', []);
    if (count($all) >= 500) return false;
    $all[] = $entry;
    write_json('messages.json', $all);
    return true;
});
if (!$saved) out(429, ['ok' => false, 'error' => 'We have a lot of messages right now. Please try again later.']);

$sent = notify(
    'Mitzvah Tank website: message from ' . $name,
    "New message from the Mitzvah Tank website\n\nName: $name\nReply to: $reply\n\n$message\n\n— Sent " . date('D M j, g:i a') . "\nAll messages: https://mitzvatank.easeintech.com/admin.php\n",
    $reply
);
with_lock(function () use ($entry, $sent) {
    $all = read_json('messages.json', []);
    foreach ($all as &$m) if (($m['id'] ?? '') === $entry['id']) $m['emailed'] = $sent;
    unset($m);
    write_json('messages.json', $all);
});
out(200, ['ok' => true]);

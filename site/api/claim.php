<?php
// Public: saves a "Claim a night" request for the admin to confirm.
require __DIR__ . '/../data/lib.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

function out(int $code, array $body): void { http_response_code($code); echo json_encode($body); exit; }

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') out(405, ['ok' => false]);
$in = json_decode((string)file_get_contents('php://input'), true);
if (!is_array($in)) $in = $_POST;

if (!empty($in['website'])) out(200, ['ok' => true]); // spam trap

$name  = clean($in['name'] ?? '', 80);
$phone = clean($in['phone'] ?? '', 30);
$night = clean($in['night'] ?? '', 40);
$ded   = clean($in['dedication'] ?? '', 120);
$pay   = clean($in['pay'] ?? '', 10);
if (!in_array($pay, ['Zelle', 'Cash'], true)) $pay = 'Zelle';
if ($name === '' || $night === '') out(400, ['ok' => false, 'error' => 'Please write your name and pick a night.']);

$res = with_lock(function () use ($name, $phone, $night, $ded, $pay) {
    $s = read_json('state.json', []);
    $open = false;
    foreach ($s['nights'] ?? [] as $n) {
        if ($n['day'] === $night && trim((string)($n['sponsor'] ?? '')) === '') $open = true;
    }
    if (!$open) return [409, ['ok' => false, 'error' => 'Sorry, that night is already sponsored. Please pick another night.']];
    $claims = read_json('claims.json', []);
    if (count($claims) >= 300) return [429, ['ok' => false, 'error' => 'We have a lot of requests right now. Please text us instead.']];
    $claims[] = [
        'id' => bin2hex(random_bytes(6)),
        'at' => date('c'),
        'name' => $name,
        'phone' => $phone,
        'night' => $night,
        'dedication' => $ded,
        'pay' => $pay,
        'status' => 'new',
    ];
    write_json('claims.json', $claims);
    return [200, ['ok' => true]];
});
out($res[0], $res[1]);

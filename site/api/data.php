<?php
// Public: returns the current total and nights for the page.
require __DIR__ . '/../data/lib.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
$s = read_json('state.json', null);
if (!$s) { http_response_code(404); echo '{}'; exit; }
echo json_encode([
    'goal' => (int)($s['goal'] ?? 2000),
    'raised' => (int)($s['raised'] ?? 0),
    'nights' => array_map(fn($n) => [
        'day' => (string)$n['day'],
        'candle' => (int)$n['candle'],
        'sponsor' => (string)($n['sponsor'] ?? ''),
        'dedication' => (string)($n['dedication'] ?? ''),
    ], $s['nights'] ?? []),
], JSON_UNESCAPED_UNICODE);

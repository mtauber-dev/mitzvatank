<?php
// Shared helpers for the Mitzvah Tank backend. This folder is blocked from the web by .htaccess.
date_default_timezone_set('America/New_York');

const DATA_DIR = __DIR__;

function read_json(string $name, $default) {
    $f = DATA_DIR . '/' . $name;
    if (!is_file($f)) return $default;
    $j = json_decode((string)file_get_contents($f), true);
    return is_array($j) ? $j : $default;
}

function write_json(string $name, $data): void {
    $f = DATA_DIR . '/' . $name;
    $tmp = $f . '.tmp';
    file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
    rename($tmp, $f);
}

function with_lock(callable $fn) {
    $h = fopen(DATA_DIR . '/.lock', 'c');
    flock($h, LOCK_EX);
    try { return $fn(); }
    finally { flock($h, LOCK_UN); fclose($h); }
}

function clean($s, int $max): string {
    $s = trim((string)$s);
    $s = preg_replace('/[\x00-\x1F\x7F]/u', '', $s) ?? '';
    return function_exists('mb_substr') ? mb_substr($s, 0, $max) : substr($s, 0, $max);
}

// Text edited on the page: keep only simple formatting tags, with no attributes.
function clean_html($s): string {
    $s = (string)$s;
    $s = strip_tags($s, '<b><i><em><strong><br><span>');
    $s = preg_replace('/<(b|i|em|strong|br|span)\b[^>]*>/i', '<$1>', $s) ?? '';
    $s = preg_replace('/<\/(b|i|em|strong|span)\b[^>]*>/i', '</$1>', $s) ?? '';
    $s = trim(str_replace("\xC2\xA0", ' ', $s));
    return function_exists('mb_substr') ? mb_substr($s, 0, 5000) : substr($s, 0, 5000);
}

// Email a notice to the address in data/mail.php (kept off the website and out of git).
function notify(string $subject, string $body, string $replyTo = ''): bool {
    $f = DATA_DIR . '/mail.php';
    if (!is_file($f)) return false;
    $cfg = require $f;
    $to = (string)($cfg['to'] ?? '');
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return false;
    $from = (string)($cfg['from'] ?? 'no-reply@mitzvatank.easeintech.com');
    $headers = "From: Mitzvah Tank website <$from>\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) $headers .= "Reply-To: $replyTo\r\n";
    $subject = '=?UTF-8?B?' . base64_encode(str_replace(["\r", "\n"], ' ', $subject)) . '?=';
    return @mail($to, $subject, $body, $headers, '-f' . $from);
}

// At most $max requests per hour from one visitor for a given form.
function rate_ok(string $bucket, int $max): bool {
    $ip = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . $bucket);
    return with_lock(function () use ($ip, $max) {
        $r = read_json('ratelimit.json', []);
        $now = time();
        foreach ($r as $k => $times) { $r[$k] = array_values(array_filter((array)$times, fn($t) => $t > $now - 3600)); if (!$r[$k]) unset($r[$k]); }
        $mine = $r[$ip] ?? [];
        if (count($mine) >= $max) { write_json('ratelimit.json', $r); return false; }
        $mine[] = $now; $r[$ip] = $mine;
        write_json('ratelimit.json', $r);
        return true;
    });
}

function start_admin_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Strict']);
    session_start();
}

function check_login(string $user, string $pass): bool {
    $users = require DATA_DIR . '/users.php';
    $rec = $users[strtolower(trim($user))] ?? null;
    if (!$rec) { hash('sha256', 'x' . $pass); return false; }
    return hash_equals($rec['hash'], hash('sha256', $rec['salt'] . $pass));
}

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

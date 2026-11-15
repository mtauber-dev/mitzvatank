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

// ---- Email: settings live in data/mail.php (blocked from the web, kept out of git) ----
function mail_cfg(): array {
    $f = DATA_DIR . '/mail.php';
    $c = is_file($f) ? (require $f) : [];
    return array_merge(['to' => '', 'smtp_host' => 'smtp.hostinger.com', 'smtp_port' => 465, 'smtp_user' => '', 'smtp_pass' => ''], is_array($c) ? $c : []);
}

function save_mail_cfg(array $c): void {
    $f = DATA_DIR . '/mail.php';
    file_put_contents($f, "<?php\n// Email settings for the Mitzvah Tank site. Not in git.\nreturn " . var_export($c, true) . ";\n", LOCK_EX);
    if (function_exists('opcache_invalidate')) @opcache_invalidate($f, true);
}

// Send through an authenticated mailbox (Hostinger Business Email). Returns '' on success, or what went wrong.
function smtp_send(array $c, string $to, string $subject, string $body, string $replyTo = ''): string {
    $user = (string)$c['smtp_user']; $pass = (string)$c['smtp_pass'];
    if ($user === '' || $pass === '') return 'No mailbox is set up yet.';
    $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true]]);
    $fp = @stream_socket_client('ssl://' . $c['smtp_host'] . ':' . (int)$c['smtp_port'], $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) return 'Could not connect to the mail server.';
    stream_set_timeout($fp, 15);
    $read = function () use ($fp) { $d = ''; while (($l = fgets($fp, 1024)) !== false) { $d .= $l; if (strlen($l) < 4 || $l[3] === ' ') break; } return $d; };
    $step = function (?string $line, string $code) use ($fp, $read) { if ($line !== null) fwrite($fp, $line . "\r\n"); return str_starts_with($read(), $code); };
    $err = '';
    if (!$step(null, '220') || !$step('EHLO mitzvatank.easeintech.com', '250')) $err = 'The mail server did not answer.';
    elseif (!$step('AUTH LOGIN', '334') || !$step(base64_encode($user), '334') || !$step(base64_encode($pass), '235')) $err = 'The mailbox address or password is not right.';
    elseif (!$step("MAIL FROM:<$user>", '250') || !$step("RCPT TO:<$to>", '250') || !$step('DATA', '354')) $err = 'The mail server would not accept the message.';
    else {
        $domain = substr(strrchr($user, '@') ?: '@localhost', 1);
        $h = "From: Mitzvah Tank website <$user>\r\nTo: <$to>\r\n"
           . 'Subject: =?UTF-8?B?' . base64_encode(str_replace(["\r", "\n"], ' ', $subject)) . "?=\r\n"
           . 'Date: ' . date('r') . "\r\nMessage-ID: <" . bin2hex(random_bytes(10)) . "@$domain>\r\n"
           . ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL) ? "Reply-To: $replyTo\r\n" : '')
           . "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n";
        if (!$step($h . "\r\n" . rtrim(chunk_split(base64_encode($body), 76, "\r\n")) . "\r\n.", '250')) $err = 'The mail server did not take the message.';
    }
    @fwrite($fp, "QUIT\r\n"); fclose($fp);
    return $err;
}

// Email a notice to the owner. Returns true when the mail server accepted it.
function notify(string $subject, string $body, string $replyTo = ''): bool {
    $c = mail_cfg();
    if (!filter_var($c['to'], FILTER_VALIDATE_EMAIL)) return false;
    return smtp_send($c, $c['to'], $subject, $body, $replyTo) === '';
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

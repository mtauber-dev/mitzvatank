<?php
declare(strict_types=1);
require __DIR__ . '/data/lib.php';
start_admin_session();
header('Cache-Control: no-store, max-age=0');
header('X-Robots-Tag: noindex, nofollow');
header('X-Frame-Options: DENY');

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function ord_s(int $n): string { return $n . ($n === 1 ? 'st' : ($n === 2 ? 'nd' : ($n === 3 ? 'rd' : 'th'))); }

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];
$msg = '';
$action = (string)($_POST['action'] ?? '');

if ($action === 'login') {
    if (check_login((string)($_POST['username'] ?? ''), (string)($_POST['password'] ?? ''))) {
        session_regenerate_id(true);
        $_SESSION['ok'] = true;
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
        header('Location: admin.php');
        exit;
    }
    sleep(2);
    $msg = 'That username or password is not right. Try again.';
}
if ($action === 'logout') { $_SESSION = []; session_destroy(); header('Location: admin.php'); exit; }

$authed = !empty($_SESSION['ok']);

// Email settings: save the mailbox, or send a test email. Handled on their own, before the totals/claims actions.
if ($authed && ($action === 'mailcfg' || $action === 'mailtest')) {
    if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) { http_response_code(400); exit('Please reload the page and try again.'); }
    $mc = mail_cfg();
    $saveOk = true;
    if (isset($_POST['mail_to'])) {
        $to = trim((string)($_POST['mail_to'] ?? ''));
        $user = trim((string)($_POST['smtp_user'] ?? ''));
        $pass = (string)($_POST['smtp_pass'] ?? '');
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) { $msg = 'The "Send messages to" address doesn\'t look right.'; $saveOk = false; }
        elseif ($user !== '' && !filter_var($user, FILTER_VALIDATE_EMAIL)) { $msg = 'The mailbox address doesn\'t look right.'; $saveOk = false; }
        else {
            $mc['to'] = $to; $mc['smtp_user'] = $user;
            if ($pass !== '') $mc['smtp_pass'] = $pass;
            save_mail_cfg($mc);
            $msg = 'Email settings saved. Press "Send test email" to check them.';
        }
    }
    if ($action === 'mailtest' && $saveOk) {
        $err = filter_var($mc['to'], FILTER_VALIDATE_EMAIL) ? smtp_send($mc, $mc['to'], 'Mitzvah Tank website: test email', "This is a test from the Mitzvah Tank website admin page.\n\nIf you're reading this, website messages and sponsor requests will reach you here.\n") : 'Set "Send messages to" first.';
        $msg = $err === '' ? 'Test email sent to ' . $mc['to'] . '. Check that inbox (and Spam).' : 'Test email failed: ' . $err;
    }
    $action = '';
}

if ($authed && $action !== '' && $action !== 'login') {
    if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) { http_response_code(400); exit('Please reload the page and try again.'); }
    $msg = with_lock(function () use ($action) {
        $s = read_json('state.json', []);
        $claims = read_json('claims.json', []);
        $m = '';
        if ($action === 'save') {
            $s['raised'] = max(0, (int)round((float)($_POST['raised'] ?? 0)));
            foreach ($s['nights'] as $i => &$n) {
                $n['sponsor'] = clean($_POST['sponsor'][$i] ?? '', 80);
                $n['dedication'] = clean($_POST['dedication'][$i] ?? '', 120);
            }
            unset($n);
            $m = 'Saved! The website now shows $' . number_format($s['raised']) . '.';
        } elseif ($action === 'confirm' || $action === 'delete') {
            $id = (string)($_POST['id'] ?? '');
            foreach ($claims as $k => $c) {
                if ($c['id'] !== $id) continue;
                if ($action === 'delete') { unset($claims[$k]); $m = 'Request removed.'; break; }
                foreach ($s['nights'] as &$n) {
                    if ($n['day'] === $c['night']) { $n['sponsor'] = $c['name']; $n['dedication'] = $c['dedication']; }
                }
                unset($n);
                $claims[$k]['status'] = 'confirmed';
                if (!empty($_POST['add400'])) $s['raised'] = (int)($s['raised'] ?? 0) + 400;
                $m = $c['night'] . ' is now sponsored by ' . $c['name'] . '.' . (!empty($_POST['add400']) ? ' Added $400 to the total.' : '');
                break;
            }
            $claims = array_values($claims);
        } elseif ($action === 'delmsg') {
            $id = (string)($_POST['id'] ?? '');
            $msgs = array_values(array_filter(read_json('messages.json', []), fn($x) => ($x['id'] ?? '') !== $id));
            write_json('messages.json', $msgs);
            $m = 'Message deleted.';
        }
        $s['updated'] = date('c');
        write_json('state.json', $s);
        write_json('claims.json', $claims);
        return $m;
    });
}

$s = read_json('state.json', ['goal' => 2000, 'raised' => 0, 'nights' => []]);
$claims = array_reverse(read_json('claims.json', []));
$newCount = count(array_filter($claims, fn($c) => ($c['status'] ?? '') === 'new'));
$messages = array_reverse(read_json('messages.json', []));
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Mitzvah Tank Admin</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&display=swap">
<style>
:root{--bg:#faf7f0;--surface:#fff;--ink:#0f1b2d;--muted:#55606e;--gold:#b5770e;--soft:#f3ead6;--line:#e2c88c;--track:#e6e2d8;--ok:#1d7a46;--bad:#b42318}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--ink);font:16px/1.55 "Inter",system-ui,sans-serif}
.wrap{max-width:900px;margin:0 auto;padding:32px max(16px,env(safe-area-inset-left)) 64px;display:flex;flex-direction:column;gap:24px}
h1,h2{font-family:"Inter Tight",sans-serif;letter-spacing:-.03em;margin:0}
h1{font-size:2rem}h2{font-size:1.3rem}
.top{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
.card{background:var(--surface);border:1px solid var(--line);border-radius:18px;padding:22px;display:flex;flex-direction:column;gap:14px}
label{display:flex;flex-direction:column;gap:6px;font-size:.9rem;font-weight:600}
input[type=text],input[type=number],input[type=password]{font:inherit;padding:10px 12px;border:1px solid var(--line);border-radius:10px;background:#fff;color:var(--ink);width:100%}
.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
@media(max-width:620px){.grid{grid-template-columns:1fr}}
fieldset{border:1px solid var(--line);border-radius:14px;padding:14px;display:flex;flex-direction:column;gap:10px;min-width:0}
legend{font-weight:700;padding:0 6px}
.btn{font:600 1rem "Inter",sans-serif;padding:11px 22px;border-radius:999px;border:2px solid var(--ink);background:transparent;color:var(--ink);cursor:pointer;text-decoration:none;display:inline-block}
.btn.gold{background:var(--gold);border-color:var(--gold);color:#fff}
.btn.sm{padding:6px 14px;font-size:.88rem;border-width:1.5px}
.btn:focus-visible,input:focus-visible{outline:3px solid var(--gold);outline-offset:2px}
.msg{background:var(--soft);border:1px solid var(--line);border-radius:12px;padding:12px 16px;font-weight:600}
.msg.bad{color:var(--bad)}
.big{font-family:"JetBrains Mono",monospace;font-weight:700;font-size:2rem;color:var(--gold)}
.muted{color:var(--muted);font-size:.9rem}
.claim{border-top:1px solid var(--track);padding-top:14px;display:flex;flex-direction:column;gap:8px}
.claim:first-of-type{border-top:0;padding-top:0}
.pill{display:inline-block;font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:3px 9px;border-radius:99px;background:var(--soft);color:var(--gold)}
.pill.done{background:#e3f3ea;color:var(--ok)}
.row{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.inline{display:inline-flex;align-items:center;gap:6px;font-weight:500;flex-direction:row}
.login{max-width:380px;margin:12vh auto 0}
</style>
</head>
<body>
<div class="wrap">
<?php if (!$authed): ?>
  <form method="post" class="card login">
    <h1>Mitzvah Tank admin</h1>
    <?php if ($msg): ?><div class="msg bad"><?= h($msg) ?></div><?php endif; ?>
    <label>Username<input type="text" name="username" id="username" autocomplete="username" autocapitalize="none" required autofocus></label>
    <label>Password<input type="password" name="password" id="password" autocomplete="current-password" required></label>
    <input type="hidden" name="action" value="login">
    <button class="btn gold" type="submit">Log in</button>
  </form>
<?php else: ?>
  <div class="top">
    <h1>Mitzvah Tank admin</h1>
    <div class="row">
      <a class="btn sm" href="/" target="_blank" rel="noopener">View website</a>
      <a class="btn sm" href="/#edit">Edit page text</a>
      <form method="post"><input type="hidden" name="action" value="logout"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><button class="btn sm" type="submit">Log out</button></form>
    </div>
  </div>
  <?php if ($msg): ?><div class="msg" role="status"><?= h($msg) ?></div><?php endif; ?>

  <div class="card">
    <h2>Messages <?php if ($messages): ?><span class="pill"><?= count($messages) ?></span><?php endif; ?></h2>
    <?php if (!$messages): ?>
      <p class="muted">No messages yet. When someone uses "Send us a message" on the website, it shows up here and is emailed to you.</p>
    <?php endif; ?>
    <?php foreach ($messages as $c): ?>
      <div class="claim">
        <div class="row"><strong><?= h($c['name']) ?></strong><span class="muted">reply to <span style="font-family:'JetBrains Mono',monospace"><?= h($c['reply']) ?></span></span></div>
        <div style="white-space:pre-wrap"><?= h($c['message']) ?></div>
        <div class="muted">Sent <?= h(date('D M j, g:i a', strtotime($c['at']))) ?><?php if (isset($c['emailed'])): ?> · <?= $c['emailed'] ? 'emailed to you' : 'email could not be sent' ?><?php endif; ?></div>
        <form method="post">
          <input type="hidden" name="action" value="delmsg"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= h($c['id']) ?>">
          <button class="btn sm" type="submit">Delete</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="card">
    <h2>Sponsor requests <?php if ($newCount): ?><span class="pill"><?= $newCount ?> new</span><?php endif; ?></h2>
    <?php if (!$claims): ?>
      <p class="muted">No requests yet. When someone fills in "Claim this night" on the website, it shows up here.</p>
    <?php endif; ?>
    <?php foreach ($claims as $c): ?>
      <div class="claim">
        <div class="row">
          <strong><?= h($c['name']) ?></strong>
          <span class="pill <?= ($c['status'] ?? '') === 'confirmed' ? 'done' : '' ?>"><?= ($c['status'] ?? '') === 'confirmed' ? 'Confirmed' : 'New' ?></span>
        </div>
        <div><?= h($c['night']) ?> · pays by <?= h($c['pay']) ?><?php if ($c['phone'] !== ''): ?> · phone <span style="font-family:'JetBrains Mono',monospace"><?= h($c['phone']) ?></span><?php endif; ?></div>
        <?php if ($c['dedication'] !== ''): ?><div class="muted">Dedication: <?= h($c['dedication']) ?></div><?php endif; ?>
        <div class="muted">Sent <?= h(date('D M j, g:i a', strtotime($c['at']))) ?></div>
        <div class="row">
          <?php if (($c['status'] ?? '') !== 'confirmed'): ?>
          <form method="post" class="row">
            <input type="hidden" name="action" value="confirm"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= h($c['id']) ?>">
            <label class="inline"><input type="checkbox" name="add400" value="1" checked> Add $400 to the total</label>
            <button class="btn sm gold" type="submit">Got the money: mark night sponsored</button>
          </form>
          <?php endif; ?>
          <form method="post">
            <input type="hidden" name="action" value="delete"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= h($c['id']) ?>">
            <button class="btn sm" type="submit">Remove</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <form method="post" class="card">
    <h2>Total and nights</h2>
    <div class="muted">Now showing on the website: <span class="big">$<?= number_format((int)$s['raised']) ?></span> of $<?= number_format((int)$s['goal']) ?></div>
    <label style="max-width:260px">Total raised so far ($)<input type="number" name="raised" id="raised" min="0" step="1" inputmode="numeric" value="<?= (int)$s['raised'] ?>"></label>
    <div class="grid">
      <?php foreach ($s['nights'] as $i => $n): ?>
      <fieldset>
        <legend><?= h($n['day']) ?> · <?= ord_s((int)$n['candle']) ?> candle</legend>
        <label>Sponsor name<input type="text" name="sponsor[<?= $i ?>]" id="sponsor-<?= $i ?>" value="<?= h($n['sponsor']) ?>" placeholder="Leave empty if open"></label>
        <label>Dedication<input type="text" name="dedication[<?= $i ?>]" id="dedication-<?= $i ?>" value="<?= h($n['dedication']) ?>"></label>
      </fieldset>
      <?php endforeach; ?>
    </div>
    <input type="hidden" name="action" value="save"><input type="hidden" name="csrf" value="<?= h($csrf) ?>">
    <div><button class="btn gold" type="submit">Save changes</button></div>
  </form>

  <?php $mc = mail_cfg(); ?>
  <div class="card">
    <h2>Email settings</h2>
    <p class="muted">Website messages and sponsor requests are sent from a Hostinger mailbox to the address below. The password is stored on the server only and is never shown here.</p>
    <form method="post" class="grid">
      <label>Send messages to<input type="text" name="mail_to" id="mail_to" value="<?= h($mc['to']) ?>" autocomplete="off"></label>
      <label>Send from (Hostinger mailbox)<input type="text" name="smtp_user" id="smtp_user" value="<?= h($mc['smtp_user']) ?>" placeholder="website@mitzvatank.easeintech.com" autocomplete="off"></label>
      <label>Mailbox password<input type="password" name="smtp_pass" id="smtp_pass" autocomplete="new-password" placeholder="<?= $mc['smtp_pass'] !== '' ? 'Saved. Leave empty to keep it' : 'Not set yet' ?>"></label>
      <div class="row" style="align-items:flex-end;grid-column:1/-1"><input type="hidden" name="csrf" value="<?= h($csrf) ?>">
        <button class="btn gold" type="submit" name="action" value="mailcfg">Save email settings</button>
        <button class="btn" type="submit" name="action" value="mailtest">Save and send test email</button></div>
    </form>
  </div>
<?php endif; ?>
</div>
</body>
</html>

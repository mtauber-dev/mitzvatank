<?php
require __DIR__ . '/data/lib.php';
header('Cache-Control: no-cache, max-age=0');
$state = read_json('state.json', ['goal' => 2000, 'raised' => 0, 'nights' => []]);
$content = read_json('content.json', []);
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="description" content="Help us bring a Mitzvah Tank to the streets for 4 nights of Chanukah. Goal: $2,000. Give by Zelle or cash, or sponsor a night for $400.">
<meta property="og:title" content="Light the Streets: Chanukah Mitzvah Tank">
<meta property="og:description" content="4 nights of Chanukah mivtzoim. Help us reach $2,000. Zelle or cash, or sponsor a night for $400.">
<meta property="og:type" content="website">
<meta property="og:url" content="https://mitzvatank.easeintech.com/">
<style>:root{color-scheme:light;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}body{margin:0;font:14px system-ui,sans-serif}img{max-width:100%}[hidden]{display:none!important}</style><title>Light the Streets</title>
<meta name="claude:viewport-fit-sides" content="cover">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&display=swap">
<style id="css">
/* Layout: one editorial column on warm paper; a dark night band in the middle; gold is the only accent */
:root{
  --bg:#faf7f0; --surface:#ffffff; --ink:#0f1b2d; --muted:#55606e;
  --gold:#b5770e; --gold-ink:#ffffff; --gold-soft:#f3ead6; --line:#e2c88c; --track:#e6e2d8;
  --night:#0d1524; --night-fg:#f3efe6; --flame:#f6b443;
  --display:"Inter Tight","Inter",system-ui,sans-serif; --body:"Inter",system-ui,sans-serif;
  --mono:"JetBrains Mono",ui-monospace,Consolas,monospace;
}
@media (prefers-color-scheme:dark){:root:not([data-theme="light"]){
  --bg:#0d1524; --surface:#141f33; --ink:#f3efe6; --muted:#a9b2c2;
  --gold:#e7a93a; --gold-ink:#0d1524; --gold-soft:#1c2840; --line:#5c4b25; --track:#26324a;
  --night:#070c17; --night-fg:#f3efe6; color-scheme:dark}}
:root[data-theme="dark"]{
  --bg:#0d1524; --surface:#141f33; --ink:#f3efe6; --muted:#a9b2c2;
  --gold:#e7a93a; --gold-ink:#0d1524; --gold-soft:#1c2840; --line:#5c4b25; --track:#26324a;
  --night:#070c17; --night-fg:#f3efe6; color-scheme:dark}
*{box-sizing:border-box}
body{background:var(--bg);color:var(--ink);font-family:var(--body);font-size:17px;line-height:1.65}
#app{position:relative}
.col{max-width:1040px;margin:0 auto;padding-left:max(16px,env(safe-area-inset-left,0px));padding-right:max(16px,env(safe-area-inset-right,0px))}
section{padding-block:56px}
h1,h2,h3{font-family:var(--display);margin:0;text-wrap:balance;letter-spacing:-.035em;line-height:1.02}
h1{font-size:clamp(3rem,11vw,6.2rem);font-weight:800}
h1 em{font-style:normal;color:var(--gold)}
h2{font-size:clamp(2rem,5.5vw,3.2rem);font-weight:800}
h3{font-size:1.3rem;font-weight:700;letter-spacing:-.02em}
p{margin:0}
.eyebrow{font-size:.75rem;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:var(--gold);display:flex;align-items:center;gap:12px}
.eyebrow.rule::before{content:"";width:32px;height:1px;background:var(--gold)}
.lede{font-size:clamp(1.15rem,2.6vw,1.45rem);font-weight:500;letter-spacing:-.01em}
.intro{color:var(--muted);max-width:60ch}
.stack{display:flex;flex-direction:column;gap:18px}
.btns{display:flex;flex-wrap:wrap;gap:12px}
.btn{display:inline-flex;align-items:center;gap:8px;font:600 1rem var(--body);padding:13px 26px;border-radius:999px;text-decoration:none;cursor:pointer;border:2px solid var(--ink);background:transparent;color:var(--ink)}
.btn.gold{background:var(--gold);border-color:var(--gold);color:var(--gold-ink)}
.btn.sm{padding:8px 16px;font-size:.92rem;border-width:1.5px}
.btn:focus-visible,input:focus-visible,select:focus-visible{outline:3px solid var(--gold);outline-offset:2px}
.btn[disabled]{opacity:.5;cursor:default}

/* hero + meter */
.hero{padding-block:72px 40px}
.meter{background:var(--surface);border:2px solid var(--gold);border-radius:24px;padding:28px clamp(18px,4vw,32px);display:flex;flex-direction:column;gap:18px}
.meter-top{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap}
.big{font-family:var(--mono);font-weight:700;font-size:clamp(2.6rem,7vw,3.6rem);color:var(--gold);line-height:1;font-variant-numeric:tabular-nums}
.of{font-family:var(--mono);font-size:1.25rem;text-align:right}
.tiny{font-size:.72rem;letter-spacing:.2em;text-transform:uppercase;color:var(--muted);margin-top:8px}
.track{position:relative;height:10px;background:var(--track);border-radius:99px;margin-inline:12px}
.track .fill{position:absolute;inset:0 auto 0 0;background:var(--gold);border-radius:99px}
.track .tick{position:absolute;top:0;bottom:0;width:2px;background:var(--bg)}
.track .rv{position:absolute;top:50%;width:28px;height:28px;margin-left:-14px;transform:translateY(-50%);border-radius:50%;background:var(--gold);display:grid;place-items:center;box-shadow:0 0 0 6px color-mix(in srgb,var(--gold) 22%,transparent)}
.meter-bot{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap}
.meter-bot .tiny{margin:0}
.about{max-width:62ch;font-size:1.12rem;color:var(--muted);margin-top:36px}

/* night band */
.night{background:var(--night);color:var(--night-fg);position:relative;overflow:hidden}
.night::before{content:"";position:absolute;inset:0;background:radial-gradient(60% 80% at 85% 30%,rgba(246,180,67,.22),transparent 60%),radial-gradient(40% 60% at 10% 90%,rgba(246,180,67,.10),transparent 60%)}
.night .col{position:relative;padding-block:72px}
.night blockquote{margin:0;font-family:var(--display);font-weight:700;font-size:clamp(1.5rem,4vw,2.4rem);letter-spacing:-.03em;line-height:1.15;max-width:26ch;text-wrap:balance}
.night blockquote span{color:var(--flame)}

/* what we do cards */
.cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;margin-top:12px}
@media (max-width:820px){.cards{grid-template-columns:1fr}}
.card{background:var(--surface);border:1px solid var(--line);border-radius:20px;overflow:hidden;display:flex;flex-direction:column}
.card .art{height:150px;background:radial-gradient(70% 90% at 50% 100%,rgba(246,180,67,.35),transparent 70%),var(--night);display:grid;place-items:center}
.card .art svg{width:110px;height:110px}
.card .txt{padding:20px 22px 24px;display:flex;flex-direction:column;gap:8px}
.card .txt p{color:var(--muted);font-size:.98rem}

/* nights */
.nights{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px}
@media (max-width:920px){.nights{grid-template-columns:repeat(3,minmax(0,1fr))}}@media (max-width:620px){.nights{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:440px){.nights{grid-template-columns:1fr}}
.night-card{background:var(--surface);border:1px solid var(--line);border-radius:18px;padding:18px;display:flex;flex-direction:column;gap:10px}
.night-card .day{font-family:var(--display);font-weight:700;font-size:1.15rem;letter-spacing:-.02em}
.candles{display:flex;gap:4px;align-items:flex-end;height:30px}
.candles i{width:6px;height:18px;border-radius:2px;background:var(--track);position:relative}
.candles i.on{background:var(--gold)}
.candles i.on::after{content:"";position:absolute;left:50%;top:-9px;width:6px;height:8px;margin-left:-3px;border-radius:50% 50% 50% 50%/60% 60% 40% 40%;background:var(--flame)}
.candles i.shamash{height:24px}
.pill{align-self:flex-start;font-size:.75rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;padding:4px 10px;border-radius:99px;background:var(--gold-soft);color:var(--gold)}
.pill.taken{background:var(--gold);color:var(--gold-ink)}
a.pill.open{text-decoration:none;border:1.5px solid var(--gold);cursor:pointer}
a.pill.open:hover,a.pill.open:focus-visible{background:var(--gold);color:var(--gold-ink);outline:none}
.night-card .by{font-size:.92rem;color:var(--muted)}
.night-card .by b{color:var(--ink)}

/* budget */
.budget{background:var(--surface);border:2px solid var(--gold);border-radius:22px;padding:clamp(18px,4vw,30px);display:flex;flex-direction:column;gap:4px;max-width:720px}
.brow{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:6px 16px;align-items:center;padding-block:12px;border-bottom:1px solid var(--track)}
.brow .what{font-weight:600}
.brow .amt{font-family:var(--mono);font-weight:700;font-variant-numeric:tabular-nums;text-align:right}
.brow .share{grid-column:1/-1;height:6px;background:var(--track);border-radius:99px;overflow:hidden}
.brow .share i{display:block;height:100%;background:var(--gold);border-radius:99px}
.brow .pct{font-size:.8rem;color:var(--muted);font-weight:500;margin-left:8px}
.btotal{display:flex;justify-content:space-between;gap:16px;padding-top:16px;font-family:var(--display);font-weight:800;font-size:1.3rem;letter-spacing:-.02em}
.btotal span:last-child{font-family:var(--mono);color:var(--gold)}
.bh{position:absolute;top:calc(14px + env(safe-area-inset-top,0px));right:max(16px,env(safe-area-inset-right,0px));font-size:.85rem;font-weight:600;color:var(--muted)}

/* giving */
.give{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
@media (max-width:760px){.give{grid-template-columns:1fr}}
.way{background:var(--surface);border:1px solid var(--line);border-radius:22px;padding:26px;display:flex;flex-direction:column;gap:12px}
.way .tag{display:flex;align-items:center;gap:12px;font-size:.72rem;letter-spacing:.24em;text-transform:uppercase;color:var(--muted);font-weight:600}
.way .tag span{width:34px;height:34px;border-radius:50%;background:var(--gold-soft);display:grid;place-items:center;color:var(--gold)}
.zelle-qr{width:200px;max-width:100%;height:auto;border-radius:14px;border:1px solid var(--line);background:#fff;image-rendering:pixelated}
.num{font-family:var(--mono);font-size:1.25rem;font-weight:500;user-select:all}
.fine{font-size:.88rem;color:var(--muted)}
.sponsor{margin-top:20px;background:var(--gold-soft);border:1px solid var(--line);border-radius:22px;padding:28px;display:flex;flex-direction:column;gap:20px}
.sponsor-head{display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap;align-items:center}
.sponsor-head p{color:var(--muted);max-width:56ch}

/* form */
form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
@media (max-width:600px){form{grid-template-columns:1fr}}
label{display:flex;flex-direction:column;gap:6px;font-size:.88rem;font-weight:600}
label.wide{grid-column:1/-1}
input,select{font:400 1rem var(--body);padding:11px 14px;border-radius:12px;border:1px solid var(--line);background:var(--surface);color:var(--ink);width:100%}
textarea{font:400 1rem var(--body);padding:11px 14px;border-radius:12px;border:1px solid var(--line);background:var(--surface);color:var(--ink);width:100%;resize:vertical}
.msg{grid-column:1/-1;background:var(--surface);border:1px dashed var(--gold);border-radius:14px;padding:16px;display:flex;flex-direction:column;gap:12px}
.msg pre{margin:0;white-space:pre-wrap;font:500 .95rem var(--body)}
.receipt{margin-top:22px}

/* admin */
.admin{border-top:2px dashed var(--line)}
.admin-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
@media (max-width:600px){.admin-grid{grid-template-columns:1fr}}
.admin fieldset{border:1px solid var(--line);border-radius:14px;padding:14px;display:flex;flex-direction:column;gap:10px;min-width:0}
.admin legend{font-weight:700;padding:0 6px}
.status{font-weight:600;color:var(--gold)}
.fab{position:fixed;right:calc(16px + env(safe-area-inset-right,0px));bottom:calc(16px + env(safe-area-inset-bottom,0px));z-index:5;box-shadow:0 6px 20px rgba(0,0,0,.18)}
footer{padding-block:40px 64px;color:var(--muted);text-align:center;font-size:.95rem}
@media (prefers-reduced-motion:no-preference){.candles i.on::after{animation:fl 1.4s ease-in-out infinite alternate}}
@keyframes fl{to{transform:scaleY(1.2)}}
.edit-entry{padding-block:0 32px;text-align:center}
#edit-link{font-size:.8rem;color:var(--muted);text-decoration:none;opacity:.75}
#edit-link:hover,#edit-link:focus-visible{opacity:1;text-decoration:underline}
.ed-dialog{border:1px solid var(--line);border-radius:18px;padding:0;background:var(--surface);color:var(--ink);width:min(380px,calc(100vw - 32px))}
.ed-dialog::backdrop{background:rgba(13,21,36,.55)}
form.ed-login{display:flex;flex-direction:column;gap:14px;padding:24px}
.ed-err{color:#c2410c;font-size:.9rem;font-weight:600;margin:0}
.ed-bar{position:fixed;left:0;right:0;top:0;z-index:20;background:var(--night);color:var(--night-fg);box-shadow:0 4px 20px rgba(0,0,0,.25);padding-top:env(safe-area-inset-top,0px)}
.ed-inner{max-width:1040px;margin:0 auto;padding:10px max(16px,env(safe-area-inset-right,0px)) 10px max(16px,env(safe-area-inset-left,0px));display:flex;flex-wrap:wrap;align-items:center;gap:10px 16px}
.ed-status{color:var(--flame);font-size:.9rem;flex:1 1 180px}
label.ed-num{flex-direction:row;align-items:center;gap:6px;color:var(--night-fg);font-size:.85rem}
.ed-num input{width:100px;padding:6px 10px}
.ed-actions{display:flex;gap:8px;flex-wrap:wrap}
.btn.ed-ghost{border-color:var(--night-fg);color:var(--night-fg)}
html.editing body{padding-top:130px}
html.editing [data-ek]{outline:1px dashed color-mix(in srgb,var(--gold) 70%,transparent);outline-offset:3px;cursor:text;border-radius:2px}
html.editing [data-ek]:hover{background:color-mix(in srgb,var(--gold) 10%,transparent)}
html.editing [data-ek]:focus{outline:2px solid var(--gold);background:color-mix(in srgb,var(--gold) 12%,transparent)}</style>
</head>
<body>
<template id="tpl">
<div class="bh" lang="he">ב״ה</div>
<header class="col hero stack">
  <div class="eyebrow rule">Chanukah 5787 · December 4–12</div>
  <h1>Light the <em>streets</em></h1>
  <p class="lede">A Mitzvah Tank. Five nights. Every corner we can reach.</p>
  <div class="btns" style="margin-top:8px">
    <a class="btn gold" href="#give">How to give →</a>
    <a class="btn" href="#what">What we do</a>
  </div>
  <div class="meter" style="margin-top:28px" aria-label="Fundraising progress">
    <div class="meter-top">
      <div><div class="big" data-raised>$0</div><div class="tiny">Raised so far</div></div>
      <div><div class="of" data-goal>of $2,000</div><div class="tiny" data-needed>$2,000 still needed</div></div>
    </div>
    <div class="track" data-track>
      <div class="fill" data-fill></div>
      <div class="tick" style="left:25%"></div><div class="tick" style="left:50%"></div><div class="tick" style="left:75%"></div>
      <div class="rv" data-rv><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--gold-ink)"><rect x="2" y="6" width="15" height="10" rx="2"/><path d="M17 9h3l2 3v4h-5"/><circle cx="7" cy="17" r="2"/><circle cx="18" cy="17" r="2"/></svg></div>
    </div>
    <div class="meter-bot"><div class="tiny" data-pct>0% funded</div><div class="tiny">5 nights · 1 Mitzvah Tank</div></div>
  </div>
  <p class="about">We're chasidishe bochurim, teenagers in black hats and short black jackets, who just want to get people to do a mitzvah. So we rent an RV, tape Chanukah signs on both sides, and take it around for five nights of Chanukah, handing out boxes of menorah candles, doughnuts and menorah light to Jews who would never walk into a building to find them. $2,000 covers the whole run: the RV, gas, the signs and printing, the candles and every doughnut we hand out. Anything past the goal goes to the driver and to a fund for students learning Chayenu.</p>
</header>

<div class="night">
  <div class="col">
    <blockquote>Bochurim in black hats rent an RV, tape signs on both sides, and park outside the pizza shop, the gym and the bus stop, <span>and the light comes to them.</span></blockquote>
  </div>
</div>

<section class="col stack" id="what">
  <div class="eyebrow">On the street</div>
  <h2>What five nights with the Mitzvah Tank look like</h2>
  <p class="intro">Mivtzoim, one corner at a time. Nothing fancy: a table, a menorah, doughnuts and a Yid who wants to talk.</p>
  <div class="cards">
    <article class="card">
      <div class="art"><svg viewBox="0 0 100 100" fill="none" stroke="#f6b443" stroke-width="3" stroke-linecap="round"><path d="M50 32v52M34 86h32"/><path d="M50 41L41 32M50 50L32 32M50 59L23 32M50 68L14 32M50 41L59 32M50 50L68 32M50 59L77 32M50 68L86 32"/><path d="M10 32h80" stroke-width="2"/><g fill="#f6b443" stroke="none"><ellipse cx="14" cy="25" rx="2.6" ry="4.5"/><ellipse cx="23" cy="25" rx="2.6" ry="4.5"/><ellipse cx="32" cy="25" rx="2.6" ry="4.5"/><ellipse cx="41" cy="25" rx="2.6" ry="4.5"/><ellipse cx="50" cy="17" rx="2.8" ry="5"/><ellipse cx="59" cy="25" rx="2.6" ry="4.5"/><ellipse cx="68" cy="25" rx="2.6" ry="4.5"/><ellipse cx="77" cy="25" rx="2.6" ry="4.5"/><ellipse cx="86" cy="25" rx="2.6" ry="4.5"/></g><path d="M50 32v-8" stroke-width="2.5"/></svg></div>
      <div class="txt"><h3>Menorahs to take home</h3><p>We give out menorahs so people can light in their own window every night of Chanukah.</p></div>
    </article>
    <article class="card">
      <div class="art"><svg viewBox="0 0 100 100" fill="none" stroke="#f6b443" stroke-width="3.5"><ellipse cx="50" cy="56" rx="34" ry="24"/><ellipse cx="50" cy="52" rx="10" ry="6"/><path d="M22 48c8-8 48-8 56 0" stroke-dasharray="2 6" stroke-linecap="round"/><path d="M50 22v8M40 25l3 6M60 25l-3 6" stroke-linecap="round"/></svg></div>
      <div class="txt"><h3>Doughnuts</h3><p>Fresh doughnuts for everyone who stops by. Nobody says no to a doughnut, and then we get to talk.</p></div>
    </article>
    <article class="card">
      <div class="art"><svg viewBox="0 0 100 100" fill="none" stroke="#f6b443" stroke-width="2.5" stroke-linejoin="round"><path d="M12 34h66v44H12z"/><path d="M12 34l10-8h66l-10 8M78 34l10-8v44l-10 8"/><text x="45" y="45" fill="#f6b443" stroke="none" font-family="Inter,system-ui,sans-serif" font-size="7" font-weight="700" text-anchor="middle" letter-spacing=".5">CHANUKAH</text><text x="45" y="53" fill="#f6b443" stroke="none" font-family="Inter,system-ui,sans-serif" font-size="6" text-anchor="middle" letter-spacing="1">CANDLES</text><rect x="18" y="58" width="54" height="15" rx="1.5" stroke-width="1.5"/><g fill="#f6b443" stroke="none"><rect x="22" y="62" width="3" height="10" rx="1"/><rect x="28" y="62" width="3" height="10" rx="1"/><rect x="34" y="62" width="3" height="10" rx="1"/><rect x="40" y="62" width="3" height="10" rx="1"/><rect x="46" y="62" width="3" height="10" rx="1"/><rect x="52" y="62" width="3" height="10" rx="1"/><rect x="58" y="62" width="3" height="10" rx="1"/><rect x="64" y="62" width="3" height="10" rx="1"/></g><path d="M23.5 62v-2.5M29.5 62v-2.5M35.5 62v-2.5M41.5 62v-2.5M47.5 62v-2.5M53.5 62v-2.5M59.5 62v-2.5M65.5 62v-2.5" stroke-width="1"/></svg></div>
      <div class="txt"><h3>Candle boxes &amp; guides</h3><p>Boxes of candles with a simple guide, so families can light every night of Chanukah, not just the night they met us.</p></div>
    </article>
  </div>
</section>

<section class="col stack" id="nights">
  <div class="eyebrow">The schedule</div>
  <h2>Our five nights</h2>
  <p class="intro">Chanukah is eight nights. We're out with the RV for five of them, Monday through Friday.</p>
  <div class="nights" data-nights></div>
</section>

<section class="col stack" id="budget">
  <div class="eyebrow">Estimated budget</div>
  <h2>Where the $2,000 goes</h2>
  <p class="intro">Every dollar goes into the five nights. Here's the full calculation.</p>
  <div class="budget">
    <div class="brow"><span class="what">RV rental</span><span class="amt">$800</span><div class="share"><i style="width:40%"></i></div></div>
    <div class="brow"><span class="what">Signs &amp; printing</span><span class="amt">$400</span><div class="share"><i style="width:20%"></i></div></div>
    <div class="brow"><span class="what">Doughnuts &amp; refreshments</span><span class="amt">$300</span><div class="share"><i style="width:15%"></i></div></div>
    <div class="brow"><span class="what">Chanukah candles</span><span class="amt">$300</span><div class="share"><i style="width:15%"></i></div></div>
    <div class="brow"><span class="what">Gas</span><span class="amt">$200</span><div class="share"><i style="width:10%"></i></div></div>
    <div class="btotal"><span>Total estimated cost</span><span>$2,000</span></div>
  </div>
</section>

<section class="col stack" id="give">
  <div class="eyebrow">The giving gateway</div>
  <h2>How to keep it on the road</h2>
  <p class="intro">Two ways to give: Zelle or cash. Pick whichever is easiest. Every gift, of any size, goes straight into the run.</p>
  <div class="give">
    <div class="way">
      <div class="tag"><span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="2" width="12" height="20" rx="2"/><path d="M11 18h2"/></svg></span><span>Fastest</span></div>
      <h3>Zelle</h3>
      <p>Scan with your phone's camera to pay with Zelle:</p>
      <img class="zelle-qr" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAWgAAAFoCAYAAAB65WHVAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAACt6SURBVHhe7dTBjuQ2EATR/f+ftq9xCEyiUKSKUvMBcUyKzbXn33/XdV3Xke4f6Ou6rkPdP9DXdV2Hun+gr+u6DnX/QF/XdR3q/oG+rus61P0DfV3Xdaj7B/q6rutQ9w/0dV3Xoe4f6Ou6rkPdP9DXdV2Hun+gr+u6DnX/QF/XdR3q/oG+rus61P0DfV3Xdaj7B/q6rutQ9w/0dV3Xoe4f6Ou6rkPdP9DXdV2Hun+gr+u6DnX/QF/XdR3q/oG+rus61P0DfV3Xdaj7B/q6rutQ9w/0dV3Xoe4f6Ou6rkPdP9DXdV2Hun+gr+u6DnX/QF/XdR3q/oG+rus61P0DfV3Xdaj7B/q6rutQ43+g//379+l2s2+yxDaVEttU6rIzK02zO7EuO5MltvlS08ZvYI/ypXazb7LENpUS21TqsjMrTbM7sS47kyW2+VLTxm9gj/KldrNvssQ2lRLbVOqyMytNszuxLjuTJbb5UtPGb2CP8qV2s2+yxDaVEttU6rIzK02zO7EuO5MltvlS08ZvYI/ypXazb7LENpUS21TqsjMrTbM7sS47kyW2+VLTxm9gj/KldrNvssQ2lRLbVOqyMytNszuxLjuTJbb5UtPGb2CP8qV2s2+yxDaVEttU6rIzK02zO7EuO5MltvlS08ZvYI/ypXazb7LENpUS21TqsjMrTbM7sS47kyW2+VLTxm9gj8JOZ3dmiW1YYhvWZWeu7HR2Z9ZlZ55UYht2OrszmzZ+A3sUdjq7M0tswxLbsC47c2WnszuzLjvzpBLbsNPZndm08RvYo7DT2Z1ZYhuW2IZ12ZkrO53dmXXZmSeV2Iadzu7Mpo3fwB6Fnc7uzBLbsMQ2rMvOXNnp7M6sy848qcQ27HR2ZzZt/Ab2KOx0dmeW2IYltmFddubKTmd3Zl125kkltmGnszuzaeM3sEdhp7M7s8Q2LLEN67IzV3Y6uzPrsjNPKrENO53dmU0bv4E9Cjud3ZkltmGJbViXnbmy09mdWZedeVKJbdjp7M5s2vgN7FHY6ezOLLENS2zDuuzMlZ3O7sy67MyTSmzDTmd3ZtPGb2CPwhLbrCyxDTud3Zl12Zmsy86slNim0m72TbabfZMltllZYhs2bfwG9igssc3KEtuw09mdWZedybrszEqJbSrtZt9ku9k3WWKblSW2YdPGb2CPwhLbrCyxDTud3Zl12Zmsy86slNim0m72TbabfZMltllZYhs2bfwG9igssc3KEtuw09mdWZedybrszEqJbSrtZt9ku9k3WWKblSW2YdPGb2CPwhLbrCyxDTud3Zl12Zmsy86slNim0m72TbabfZMltllZYhs2bfwG9igssc3KEtuw09mdWZedybrszEqJbSrtZt9ku9k3WWKblSW2YdPGb2CPwhLbrCyxDTud3Zl12Zmsy86slNim0m72TbabfZMltllZYhs2bfwG9igssc3KEtuw09mdWZedybrszEqJbSrtZt9ku9k3WWKblSW2YdPGb2CPwhLbrCyxDUtss7LENiyxzZN12Zkssc2b6rIzWWKblSW2YdPGb2CPwhLbrCyxDUtss7LENiyxzZN12Zkssc2b6rIzWWKblSW2YdPGb2CPwhLbrCyxDUtss7LENiyxzZN12Zkssc2b6rIzWWKblSW2YdPGb2CPwhLbrCyxDUtss7LENiyxzZN12Zkssc2b6rIzWWKblSW2YdPGb2CPwhLbrCyxDUtss7LENiyxzZN12Zkssc2b6rIzWWKblSW2YdPGb2CPwhLbrCyxDUtss7LENiyxzZN12Zkssc2b6rIzWWKblSW2YdPGb2CPwhLbrCyxDUtss7LENiyxzZN12Zkssc2b6rIzWWKblSW2YdPGb2CPwhLbrCyxDUtss7LENiyxzZN12Zkssc2b6rIzWWKblSW2YdPGb2CPwhLbrCyxDeuyM9nX2W9e2W72TTbN7sQS27DENitLbMOmjd/AHoUltllZYhvWZWeyr7PfvLLd7Jtsmt2JJbZhiW1WltiGTRu/gT0KS2yzssQ2rMvOZF9nv3llu9k32TS7E0tswxLbrCyxDZs2fgN7FJbYZmWJbViXncm+zn7zynazb7JpdieW2IYltllZYhs2bfwG9igssc3KEtuwLjuTfZ395pXtZt9k0+xOLLENS2yzssQ2bNr4DexRWGKblSW2YV12Jvs6+80r282+yabZnVhiG5bYZmWJbdi08RvYo7DENitLbMO67Ez2dfabV7abfZNNszuxxDYssc3KEtuwaeM3sEdhiW1WltiGddmZ7OvsN69s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" alt="Zelle QR code" width="360" height="360">
      <p class="fine"><b>On your phone?</b> Press and hold the QR code and save it to your photos. Then open Zelle in your bank app, tap <b>Scan QR code</b>, and pick the photo.</p>
      <p class="fine">Add "Tank" and your name in the memo so we can thank you properly.</p>
    </div>
    <div class="way">
      <div class="tag"><span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/></svg></span><span>Cash only</span></div>
      <h3>Cash</h3>
      <p>Want to give cash? Send us a message and we'll set it up.</p>
      <div class="btns"><a class="btn sm" href="#contact">Send a message</a></div>
      <p class="fine">No checks. Cash, Zelle, or flag us down on the street any night we're out.</p>
    </div>
  </div>

  <div class="sponsor" id="sponsor">
    <div class="sponsor-head">
      <div class="stack" style="gap:8px"><h3 style="font-size:1.6rem">Sponsor a whole night: $400</h3>
      <p>Covers the RV, the gas, the doughnuts and the candles for one night. We'll put your name or a dedication on that night.</p></div>
    </div>
    <form id="claim-form" novalidate>
      <label><span>Your name</span><input id="claim-name" required autocomplete="name" placeholder="e.g. The Levy family"></label>
      <label><span>Phone (so we can thank you)</span><input id="claim-phone" type="tel" autocomplete="tel" placeholder="Optional"></label>
      <label><span>Which night</span><select id="claim-night" required></select></label>
      <label><span>How you'll pay</span><select id="claim-pay"><option>Zelle</option><option>Cash</option></select></label>
      <label class="wide"><span>Dedication (optional)</span><input id="claim-ded" placeholder="In honor of… / L'ilui nishmas…"></label>
      <input id="claim-web" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px">
      <div class="wide" style="display:flex;flex-direction:column;gap:8px;align-items:flex-start"><button class="btn gold" type="submit" id="claim-btn">Claim this night</button><p class="fine" id="claim-err" role="alert" hidden></p></div>
      <div class="msg" id="claim-msg" hidden>
        <strong id="claim-head"></strong>
        <div id="claim-text-wrap" class="stack" style="gap:12px">
          <pre id="claim-text"></pre>
          <div class="btns"><button class="btn sm gold" type="button" id="copy-msg">Copy message</button><a class="btn sm" id="sms-link" href="#">Open in Messages</a></div>
        </div>
        <p class="fine">Please send $400 with the Zelle QR code above (memo "Tank"), or send us a message below to arrange cash. Once we get it, your name goes up on the night.</p>
      </div>
    </form>
  </div>

  <div class="sponsor contact" id="contact">
    <div class="stack" style="gap:8px"><h3 style="font-size:1.6rem">Send us a message</h3>
    <p>Want to give cash, ask a question, or set up a dedication? Write to us and we'll get back to you.</p></div>
    <form id="contact-form" novalidate>
      <label><span>Your name</span><input id="ct-name" autocomplete="name" required></label>
      <label><span>Email or phone (so we can reply)</span><input id="ct-reply" autocomplete="email" required></label>
      <label class="wide"><span>Message</span><textarea id="ct-msg" rows="4" maxlength="2000" required></textarea></label>
      <input id="ct-web" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px">
      <div class="wide" style="display:flex;flex-direction:column;gap:8px;align-items:flex-start"><button class="btn gold" type="submit" id="ct-btn">Send message</button><p class="fine" id="ct-status" role="status" hidden></p></div>
    </form>
  </div>
  <p class="fine receipt">Need a receipt? Put your name in the Zelle memo, or tell us when you give cash, and we'll send one. And if you want to thank the driver, a tip goes straight to him.</p>
</section>

<footer class="col">Thank you for helping bring the light to the streets.</footer>
<div class="col edit-entry"><a href="#edit" id="edit-link">Edit page</a></div>
</template>
<div id="app"></div>
<script type="application/json" id="state"><?= json_encode($state, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?></script>
<script type="application/json" id="content"><?= json_encode((object)$content, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?></script>
<script id="main">
(function(){
const state = JSON.parse(document.getElementById('state').textContent);
let overrides = {};
try { overrides = JSON.parse(document.getElementById('content').textContent) || {}; } catch (_) {}
const tpl = document.getElementById('tpl');
const app = document.getElementById('app');
const $ = s => app.querySelector(s);
const money = n => '$' + Math.round(n).toLocaleString('en-US');
const ord = n => n + (n===1?'st':n===2?'nd':n===3?'rd':'th');

// ---- every text block on the page gets a stable key, so edits can be saved and re-applied ----
const INLINE = new Set(['B','I','EM','STRONG','BR','SPAN']);
const SKIP = 'script,style,svg,select,option,input,textarea,[data-nights],#claim-msg,#claim-err,#ct-status,[data-raised],[data-needed],[data-pct],[data-goal],[data-track],#edit-link';
const hash = s => { let h = 5381; for (let i = 0; i < s.length; i++) h = ((h << 5) + h + s.charCodeAt(i)) | 0; return (h >>> 0).toString(36); };
const isLeaf = el => el.textContent.trim() !== '' && [...el.querySelectorAll('*')].every(c => INLINE.has(c.tagName));
(function walk(root){
  for (const el of [...root.children]) {
    if (el.matches(SKIP)) continue;
    if (isLeaf(el)) {
      const k = hash(el.tagName + '|' + el.innerHTML.trim());
      el.setAttribute('data-ek', k);
      if (Object.prototype.hasOwnProperty.call(overrides, k)) el.innerHTML = overrides[k];
    } else walk(el);
  }
})(tpl.content);

function copy(text, btn){
  const done = () => { const t = btn.textContent; btn.textContent = 'Copied!'; setTimeout(() => btn.textContent = t, 1500); };
  try { navigator.clipboard.writeText(text).then(done, () => fallback()); } catch (_) { fallback(); }
  function fallback(){ const r = document.createRange(); const el = btn.closest('.way, .msg').querySelector('.num, pre'); r.selectNodeContents(el); const s = getSelection(); s.removeAllRanges(); s.addRange(r); btn.textContent = 'Selected, press Ctrl+C'; }
}

function boot(){
  app.replaceChildren(tpl.content.cloneNode(true));

  // progress
  const pct = Math.max(0, Math.min(state.raised / state.goal, 1));
  $('[data-raised]').textContent = money(state.raised);
  $('[data-goal]').textContent = 'of ' + money(state.goal);
  $('[data-needed]').textContent = state.raised >= state.goal ? 'Goal reached, thank you!' : money(state.goal - state.raised) + ' still needed';
  $('[data-pct]').textContent = Math.floor(pct*100) + '% funded';
  $('[data-fill]').style.width = (pct*100) + '%';
  $('[data-rv]').style.left = (pct*100) + '%';

  // nights
  const box = $('[data-nights]');
  state.nights.forEach(n => {
    const c = document.createElement('div'); c.className = 'night-card';
    const d = document.createElement('div'); d.className = 'day'; d.textContent = n.day;
    const cs = document.createElement('div'); cs.className = 'candles'; cs.setAttribute('aria-label', ord(n.candle) + ' candle');
    for (let i = 0; i < 9; i++) { const k = document.createElement('i'); if (i === 4) k.className = 'on shamash'; else if ((i < 4 ? 8 - i : 8 - i + 1) <= n.candle) k.className = 'on'; cs.appendChild(k); }
    const lbl = document.createElement('div'); lbl.className = 'fine'; lbl.textContent = ord(n.candle) + ' candle';
    let p;
    if (n.sponsor) { p = document.createElement('span'); p.className = 'pill taken'; p.textContent = 'Sponsored'; }
    else {
      p = document.createElement('a'); p.className = 'pill open'; p.href = '#sponsor'; p.textContent = 'Sponsor this night →';
      p.addEventListener('click', e => {
        if (editing) return;
        e.preventDefault();
        const s = $('#claim-night'); if (s) s.value = n.day;
        $('#sponsor').scrollIntoView({behavior: 'smooth', block: 'start'});
        setTimeout(() => { const f = $('#claim-name'); if (f) f.focus({preventScroll: true}); }, 500);
      });
    }
    c.append(d, cs, lbl, p);
    if (n.sponsor) { const by = document.createElement('div'); by.className = 'by'; const b = document.createElement('b'); b.textContent = n.sponsor; by.append('By ', b); if (n.dedication) by.append(document.createElement('br'), n.dedication); c.appendChild(by); }
    box.appendChild(c);
  });

  // claim form
  const sel = $('#claim-night');
  const open = state.nights.filter(n => !n.sponsor);
  if (!open.length) { const o = document.createElement('option'); o.textContent = 'All nights are sponsored!'; sel.appendChild(o); sel.disabled = true; $('#claim-btn').disabled = true; }
  open.forEach(n => { const o = document.createElement('option'); o.value = n.day; o.textContent = n.day + ' (' + ord(n.candle) + ' candle)'; sel.appendChild(o); });
  const btnLabel = $('#claim-btn').innerHTML;
  $('#claim-form').addEventListener('submit', async e => {
    e.preventDefault();
    const nameEl = $('#claim-name'), err = $('#claim-err'), btn = $('#claim-btn');
    const name = nameEl.value.trim();
    err.hidden = true;
    if (!name) { nameEl.focus(); nameEl.setAttribute('aria-invalid','true'); err.textContent = 'Please write your name.'; err.hidden = false; return; }
    nameEl.removeAttribute('aria-invalid');
    const n = state.nights.find(x => x.day === sel.value);
    const ded = $('#claim-ded').value.trim();
    const phone = $('#claim-phone').value.trim();
    const pay = $('#claim-pay').value;
    const txt = 'Hi Yisroel! I want to sponsor the Mitzvah Tank on ' + n.day + ' (' + ord(n.candle) + ' candle) for $400.\nName: ' + name + (phone ? '\nPhone: ' + phone : '') + (ded ? '\nDedication: ' + ded : '') + '\nI\'ll pay by ' + pay + '.';
    btn.disabled = true; btn.textContent = 'Sending…';
    let saved = false, problem = '';
    try {
      const r = await fetch('api/claim.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({name, phone, night: n.day, dedication: ded, pay, website: $('#claim-web').value})});
      const j = await r.json().catch(() => null);
      if (r.ok && j && j.ok) saved = true; else if (j && j.error) problem = j.error;
    } catch (_) {}
    btn.disabled = false; btn.innerHTML = btnLabel;
    if (problem) { err.textContent = problem; err.hidden = false; return; }
    if (saved) {
      $('#claim-head').textContent = 'Thank you, ' + name + '! We got your request for ' + n.day + '. We\'ll be in touch to confirm.';
      $('#claim-text-wrap').hidden = true;
    } else {
      $('#claim-head').textContent = 'Sorry, we couldn\'t save your request. Please send us a message below with the night you want.';
      $('#claim-text-wrap').hidden = true;
    }
    $('#claim-msg').hidden = false;
    $('#claim-msg').scrollIntoView({behavior:'smooth', block:'nearest'});
  });

  app.querySelectorAll('[data-copy]').forEach(b => b.addEventListener('click', () => { if (!editing) copy(b.dataset.copy, b); }));
  $('#copy-msg').addEventListener('click', e => copy($('#claim-text').textContent, e.currentTarget));
  const ctLabel = $('#ct-btn').innerHTML;
  $('#contact-form').addEventListener('submit', async e => {
    e.preventDefault();
    const st = $('#ct-status'), btn = $('#ct-btn');
    const name = $('#ct-name').value.trim(), reply = $('#ct-reply').value.trim(), message = $('#ct-msg').value.trim();
    const say = (t, bad) => { st.textContent = t; st.hidden = false; st.style.color = bad ? '#c2410c' : ''; };
    if (!name || !reply || !message) { say('Please fill in your name, how to reach you, and a message.', true); return; }
    btn.disabled = true; btn.textContent = 'Sending…';
    try {
      const r = await fetch('api/contact.php', {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({name, reply, message, website: $('#ct-web').value})});
      const j = await r.json().catch(() => null);
      if (r.ok && j && j.ok) { $('#contact-form').reset(); say('Thank you! Your message was sent. We\'ll get back to you soon.'); }
      else say((j && j.error) || 'Could not send right now. Please try again in a few minutes.', true);
    } catch (_) { say('Could not reach the server. Check your connection and try again.', true); }
    btn.disabled = false; btn.innerHTML = ctLabel;
  });
}

// ---- admin: log in, edit any text in place, publish ----
let editing = false, csrf = '';
const changed = new Map();
const el = (tag, attrs, ...kids) => { const e = document.createElement(tag); for (const k in attrs || {}) { if (k === 'class') e.className = attrs[k]; else e.setAttribute(k, attrs[k]); } e.append(...kids); return e; };

const dlg = el('dialog', {class: 'ed-dialog', 'aria-labelledby': 'ed-title'});
const loginForm = el('form', {class: 'ed-login', method: 'dialog'});
const uIn = el('input', {id: 'ed-user', type: 'text', autocomplete: 'username', autocapitalize: 'none', required: ''});
const pIn = el('input', {id: 'ed-pass', type: 'password', autocomplete: 'current-password', required: ''});
const lErr = el('p', {class: 'ed-err', role: 'alert'}); lErr.hidden = true;
const lBtn = el('button', {class: 'btn gold', type: 'submit'}, 'Log in to edit');
const lCancel = el('button', {class: 'btn', type: 'button'}, 'Cancel');
loginForm.append(el('h3', {id: 'ed-title'}, 'Edit this page'), el('label', {}, 'Username', uIn), el('label', {}, 'Password', pIn), lErr, el('div', {class: 'btns'}, lBtn, lCancel));
dlg.append(loginForm);
document.body.appendChild(dlg);
lCancel.addEventListener('click', () => dlg.close());

const bar = el('div', {class: 'ed-bar', role: 'region', 'aria-label': 'Editing tools'}); bar.hidden = true;
const raisedIn = el('input', {id: 'ed-raised', type: 'number', min: '0', step: '1', inputmode: 'numeric'});
const goalIn = el('input', {id: 'ed-goal', type: 'number', min: '1', step: '1', inputmode: 'numeric'});
const status = el('span', {class: 'ed-status', role: 'status'}, 'Click any text to change it.');
const pubBtn = el('button', {class: 'btn gold sm', type: 'button'}, 'Publish');
const cancelBtn = el('button', {class: 'btn sm ed-ghost', type: 'button'}, 'Cancel');
const reqLink = el('a', {class: 'btn sm ed-ghost', href: 'admin.php'}, 'Sponsor requests');
bar.append(el('div', {class: 'ed-inner'},
  el('strong', {}, 'Editing'), status,
  el('label', {class: 'ed-num'}, 'Raised $', raisedIn),
  el('label', {class: 'ed-num'}, 'Goal $', goalIn),
  el('div', {class: 'ed-actions'}, reqLink, cancelBtn, pubBtn)));
document.body.appendChild(bar);

function openLogin(){ lErr.hidden = true; pIn.value = ''; if (dlg.showModal) dlg.showModal(); else dlg.setAttribute('open', ''); setTimeout(() => (uIn.value ? pIn : uIn).focus(), 30); }

loginForm.addEventListener('submit', async e => {
  e.preventDefault();
  lBtn.disabled = true; lBtn.textContent = 'Checking…'; lErr.hidden = true;
  try {
    const r = await fetch('api/login.php', {method: 'POST', headers: {'Content-Type': 'application/json'}, credentials: 'same-origin', body: JSON.stringify({user: uIn.value, pass: pIn.value})});
    const j = await r.json().catch(() => null);
    if (r.ok && j && j.ok) { csrf = j.csrf; pIn.value = ''; dlg.close(); enterEdit(); }
    else { lErr.textContent = (j && j.error) || 'Could not log in. Try again.'; lErr.hidden = false; }
  } catch (_) { lErr.textContent = 'Could not reach the server. Check your connection.'; lErr.hidden = false; }
  lBtn.disabled = false; lBtn.textContent = 'Log in to edit';
});

function enterEdit(){
  editing = true; changed.clear();
  document.documentElement.classList.add('editing');
  app.querySelectorAll('[data-ek]').forEach(n => { n.contentEditable = 'true'; });
  raisedIn.value = state.raised; goalIn.value = state.goal;
  bar.hidden = false;
  status.textContent = 'Click any text to change it.';
}
function updateStatus(){ const n = changed.size + (Number(raisedIn.value) !== state.raised ? 1 : 0) + (Number(goalIn.value) !== state.goal ? 1 : 0); status.textContent = n ? n + (n === 1 ? ' change' : ' changes') + ' not published yet' : 'Click any text to change it.'; }

app.addEventListener('input', e => { if (!editing) return; const n = e.target.closest && e.target.closest('[data-ek]'); if (n) { changed.set(n.dataset.ek, n.innerHTML); updateStatus(); } });
raisedIn.addEventListener('input', updateStatus); goalIn.addEventListener('input', updateStatus);
app.addEventListener('click', e => { if (editing && e.target.closest('a,button')) e.preventDefault(); }, true);
app.addEventListener('submit', e => { if (editing) { e.preventDefault(); e.stopImmediatePropagation(); } }, true);
app.addEventListener('keydown', e => {
  if (!editing || !e.target.closest('[data-ek]')) return;
  if (e.key === 'Enter') { e.preventDefault(); document.execCommand('insertLineBreak'); }
  if (e.key === ' ' && e.target.closest('button')) { e.preventDefault(); document.execCommand('insertText', false, ' '); }
}, true);
app.addEventListener('paste', e => { if (!editing || !e.target.closest('[data-ek]')) return; e.preventDefault(); document.execCommand('insertText', false, (e.clipboardData || window.clipboardData).getData('text/plain')); }, true);
window.addEventListener('beforeunload', e => { if (editing && changed.size) { e.preventDefault(); e.returnValue = ''; } });

cancelBtn.addEventListener('click', () => { editing = false; location.reload(); });
pubBtn.addEventListener('click', async () => {
  const raised = Number(raisedIn.value), goal = Number(goalIn.value);
  if (!Number.isFinite(raised) || raised < 0) { status.textContent = 'Type a number for Raised, like 500.'; raisedIn.focus(); return; }
  if (!Number.isFinite(goal) || goal <= 0) { status.textContent = 'Type a number for Goal, like 2000.'; goalIn.focus(); return; }
  pubBtn.disabled = true; status.textContent = 'Publishing…';
  try {
    const r = await fetch('api/publish.php', {method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF': csrf}, credentials: 'same-origin', body: JSON.stringify({content: Object.fromEntries(changed), raised, goal})});
    const j = await r.json().catch(() => null);
    if (r.ok && j && j.ok) { status.textContent = 'Published! Reloading…'; editing = false; setTimeout(() => location.reload(), 600); return; }
    status.textContent = (j && j.error) || 'Could not publish. Try again.';
  } catch (_) { status.textContent = 'Could not reach the server. Your changes are still here; try again.'; }
  pubBtn.disabled = false;
});

boot();
document.getElementById('edit-link').addEventListener('click', e => { e.preventDefault(); openLogin(); });
if (location.hash === '#edit') { history.replaceState(null, '', location.pathname); openLogin(); }
})();
</script>

</body>
</html>

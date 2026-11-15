# Light the Streets: Chanukah Mitzvah Tank

Fundraising site for a Chanukah 5787 Mitzvah Tank (goal $2,000), live at **https://mitzvatank.easeintech.com**.

## What's here

| Folder | What it is |
|---|---|
| `site/` | The live website (PHP, no build step). Deployed to Hostinger `public_html` for `mitzvatank.easeintech.com`. |
| `flyer/` | Printable letter-size flyer (`mitzvah-tank-flyer.html`) and its PDF. The QR code points to the live site. |
| `claude-page/` | The first, private Claude-hosted version of the page. Kept for reference; not linked anywhere. |

## How the site works

- `site/index.php` renders the page. It reads the live total and nights from `data/state.json` and any text edits from `data/content.json`.
- **Edit mode:** click **Edit page** at the bottom of the site and log in. Every text block becomes editable; the bar at the top also sets the amount raised and the goal. **Publish** saves the changes and logs out, so the next edit needs the password again.
- `site/admin.php`: sponsor requests from the "Claim this night" form, plus total and per-night sponsor names.
- `site/api/`: `login.php`, `publish.php` (edit mode), `claim.php` (sponsor form), `data.php` (public totals).
- `site/data/` is blocked from the web by `.htaccess`. Edited text is stripped down to plain text plus `<b> <i> <em> <strong> <br> <span>`.

## Not in git

Kept out on purpose (see `.gitignore`): the admin login (`site/data/users.php`), live data (`state.json`, `content.json`), donors' sponsor requests (`claims.json`), and `.secrets/`. For a fresh server, copy `site/data/state.example.json` to `state.json`, create `content.json` and `claims.json` containing `{}` and `[]`, and create `users.php`:

```php
<?php
return [
    'admin' => ['salt' => '<random hex>', 'hash' => '<sha256 of salt + password>'],
];
```

## Deploying

Upload the changed files under `site/` to the website's `public_html` (Hostinger file manager, or the Hostinger API upload). Don't overwrite `site/data/*.json` or `site/data/mail.php` on the server; those hold the live totals, edits, requests, messages and the email settings saved from the admin page.

## Email

The contact form and sponsor requests are emailed by logging in to a Hostinger mailbox over SMTP (`smtp.hostinger.com:465`). The mailbox address, its password and the "send to" address are set in **Email settings** on `admin.php` and stored in `site/data/mail.php`. Every message is also kept on the admin page.

# CLAUDE.md — Reer & Horten Trafikkskole

Les dette først i hver økt. Full referanse i `README.md`.

## Alltid start med
1. Les `README.md` for arkitektur og oppsett.
2. **Design-fasit er `index.html`.** Rediger ALDRI
   `wordpress/reer-landing/template-landing.php` for hånd — den er generert.

## Fast arbeidsflyt etter enhver endring i `index.html`
1. `python3 wordpress/build-template.py`  ← regenererer malen
2. `php -l wordpress/reer-landing/reer-landing.php` og
   `php -l wordpress/reer-landing/template-landing.php`
3. `cd wordpress && zip -r reer-landing.zip reer-landing`  ← bygg pakken
4. Commit + push.
5. Send `reer-landing.zip` til brukeren og minn om:
   wp-admin → Utvidelser → Legg til → Last opp → «Erstatt nåværende med
   opplastet» → **Clear Cache** → sjekk reer.no i inkognito.

> Endrer du **skjemaet**: speil endringen i `build-template.py` (`new_form`)
> og i `reer-landing.php` (`reer_landing_collect_fields` +
> `reer_landing_send_mail`) hvis et nytt felt skal med i e-posten.

## Må huskes
- Serveren kjører **PHP 5.6.40** → hold koden PHP 5.6-kompatibel (ingen `??`,
  arrow-funksjoner eller type-hints).
- Kontaktskjemaet sender til **reer@reer.no** (`REER_LANDING_SIGNUP_TO`).
- Tidsstempel i e-post skal være **Europe/Oslo**.
- SEO-tittel/beskrivelse styres i **All in One SEO** (koden har kun fallback).
- Tøm **Quick Cache** etter hver publisering.
- Gamle undersider videresendes med **«Page Links To»** (ikke Redirection —
  krever nyere PHP).

## Verifisering (anbefalt)
- Rendre og ta skjermbilde med Playwright/Chromium for å sjekke layout:
  `npm install playwright --no-save`, `executablePath: '/opt/pw-browsers/chromium'`.

## Git
- Utvikle på `claude/wordpress-website-publishing-vrshii`. Commit ofte, push,
  og hold en PR (draft) oppdatert. Er PR-en allerede merget, restart grenen
  fra siste standardgren før nytt arbeid.

# Reer & Horten Trafikkskole — nettsted

Ferdig nettside for **reer.no**, levert som en selvstendig WordPress-plugin.
Dette dokumentet gir en ny økt (eller person) rask oversikt til å håndtere
nye bestillinger og endringer.

---

## Kort om oppsettet (live)

- **reer.no kjører WordPress** (wp-admin på `https://reer.no/wp-admin/`).
- Forsiden vises av plugin-en **«Reer Landingsside»** (mappe `wordpress/reer-landing/`),
  som en fullbredde-sidemal uavhengig av det aktive temaet.
- Forsiden er en WordPress-**side** («Forside 2026») som bruker sidemalen
  «Reer – Forside (fullbredde)», satt som forside under **Innstillinger → Lesing**.
- **Serveren kjører PHP 5.6.40** → all PHP-kode må være 5.6-kompatibel
  (ingen `??`, arrow-funksjoner eller type-hints). Plugin-headeren sier
  `Requires PHP: 5.6`.
- **Kontaktskjemaet** sender e-post til **reer@reer.no** (konstanten
  `REER_LANDING_SIGNUP_TO`) via `wp_mail`. AJAX-innsending med vanlig
  server-POST som fallback. Tidsstempel tvinges til Europe/Oslo.
- **Cache:** Quick Cache — må tømmes («Clear Cache») etter hver opplasting.
- **SEO:** tittel/beskrivelse settes i **All in One SEO** på siden (koden har
  en fallback-kopi i `<title>`/meta).
- **Gamle undersider** videresendes til den nye forsiden med plugin-en
  **«Page Links To»** (per side), fordi moderne redirect-plugins krever nyere
  PHP. Gamle artikkelsider (rundkjøring, oppkjøring osv.) er beholdt.

---

## Filer / arkitektur

| Fil | Rolle |
|---|---|
| `index.html` | **Kilde/fasit for designet.** Én selvstendig HTML-fil; bilder er innebygd som base64. |
| `wordpress/build-template.py` | Genererer WordPress-malen fra `index.html`. **Kjør denne etter hver endring i index.html.** |
| `wordpress/reer-landing/reer-landing.php` | Selve plugin-en: registrerer sidemalen, håndterer skjemaet (server + AJAX) og sender e-post. |
| `wordpress/reer-landing/template-landing.php` | **Generert** av byggeskriptet — rediger aldri for hånd. |
| `wordpress/reer-landing.zip` | Ferdig plugin-pakke som lastes opp i wp-admin. |
| `wordpress/PUBLISERING.md` | Grundig steg-for-steg for publisering, cache og tilbakerulling. |

---

## Slik gjør du en endring (standard arbeidsflyt)

1. **Rediger `index.html`** (design/innhold/priser/tekst).
   - Endrer du **selve skjemaet** (felter/valg): speil endringen i
     `build-template.py` (variabelen `new_form`), og i `reer-landing.php`
     (`reer_landing_collect_fields` + `reer_landing_send_mail`) hvis et nytt
     felt skal med i e-posten.
2. **Regenerer malen:** `python3 wordpress/build-template.py`
3. **Sjekk PHP:** `php -l wordpress/reer-landing/reer-landing.php` og
   `php -l wordpress/reer-landing/template-landing.php`
4. **Bygg zip:** `cd wordpress && zip -r reer-landing.zip reer-landing`
5. **Commit + push.**
6. **Legg live:** wp-admin → Utvidelser → Legg til → Last opp →
   `reer-landing.zip` → «Erstatt nåværende med opplastet» → **Clear Cache** →
   sjekk reer.no i inkognito.

> Forsiden er allerede satt, så steg i «Innstillinger → Lesing» trengs bare
> ved førstegangs publisering (se `PUBLISERING.md`).

---

## Nyttige detaljer

- **Nettleser-testing:** Chromium + Playwright er tilgjengelig for å ta
  skjermbilder og verifisere layout. `npm install playwright --no-save`, og
  bruk `executablePath: '/opt/pw-browsers/chromium'`. (`node_modules/` er
  git-ignorert.)
- **Bilder:** nye bilbilder (med reer.no-skilt) er optimalisert til JPEG
  (~1600px) og innebygd. Nye originaler kan lastes opp i repoet og hentes via
  `git show`, så optimaliseres de før innebygging.
- **Skjemafelt i dag:** navn, mobil, e-post, fødselsdato (kalender), kurs
  (inkl. Klasse B manuell/automat), ønsket lærer, melding, «hvor hørte du om oss».

---

## Grener

Alt arbeid ligger på grenen **`claude/wordpress-website-publishing-vrshii`**
(åpen PR #2). Den nyeste versjonen er her — ikke nødvendigvis på standardgrenen.
En ny økt bør jobbe videre på denne grenen (eller flette PR #2 inn i
standardgrenen først, hvis den skal være den offisielle).

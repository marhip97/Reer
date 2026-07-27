#!/usr/bin/env python3
"""Genererer reer-landing/template-landing.php fra ../index.html.

Kjør på nytt hver gang index.html endres:

    python3 wordpress/build-template.py

Skriptet gjør tre ting med den statiske HTML-en:
  1. Legger inn wp_head() rett før </head> og wp_footer() rett før </body>
     slik at aktive plugins (All in One SEO, cache, WP Translate) fortsatt
     får hektet seg på siden.
  2. Bytter ut det statiske <form>-blokken med en PHP-drevet versjon som
     poster til seg selv, har nonce + honeypot og viser kvittering/feil.
  3. Fjerner den gamle "falske" submit-håndteringen i JS og erstatter den
     med en liten scroll-til-kvittering når skjemaet er sendt.
"""

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
SRC = ROOT / "index.html"
DST = ROOT / "wordpress" / "reer-landing" / "template-landing.php"

html = SRC.read_text(encoding="utf-8")

# --- 1. PHP-hode: kjøresperre + malnavn -------------------------------------
php_header = """<?php
/**
 * Template Name: Reer – Forside (fullbredde)
 *
 * Selvstendig landingsside for Reer & Horten Trafikkskole. Rendres uavhengig
 * av det aktive temaet (lastes via template_include i reer-landing.php).
 *
 * Denne filen er GENERERT av wordpress/build-template.py – ikke rediger
 * den for hånd. Endre index.html og kjør skriptet på nytt.
 *
 * @package Reer_Landing
 */

if ( ! defined( 'ABSPATH' ) ) {
\texit; // Ingen direkte tilgang.
}

$reer_sendt = isset( $_GET['reer_sendt'] ) && '1' === $_GET['reer_sendt'];
$reer_feil  = isset( $_GET['reer_feil'] ) && '1' === $_GET['reer_feil'];
$reer_action = esc_url( remove_query_arg( array( 'reer_sendt', 'reer_feil' ) ) );
?>
"""

if not html.lstrip().startswith("<!DOCTYPE"):
    sys.exit("Uventet start på index.html – forventet <!DOCTYPE>.")

out = php_header + html

# --- 2. wp_head() / wp_footer() ---------------------------------------------
if "wp_head()" not in out:
    out, n = re.subn(r"</head>", "<?php wp_head(); ?>\n</head>", out, count=1)
    if n != 1:
        sys.exit("Fant ikke </head>.")
if "wp_footer()" not in out:
    out, n = re.subn(r"</body>", "<?php wp_footer(); ?>\n</body>", out, count=1)
    if n != 1:
        sys.exit("Fant ikke </body>.")

# --- 3. Bytt ut skjema-blokken ----------------------------------------------
form_pattern = re.compile(
    r'<form class="signup-form" id="signupForm">.*?</form>',
    re.DOTALL,
)

new_form = '''<form class="signup-form" id="signupForm" method="post" action="<?php echo $reer_action; ?>#pamelding">
      <h3>Påmelding</h3>
      <p>Vi svarer innen én virkedag — som regel raskere.</p>
      <div class="form-success<?php echo $reer_sendt ? ' show' : ''; ?>" id="formSuccess">Takk! Vi tar kontakt så snart som mulig.</div>
      <?php if ( $reer_feil ) : ?>
      <div class="form-error show" id="formError">Beklager, noe gikk galt under sendingen. Prøv igjen, eller ring oss på 930&nbsp;20&nbsp;620.</div>
      <?php endif; ?>
      <div class="field">
        <label for="navn">Navn</label>
        <input type="text" id="navn" name="navn" autocomplete="name" required>
      </div>
      <div class="field">
        <label for="telefon">Mobilnummer</label>
        <input type="tel" id="telefon" name="telefon" autocomplete="tel" required>
      </div>
      <div class="field">
        <label for="epost">E-post (valgfritt)</label>
        <input type="email" id="epost" name="epost" autocomplete="email">
      </div>
      <div class="field">
        <label for="kursvalg">Hvilket kurs gjelder det?</label>
        <select id="kursvalg" name="kursvalg">
          <option>Trafikalt grunnkurs — Horten</option>
          <option>Trafikalt grunnkurs — Revetal</option>
          <option>Klasse B</option>
          <option>Klasse BE / B96</option>
          <option>Usikker — hjelp meg å velge</option>
        </select>
      </div>
      <div class="field">
        <label for="laerer">Ønsket kjøreskolelærer</label>
        <select id="laerer" name="laerer">
          <option>Reer finner en ledig lærer</option>
          <option>Andreas Bolstad Hesjedal</option>
          <option>Henrik Finsberg</option>
          <option>Atle Hesjedal</option>
        </select>
      </div>
      <div class="field">
        <label for="melding">Melding (valgfritt)</label>
        <textarea id="melding" name="melding" placeholder="F.eks. når du ønsker å starte"></textarea>
      </div>
      <?php wp_nonce_field( 'reer_signup', 'reer_signup_nonce' ); ?>
      <div class="reer-hp" aria-hidden="true">
        <label>La dette feltet stå tomt
          <input type="text" name="reer_hp" tabindex="-1" autocomplete="off">
        </label>
      </div>
      <button type="submit" class="btn lg primary" style="width:100%;">Send påmelding</button>
      <p class="form-hint">Uforpliktende — vi kontakter deg for å avtale detaljer.</p>
    </form>'''

out, n = form_pattern.subn(lambda _m: new_form, out, count=1)
if n != 1:
    sys.exit("Fant ikke skjema-blokken å bytte ut.")

# --- 4. Bytt ut den gamle JS-submit-håndteringen ----------------------------
script_pattern = re.compile(
    r"<script>\s*document\.getElementById\('signupForm'\).*?</script>",
    re.DOTALL,
)

new_script = '''<script>
  // Skjemaet sendes nå på server (wp_mail). Rull til kvitteringen etter innsending.
  // Vi venter til hele siden (inkl. store bilder) er lastet, ellers regnes
  // feil posisjon ut og man havner på toppen i stedet for ved kvitteringen.
  (function () {
    var params = new URLSearchParams(window.location.search);
    if (params.get('reer_sendt') !== '1' && params.get('reer_feil') !== '1') { return; }
    function scrollToResult() {
      var target = document.getElementById('formError') || document.getElementById('formSuccess');
      if (target) { target.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    }
    if (document.readyState === 'complete') {
      setTimeout(scrollToResult, 150);
    } else {
      window.addEventListener('load', function () { setTimeout(scrollToResult, 150); });
    }
  })();
</script>'''

out, n = script_pattern.subn(lambda _m: new_script, out, count=1)
if n != 1:
    sys.exit("Fant ikke JS-submit-blokken å bytte ut.")

# --- 5. Legg til litt CSS for honeypot + feilmelding ------------------------
extra_css = """  /* Reer landing: honeypot + feilmelding (lagt til av build-template.py) */
  .reer-hp { position: absolute !important; left: -9999px !important; top: auto !important; width: 1px; height: 1px; overflow: hidden; }
  .form-error { display: none; margin-bottom: 18px; padding: 12px 16px; border: 1px solid #d33; border-radius: 10px; background: #fdf2f2; color: #9b1c1c; font-size: 14px; }
  .form-error.show { display: block; }
"""
out, n = re.subn(r"</style>", extra_css + "</style>", out, count=1)
if n != 1:
    sys.exit("Fant ikke </style> for å legge til CSS.")

DST.write_text(out, encoding="utf-8")
print(f"Skrev {DST} ({len(out):,} bytes)")

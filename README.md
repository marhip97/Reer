# Reer & Horten Trafikkskole – nettside

Kildekode for nettsiden til Reer & Horten Trafikkskole.

## Filer

- `index.html` – den komplette nettsiden (selvforsynt: innebygde bilder, egen CSS/JS).
- `wordpress/reer-side.html` – samme side tilpasset innliming i en **Custom HTML-blokk**
  i WordPress (Google Fonts flyttet fra `<link>` til `@import`). Klar til bruk.
- `PUBLISERING.md` – **START HER.** Full steg-for-steg for å publisere i WordPress
  uten nedetid, aktivere skjemaet (WPForms + WP Mail SMTP), og en hand-off-status
  som viser hva som er gjort og hva som gjenstår.

## Status (kort)

Alt materiale er klart i repoet. Selve utførelsen i WordPress (oppdateringer,
skjema, innliming, forside-bytte) gjenstår og er beskrevet som «Master-sekvens»
(Fase 0–6) øverst i `PUBLISERING.md`.

Arbeidsbranch: `claude/reer-wordpress-publish-forms-fhqs72`.

## Deploy (GitHub Pages – alternativ)

Repoet har også en GitHub Pages-workflow (`.github/workflows/pages.yml`) som
publiserer `index.html` ved push til `main`. Dette er et alternativ til
WordPress-ruten; valgt rute er WordPress (se `PUBLISERING.md`).

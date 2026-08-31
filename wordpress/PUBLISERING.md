# Publisere den ferdige nettsiden på reer.no (WordPress)

Denne guiden tar deg steg for steg gjennom å publisere den nye forsiden på
`https://reer.no` via **wp-admin**, med **lav risiko for nedetid** og en tydelig
**tilbakerulling** («exit») du kan bruke når som helst underveis.

Prinsippet er trygt: vi bygger den nye siden **ved siden av** den gamle og bytter
først forside helt til slutt. Frem til det siste steget ser besøkende fortsatt
dagens nettside. Byttet tilbake tar under ett minutt.

---

## Hva du får

Plugin-en **«Reer Landingsside»** (`wordpress/reer-landing.zip`):

- Legger til en sidemal **«Reer – Forside (fullbredde)»** som viser det ferdige
  designet nøyaktig, **uavhengig av det aktive temaet** (temaet ditt røres ikke).
- Påmeldingsskjemaet sender e-post til **reer@reer.no** via WordPress (`wp_mail`),
  med navn, telefon, e-post, kursvalg, ønsket lærer og melding. Innsenderens
  e-post settes som **Svar-til**, så du svarer rett til kunden.
- Spam-beskyttelse: skjult honeypot-felt + WordPress-nonce.

Ingenting av dette overskriver eksisterende innhold. Alt kan slås av ved å
deaktivere plugin-en.

---

## Før du starter (5 min) — sikkerhetsnett

1. **Ta backup.** Har du UpdraftPlus/Jetpack eller backup hos webhotellet: kjør en
   full backup (filer + database) nå. Dette er din ytterste «exit».
2. **Noter dagens forside.** Gå til **Innstillinger → Lesing**. Skriv ned hva som
   står under «Forsiden viser»:
   - enten *«Siste innlegg»*,
   - eller *«En statisk side»* med et bestemt sidenavn.
   Dette er verdien du stiller **tilbake** til hvis du vil angre.
3. **Sjekk at e-post virker.** Send deg selv en test senere (steg 4). Hvis
   WordPress-e-post er upålitelig hos webhotellet, installer et SMTP-plugin
   (f.eks. *WP Mail SMTP* — samme leverandør som WPForms) og koble til en ekte
   postkasse. Se «Hvis skjema-e-post ikke kommer frem» nederst.

---

## Steg 1 — Last opp og aktiver plugin-en

1. wp-admin → **Utvidelser → Legg til ny → Last opp utvidelse**.
2. Velg fila **`reer-landing.zip`** og klikk **Installer nå**.
3. Klikk **Aktiver**.

Ingenting synlig endres for besøkende ennå. Du har bare gjort en ny sidemal
tilgjengelig.

> **Exit her:** Utvidelser → deaktiver «Reer Landingsside». Alt er som før.

---

## Steg 2 — Lag den nye forsiden som en skjult kladd

1. wp-admin → **Sider → Legg til ny**.
2. Tittel: f.eks. **`Forside 2026`** (tittelen vises ikke på selve siden).
3. La innholdet stå **tomt** — designet kommer fra sidemalen.
4. I høyre panel, under **Sideattributter → Mal**, velg
   **«Reer – Forside (fullbredde)»**.
   - *Bruker du blokkredigereren (Gutenberg)?* Klikk på **Side**-fanen i
     innstillingspanelet til høyre; «Mal» ligger der.
   - *Ser du ikke «Mal»?* Åpne «Innstillinger» (tannhjul oppe til høyre).
5. Sett status til **Kladd** (ikke publiser ennå) og klikk **Lagre kladd**.

---

## Steg 3 — Forhåndsvis og kvalitetssjekk (uten at noen ser det)

1. Klikk **Forhåndsvis** på siden. Du ser nå den ferdige forsiden på en egen
   URL — besøkende på reer.no ser fortsatt den gamle siden.
2. Sjekk på både **mobil og PC**:
   - Meny, seksjoner (kurs, priser, ansatte, påmelding), bilder og fonter.
   - At telefonnummer og e-postlenker virker.
3. La denne fanen stå åpen til neste steg.

> **Exit her:** Bare la være å publisere/bytte forside. Ingen har sett noe.

---

## Steg 4 — Test at skjemaet sender e-post til reer@reer.no

1. I forhåndsvisningen: rull til **Påmelding**, fyll ut og send inn et testtilfelle.
2. Du skal se kvitteringen **«Takk! Vi tar kontakt så snart som mulig.»**
3. Sjekk at e-posten kommer til **reer@reer.no** (også i søppelpost).
   - Kommer den ikke: se «Hvis skjema-e-post ikke kommer frem» nederst. Ikke gå
     videre til byttet før e-post er bekreftet.

---

## Steg 5 — Publiser siden (fortsatt ikke forside)

1. Klikk **Publiser** på siden `Forside 2026`.
2. Siden er nå live på sin egen adresse (f.eks. `reer.no/forside-2026`), men
   **forsiden er fortsatt den gamle**. Fint tidspunkt for en siste titt.

---

## Steg 6 — Bytt forside (selve «go live») ⏱️ ~30 sek nedetid = 0

1. wp-admin → **Innstillinger → Lesing**.
2. **Forsiden viser:** velg **«En statisk side»**.
3. **Forside:** velg **`Forside 2026`**.
4. Klikk **Lagre endringer**.
5. Åpne `https://reer.no` i et **privat/inkognito-vindu** (unngå cache) og
   bekreft at den nye siden vises.

**Tøm cache** hvis du bruker Quick Cache (du har den installert):
wp-admin → **Quick Cache → tøm/clear cache**, eventuelt «Clear Cache»-knappen
øverst i verktøylinja. Gjør det samme i eventuell cache hos webhotell/Cloudflare.

🎉 Nå er den nye siden live.

---

## ⏮️ Tilbakerulling (exit) — hvis noe ser feil ut

Velg det minst inngripende som løser problemet:

1. **Bytt forsiden tilbake (raskest, ~30 sek).**
   Innstillinger → Lesing → sett «Forsiden viser» tilbake til det du noterte i
   forberedelsene → Lagre → tøm cache. Gammel side er live igjen umiddelbart.

2. **Deaktiver plugin-en.**
   Utvidelser → deaktiver «Reer Landingsside». Sidemalen forsvinner; `Forside
   2026` faller tilbake til standardvisning. Kombiner gjerne med punkt 1.

3. **Full gjenoppretting.**
   Kun hvis noe større skjer: gjenopprett backupen fra forberedelsene.

Ingen av stegene sletter den gamle siden — den ligger urørt hele veien.

---

## Om oppdateringene i skjermbildet (WordPress + plugins)

Skjermbildet viser **WordPress 7.0.2 tilgjengelig** og **11 oppdateringer**, samt
flere plugins som er gamle/aggressive. Anbefaling:

**Gjør ikke store oppdateringer samtidig som du bytter forside.** Én endring om
gangen gjør det lett å se hva som eventuelt forårsaker feil, og holder
nedetidsrisikoen lav.

Anbefalt rekkefølge:

1. **Først:** publiser den nye forsiden etter denne guiden. Få den stabil.
2. **Deretter, på et rolig tidspunkt og med fersk backup:**
   - Oppdater **WordPress-kjernen** til nyeste (7.0.2). Sikkerhetsoppdateringer
     bør ikke utsettes.
   - Oppdater plugins du faktisk bruker (bl.a. **WPForms**, **All in One SEO**).
   - Test forsiden på nytt etterpå (spesielt skjemaet).

**Rydd i ubrukte plugins — de er den største risikoen her.** Flere av plugin-ene
ser lite brukt ut og øker angreps- og feilflaten:

- Kandidater for **deaktivering/sletting** hvis de ikke brukes bevisst:
  *WOW Slider*, *mTouch Quiz*, *Visual Form Builder* (du har WPForms),
  *Social Media Widget*, *Like & Share*, *Simple Facebook Page Plugin*,
  *Shortcodes Ultimate*, *WP Translate / WP Translate Pro*.
- **Behold** det du faktisk trenger: **WPForms** (skjema), **All in One SEO**
  (SEO), og et **cache**-plugin (Quick Cache — vurder å bytte til en aktivt
  vedlikeholdt cache senere, da Quick Cache er utdatert).
- Vurder et **SMTP-plugin** (*WP Mail SMTP*) for pålitelig e-postlevering.

Deaktiver ett plugin om gangen og sjekk at forsiden fortsatt virker.

> Merk: Den nye forsiden er **ikke avhengig av** disse plugin-ene. Å rydde bort
> ubrukte plugins påvirker ikke det nye designet.

---

## Vedlikehold senere — hvis designet skal endres

Designet ligger i `index.html` i dette repoet. Slik oppdaterer du plugin-en:

```bash
# 1. Rediger index.html
# 2. Regenerer sidemalen:
python3 wordpress/build-template.py
# 3. Lag ny zip:
cd wordpress && zip -r reer-landing.zip reer-landing
```

Last opp den nye `reer-landing.zip` på nytt (WordPress spør om å erstatte den
eksisterende), så er endringene live. Ikke rediger
`reer-landing/template-landing.php` for hånd — den blir overskrevet av skriptet.

---

## Hvis skjema-e-post ikke kommer frem

WordPress-e-post (`wp_mail`) går som standard via serverens `mail()`, som mange
webhotell blokkerer eller lar havne i søppelpost. Løsning:

1. Installer **WP Mail SMTP** (Utvidelser → Legg til ny).
2. Koble til en ekte postkasse (f.eks. reer@reer.no via webhotellets SMTP, eller
   en tjeneste som Brevo/SendGrid).
3. Send en testmelding fra WP Mail SMTP, og test skjemaet på nytt (steg 4).

Mottakeradressen for skjemaet (`reer@reer.no`) er satt i
`wordpress/reer-landing/reer-landing.php` (konstanten `REER_LANDING_SIGNUP_TO`)
om den noen gang skal endres.

---

## Sjekkliste

- [ ] Backup tatt, dagens «Forsiden viser» notert
- [ ] Plugin lastet opp og aktivert
- [ ] Side `Forside 2026` laget med malen «Reer – Forside (fullbredde)»
- [ ] Forhåndsvist OK på mobil og PC
- [ ] Skjema testet – e-post mottatt på reer@reer.no
- [ ] Side publisert
- [ ] Forside byttet under Innstillinger → Lesing
- [ ] Cache tømt og reer.no verifisert i inkognito
- [ ] (Senere) WordPress-kjerne + brukte plugins oppdatert, ubrukte ryddet

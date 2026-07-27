# Publisere ny reer.no i WordPress – uten nedetid

Denne veiledningen er skrevet for deg som skal lime den nye forsiden inn i det
eksisterende WordPress-oppsettet (samme som i skjermbildene: WPForms, All in One
SEO, osv.). Målet er **null nedetid** og et **skjema som faktisk sender innsendinger**.

Den nye siden ligger ferdig i `wordpress/reer-side.html` i dette repoet.

> Kort om ansvarsdeling: Jeg (Claude) har ikke tilgang til WP-login og kan ikke
> klikke inne i WordPress for deg. Jeg har gjort HTML-en klar til å limes inn, og
> stegene under er skrevet slik at du kan gjøre det trygt selv.

---

## STATUS (hand-off) – sist oppdatert 2026-07-27

Bruk dette som utgangspunkt hvis du fortsetter i en ny session.

**Ferdig (ligger i repoet, branch `claude/reer-wordpress-publish-forms-fhqs72`):**
- `wordpress/reer-side.html` – forsiden klar til innliming. Selvforsynt: 4
  innebygde bilder (base64), ingen eksterne filer, Google Fonts via `@import`.
- `PUBLISERING.md` – denne veiledningen.
- SMTP-verdier bekreftet fra cPanel (se Del 2).
- Lærervalg avklart: alt til `reer@reer.no`, valgt lærer som felt i varselet.

**Gjenstår (må gjøres i WordPress – kun eier/du har tilgang):** hele
«Master-sekvensen» under, Fase 0–6. Ingenting av dette er startet ennå.

**Kjente fakta om oppsettet:**
- Hosting: cPanel. Webmail: `webmail.reer.no` (Roundcube). Innboks tilgjengelig.
- SMTP (bekreftet): `mail.reer.no`, SSL, port `465`, brukernavn `reer@reer.no`.
- WordPress har ventende oppdateringer (skjermbilde: 11 totalt, WP 7.0.2, 7 utvidelser).
- Dagens skjema kan være en gammel plugin (f.eks. Visual Form Builder) – ikke
  slett gamle plugins før den nye siden er live.

**Åpne punkter / beslutninger tatt:**
- Skjema løses med **WPForms** (allerede installert), ikke ekstern tjeneste.
- Publisering: lim HTML inn i WordPress-side, bytt statisk forside til slutt.

---

## Master-sekvens – hele prosessen i rekkefølge

Dette er den autoritative rekkefølgen. Detaljer for hvert tema står i Del 1 og
Del 2 lenger ned.

### Fase 0 – Backup (aldri hopp over)
- [ ] cPanel → «Backup» (eller UpdraftPlus) → full backup (filer + database), lastet ned lokalt.

### Fase 1 – Oppdateringer (før du bygger nytt)
- [ ] Oppdater **WPForms** og **WP Mail SMTP** (installer sistnevnte om den mangler) – test forsiden etter.
- [ ] Oppdater resten av utvidelsene, batch for batch, test etter hver.
- [ ] Oppdater WordPress-kjernen (7.0.2) til slutt, test etter.
- [ ] Brekker noe: gjenopprett backup fra Fase 0. **Ikke slett gamle plugins ennå.**

### Fase 2 – Skjema + e-postlevering
- [ ] WP Mail SMTP: Other SMTP, `mail.reer.no` / SSL / `465` / `reer@reer.no` / From `reer@reer.no`.
- [ ] Send test-e-post fra WP Mail SMTP → bekreft mottatt i `reer@reer.no`. Stopp og feilsøk hvis den ikke kommer.
- [ ] WPForms: bygg feltene (Navn, Mobil, E-post valgfritt, Kursvalg, Ønsket lærer, Melding).
- [ ] WPForms Notifications: Send To `reer@reer.no`, `{Ønsket lærer}` med i teksten. Confirmation: takkemelding.

### Fase 3 – Bygg ny side (uten å røre den gamle)
- [ ] Sider → Legg til ny → «Ny forside», full bredde / blank mal.
- [ ] Custom HTML-blokk → lim inn hele `wordpress/reer-side.html`.
- [ ] Sett skjemaseksjonen som en egen **WPForms-blokk** (kortkode virker ikke i Custom HTML).
- [ ] Forhåndsvis mobil + PC. Prøvepåmelding → bekreft mottatt i `reer@reer.no` OG i WPForms → Entries.

### Fase 4 – Go-live
- [ ] Innstillinger → Lesing → Forsiden viser → En statisk side → «Ny forside». Lagre.
- [ ] Tøm cache. Sjekk `reer.no` i inkognito.

### Fase 5 – Rollback (hvis noe er galt)
- [ ] Innstillinger → Lesing → velg gammel forside igjen. Øyeblikkelig, ingen nedetid.

### Fase 6 – Opprydding (rolig, etterpå)
- [ ] Når ny side har fungert en stund: deaktiver ubrukte plugins én om gangen, med sjekk etter hver.

---

## Del 1 – Publiser siden uten nedetid

Hovedprinsippet: **rør aldri den gamle forsiden direkte.** Lag den nye siden ved
siden av, se på den i fred, og bytt forside først når den er 100 % riktig. Da er
tilbakerulling ett klikk unna.

1. **Ta backup først.** Hvis hosten har en «backup»-funksjon (eller en plugin som
   UpdraftPlus), kjør en full backup. Da har du en fasit å gå tilbake til.
2. **Lag en ny side.** WordPress-admin → **Sider → Legg til ny**. Kall den f.eks.
   «Ny forside». **Ikke** publiser den som forside ennå.
3. **Velg en mal med full bredde / uten sidebar.** I sideredigereren, se etter
   «Mal» / «Sidemal» i innstillingene til høyre. Velg «Full bredde», «Canvas»,
   «Blank» eller lignende hvis temaet har det. Da slipper designet å slåss mot
   temaets marger.
4. **Lim inn HTML-en.** Bytt til blokk-editoren → legg til blokken **«Egendefinert
   HTML» / «Custom HTML»** → lim inn **hele innholdet** i `wordpress/reer-side.html`.
   (Fonter er allerede flyttet til `@import`, så én blokk holder.)
5. **Forhåndsvis grundig.** Trykk **Forhåndsvis** og sjekk på både mobil og PC:
   - Fonter og farger ser riktige ut
   - Bilder/lenker fungerer
   - Skjemaet vises riktig (funksjon kobler vi i Del 2)
6. **Bytt forside – selve «go-live».** Når siden er riktig:
   **Innstillinger → Lesing → «Forsiden viser» → En statisk side → velg «Ny forside».**
   Lagre. Nettstedet peker nå på den nye siden umiddelbart.
7. **Rollback hvis noe er galt:** gå tilbake til **Innstillinger → Lesing** og velg
   den *gamle* forsiden igjen. Endringen er øyeblikkelig – ingen nedetid.

Tips: rydd cache etter byttet («Clear Cache» / Quick Cache i skjermbildet ditt),
ellers kan du selv se den gamle siden i noen minutter selv om besøkende ser den nye.

---

## Del 2 – Få skjemaet til å faktisk sende innsendinger

**Slik det er nå sender skjemaet ingenting.** Det viser bare «Takk!» og nullstiller
seg. Det må kobles til noe som kan sende e-post. Du er usikker på tilgangen til
`reer@reer.no`-innboksen – det er greit, for det er **ikke** til hinder for å sette
opp skjemaet. Se under.

### Anbefaling: bruk WPForms (allerede installert)

WPForms ligger allerede i oppsettet ditt, og er det tryggeste valget her:

- **Du trenger IKKE logge inn på `reer@reer.no` for å sette det opp.** Nettstedet
  *sender til* den adressen – det er noe helt annet enn å *lese* den. Å motta mailene
  krever selvsagt at noen har tilgang til innboksen, men oppsettet blokkeres ikke av
  det.
- **Ingen tapte henvendelser:** WPForms lagrer *hver* innsending i WordPress under
  **WPForms → Entries**, uansett om e-posten kommer frem eller ei. Det er nettopp
  denne backupen som fjerner risikoen for at en påmelding forsvinner i det stille.

**Steg:**
1. WPForms → **Add New** → velg en enkel «Simple Contact Form» og bygg feltene slik
   de er i designet: Navn, Mobilnummer, E-post (valgfritt), Kursvalg (dropdown),
   Ønsket lærer (dropdown), Melding (valgfritt).
2. **Notifications:** sett **Send To Email Address** til `reer@reer.no` (innboksen
   er nå tilgjengelig via `webmail.reer.no`).
   - **Lærervalget:** alle innsendinger går til `reer@reer.no` – valgt lærer vises som
     et felt i e-posten. Legg feltet `{Ønsket lærer}` inn i notifikasjonens
     meldingstekst, slik at den som leser innboksen ser hvem påmeldingen gjelder og
     kan videreformidle. Dette er tryggere enn å sende direkte til hver lærers adresse
     (én sikker innboks framfor tre adresser som kan feile).
   - Vil dere *senere* også varsle læreren direkte, kan dere legge på et **betinget
     varsel** («Conditional/Smart Notifications») som et ekstra steg – uten å endre
     hovedflyten til `reer@reer.no`.
3. **Confirmation:** sett en takkemelding – f.eks. samme tekst som «Takk! Vi tar
   kontakt så snart som mulig.»
4. **Sett skjemaet inn i siden:** siden er limt inn som Custom HTML, og en
   WPForms-kortkode kjører *ikke* inne i en Custom HTML-blokk. Gjør derfor slik:
   - I «Ny forside»: la Custom HTML-blokken slutte rett før skjemaseksjonen, legg
     inn en egen **WPForms-blokk** der skjemaet skal stå, og fortsett evt. med en ny
     Custom HTML-blokk for footeren. Alternativt: behold hele designet og erstatt kun
     `<form id="signupForm">…</form>` i HTML-en med en WPForms-blokk på samme sted.

### Viktig for at e-posten skal komme frem: SMTP

Standard WordPress-e-post (PHP `mail()`) havner ofte i søppelpost eller feiler helt
på delte webhoteller. **Installer «WP Mail SMTP»** og koble den til hostens egen SMTP
for `reer@reer.no`. Da sendes varslene *som* `reer@reer.no` via samme server som
mottar dem – best mulig leveringssikkerhet.

**Bekreftede innstillinger (fra cPanel → Configure Mail Client, SSL/TLS):**

| Felt i WP Mail SMTP | Verdi |
|---|---|
| Mailer | Other SMTP |
| SMTP Host | `mail.reer.no` |
| Encryption | SSL |
| SMTP Port | `465` |
| Auto TLS | På |
| Authentication | På |
| SMTP Username | `reer@reer.no` |
| SMTP Password | mailboks-passordet (skriv inn direkte i WordPress – ikke lagre i repoet) |
| From Email | `reer@reer.no` |
| From Name | Reer & Horten Trafikkskole |

(Til info: innkommende IMAP er `mail.reer.no` port `993` – ikke nødvendig for skjemaet,
men greit å ha.) Uten SMTP kan varslene stilne – men Entries-backupen i WPForms fanger
dem uansett. **Send en testinnsending** etter oppsett og bekreft at mailen kommer frem
til `reer@reer.no`.

### Alternativ hvis du vil beholde det håndlagde skjemadesignet

Vil du beholde det pent stylede skjemaet i HTML-en (i stedet for WPForms sitt
utseende), kan du koble `<form id="signupForm">` til en skjematjeneste som
**Web3Forms** eller **Formspree**:

- Sett `action="…"` på `<form>` til tjenestens endepunkt, `method="POST"`, og gi
  hvert felt et `name`.
- Fjern `e.preventDefault()`-blokken nederst (eller send via `fetch`).
- Ulempe: disse tjenestene sender deg en tilgangsnøkkel / krever verifisering på
  mottaker-e-posten – altså trenger du én gangs tilgang til en innboks du styrer
  (kan være din egen midlertidig).

**WPForms er fortsatt anbefalt** – både fordi innsendinger lagres som backup under
Entries, og fordi du slipper å verifisere mottaker-e-post hos en ekstern tjeneste.

---

## Sjekkliste før du bytter forside

- [ ] Full backup tatt
- [ ] Ny side ser riktig ut på mobil og PC (forhåndsvist)
- [ ] Skjema koblet til WPForms, mottaker satt til `reer@reer.no`
- [ ] Lærer-feltet (`{Ønsket lærer}`) med i varselteksten
- [ ] WP Mail SMTP satt opp (mail.reer.no / SSL / 465), og **testinnsending bekreftet mottatt**
- [ ] Entries-lagring bekreftet (innsendingen dukker opp under WPForms → Entries)
- [ ] Cache tømt etter forside-bytte
- [ ] Rollback-plan klar: Innstillinger → Lesing → velg gammel forside

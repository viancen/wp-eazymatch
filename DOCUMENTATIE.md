# EazyMatch installeren en instellen in WordPress

Deze handleiding is bedoeld voor websitebeheerders die de EazyMatch-plugin in een
WordPress-website willen installeren en de vacatureonderdelen via het CMS willen
plaatsen.

De voorbeelden gaan uit van EazyMatch 7.0.1. De plugin ondersteunt WordPress 7.1
en PHP 7.4 tot en met 8.5.

## 1. Benodigdheden

Zorg vóór de installatie voor:

- een WordPress-beheerdersaccount met de bevoegdheid om plugins te installeren;
- een server met PHP 7.4 of nieuwer;
- een actief EazyMatch-account;
- de EazyMatch-instancenaam, API-key en API-secret;
- een back-up van de website als je een bestaande installatie vervangt.

De standaard API-URL is `https://api.eazymatch.cloud`. Gebruik alleen een andere
URL als EazyMatch die expliciet heeft verstrekt.

> Plaats de API-key en het API-secret nooit in een WordPress-pagina, shortcode,
> template of publiek document. Deze gegevens horen alleen in het afgeschermde
> EazyMatch-instellingenscherm.

## 2. De plugin installeren

### Installeren via het WordPress-dashboard

1. Maak een zipbestand van de map `wp-eazymatch`. In het zipbestand moet
   `wp-eazymatch/wp-eazymatch.php` staan.
2. Log in op WordPress.
3. Ga naar **Plugins > Nieuwe plugin toevoegen > Plugin uploaden**.
4. Selecteer het zipbestand en klik op **Nu installeren**.
5. Klik na de installatie op **Plugin activeren**.

### Installeren via SFTP of het hostingpaneel

1. Upload de volledige map naar `wp-content/plugins/wp-eazymatch/`.
2. Controleer of het hoofdbestand staat op
   `wp-content/plugins/wp-eazymatch/wp-eazymatch.php`.
3. Ga in WordPress naar **Plugins** en activeer **EazyMatch**.

Bij een update mag de bestaande pluginmap worden vervangen nadat een back-up is
gemaakt. De instellingen staan in de WordPress-database en blijven normaal
gesproken behouden.

## 3. De EazyMatch-verbinding instellen

1. Open in het WordPress-dashboard **EazyMatch**.
2. Vul de volgende velden in:

   - **API:** normaal `https://api.eazymatch.cloud`;
   - **Instance naam:** de instancenaam van het EazyMatch-account;
   - **Key:** de verstrekte API-key;
   - **Secret:** het verstrekte API-secret;
   - **Lang:** bijvoorbeeld Nederlands.

3. Vul desgewenst het klantadres en de account-URL's in.
4. Klik op **Opslaan**.
5. Klik op **Connect met eazymatch**.

Na een geslaagde verbinding worden ook de overige EazyMatch-menuonderdelen
beschikbaar. Gebruik **Connectie met EazyMatch vernieuwen** als de sleutel,
instantie of API-URL later verandert.

## 4. Permalinks voorbereiden

De plugin gebruikt leesbare URL's voor vacaturedetails, zoekfilters en
sollicitaties.

1. Ga naar **Instellingen > Permalinks**.
2. Kies **Berichtnaam** of een andere niet-lege aangepaste structuur.
3. Klik op **Wijzigingen opslaan**.

Sla de permalinks opnieuw op nadat je een EazyMatch-pagina of URL-slug hebt
gewijzigd. Hiermee vernieuwt WordPress de routeringsregels.

## 5. De benodigde WordPress-pagina's maken

Maak eerst de onderstaande pagina's via **Pagina's > Nieuwe pagina toevoegen**.
Gebruik voor elke shortcode een Gutenberg-blok van het type **Shortcode**. In de
klassieke editor kan de shortcode rechtstreeks in de inhoud worden geplakt. In
Elementor, Divi of een andere pagebuilder gebruik je de shortcode-widget van die
pagebuilder.

Plak de shortcodes niet in een HTML- of PHP-blok.

### Pagina 1: Vacatures

- Titel: `Vacatures`
- Aanbevolen slug: `vacatures`
- Inhoud:

```text
[eazymatch view="searchjobs" settings="title=Zoek een vacature|button=Zoeken|reset=Alle vacatures tonen"]

[eazymatch view="jobpage"]
```

De eerste shortcode toont het zoekformulier. De tweede toont de resultaten en
verwerkt filters uit de URL.

### Pagina 2: Vacature

- Titel: `Vacature`
- Aanbevolen slug: `vacature`
- Inhoud:

```text
[eazymatch view="job"]
```

Deze pagina is de container voor één vacature. Het vacature-ID wordt automatisch
via de URL aan de pagina doorgegeven.

### Pagina 3: Solliciteren

- Titel: `Solliciteren`
- Aanbevolen slug: `solliciteren`
- Inhoud:

```text
[eazymatch view="apply"]
```

Deze pagina toont het sollicitatieformulier voor de gekozen vacature.

### Pagina 4: Bedankt

- Titel: `Bedankt voor je sollicitatie`
- Aanbevolen slug: `bedankt`
- Inhoud: een normale bevestigingstekst zonder EazyMatch-shortcode.

Publiceer alle vier de pagina's voordat je ze in EazyMatch selecteert.

## 6. De pagina's aan EazyMatch koppelen

Ga naar **EazyMatch > Vacatures** en selecteer:

| Instelling | Te selecteren pagina |
| --- | --- |
| Vacature weergave pagina | Vacature |
| Job zoek page | Vacatures |
| Sollicitatie pagina | Solliciteren |
| Url met bedankpagina | Bedankt voor je sollicitatie |

Op hetzelfde scherm staan enkele velden die als **dummy-url** zijn aangeduid.
Dit zijn actieve, virtuele fallback-URL's voor oudere links, widgets en feeds.
Vul hier alleen een slug in, dus zonder domeinnaam en zonder `/` aan het begin of
einde. Voorbeeld:

| Instelling | Voorbeeldslug |
| --- | --- |
| Vacature weergave url | `vacature-detail` |
| Vacature zoek url | `vacatures-zoeken` |
| Sollicitatie url | `reageren-vacature` |
| Url voor open inschrijving | `open-sollicitatie` |

Maak geen gewone WordPress-pagina's met deze dummy-slugs. Gebruik ook niet
dezelfde slug voor meerdere instellingen.

Klik op **Opslaan** en sla daarna nogmaals de WordPress-permalinks op.

## 7. Vacatures op andere pagina's plaatsen

Een compacte vacaturelijst kan op iedere pagina, landingspagina of homepage
worden geplaatst:

```text
[eazymatch view="jobs" limit="5"]
```

`limit` bepaalt het maximale aantal getoonde vacatures. Gebruik een positief
geheel getal.

Filteren op competentie-ID's kan als volgt:

```text
[eazymatch view="jobs" limit="10" competences="31,152"]
```

Gebruik alleen numerieke competentie-ID's uit het EazyMatch-matchprofiel.

### Een tekstblok als korte tekst in het overzicht

Standaard toont het overzicht de omschrijving van de vacature als korte tekst
(mits **Korte omschrijving in zoekresultaat?** onder **EazyMatch > Vacatures**
aan staat). Met `job-teaser-text` wijs je in plaats daarvan één van de
tekstblokken van de vacature aan:

```text
[eazymatch view="jobs" limit="5" job-teaser-text="12"]
[eazymatch view="jobpage" job-teaser-text="12"]
```

De waarde is het id van het tekstblok, of de originele EazyMatch-titel van het
blok (bijvoorbeeld `job-teaser-text="Wij bieden"`). De beschikbare tekstblokken
met hun id's staan onderaan **EazyMatch > Shortcodes**. Is het blok bij een
vacature leeg of bestaat het niet, dan wordt voor die vacature de gewone
omschrijving getoond.

Een los zoekformulier kan ook elders worden geplaatst:

```text
[eazymatch view="searchjobs" settings="title=Vind jouw baan|button=Zoeken|reset=Wis filters"]
```

Het attribuut `settings` is bij `searchjobs` verplicht. Scheid de onderdelen met
een verticale streep (`|`).

## 8. Overzicht van beschikbare shortcodes

| Shortcode | Functie | Parameters |
| --- | --- | --- |
| `[eazymatch view="jobs"]` | Compacte lijst met vacatures | `limit`, `competences`, `job-teaser-text` |
| `[eazymatch view="searchjobs" settings="..."]` | Zoekformulier voor vacatures | `settings` (verplicht) |
| `[eazymatch view="jobpage"]` | Volledige vacaturezoekresultaten | `job-teaser-text` |
| `[eazymatch view="job"]` | Eén vacature | – |
| `[eazymatch view="apply"]` | Sollicitatieformulier | – |
| `[eazymatch view="cv"]` | Compacte lijst uit de CV-database | – |
| `[eazymatch view="react"]` | Reactieformulier voor een kandidaat | `applicant_id` |

Voor de normale vacaturewebsite zijn de eerste vijf shortcodes voldoende.

Een volledig overzicht van alle shortcodes, hun parameters (type, standaardwaarde,
voorbeeld) en de beschikbare tekstblokken voor `job-teaser-text` staat in het CMS
onder **EazyMatch > Shortcodes**. Dit overzicht wordt gegenereerd uit
`lib/emol/shortcoderegistry.php`; een nieuwe shortcode of parameter hoeft alleen
daar te worden toegevoegd.

## 9. Formulieren en reCAPTCHA instellen

Ga naar **EazyMatch > Formulieren** om per invoerveld te bepalen of het:

- niet wordt getoond;
- optioneel wordt getoond;
- verplicht wordt getoond.

Op dit scherm kunnen ook de Google reCAPTCHA-sitekey en het bijbehorende secret
worden ingevuld. Vul altijd beide waarden in en zorg dat het websitedomein bij
de reCAPTCHA-configuratie is toegestaan. Laat beide velden leeg als reCAPTCHA
niet wordt gebruikt.

Controleer onder **EazyMatch > Vacatures** daarnaast:

- of sollicitaties als webaanmelding of direct als kandidaat worden verwerkt;
- het e-mailadres en de begeleidende e-mailtekst;
- de succesmelding en de melding bij geen resultaten;
- het aantal vacatures per pagina;
- welke vacaturegegevens en filters zichtbaar zijn.

## 10. Optionele onderdelen

De overige menuonderdelen zijn alleen nodig als de website deze functies gebruikt:

- **Managers:** contactpersoon en contactblok bij een vacature;
- **CV-database:** kandidaatprofielen, filters en reactie-URL's;
- **Accountformulieren:** velden voor kandidaat- en bedrijfsaccounts;
- **AVG wet:** privacyvelden en toestemmingen;
- **Styling:** aanvullende CSS en het verwijderen van HTML uit omschrijvingen;
- **Connectiviteit:** social sharing en externe vacaturefeeds.

De styling wordt opgeslagen als `eazymatch.style.css` in de WordPress-uploadmap.
Die map moet daarom beschrijfbaar zijn voor WordPress. Schakel feed- en
sharingopties alleen in als ze daadwerkelijk worden gebruikt.

## 11. Cache en beveiliging

- Sluit de pagina's voor solliciteren, kandidaat-/bedrijfsaccounts en andere
  formulieren uit van volledige paginacache.
- Deel API-gegevens niet met redacteuren die ze niet nodig hebben.
- Gebruik HTTPS voor de hele website.
- Test formulieren altijd ook in een privévenster, dus zonder ingelogde
  WordPress-sessie.
- Verwijder of anonimiseer testsollicitaties volgens het privacybeleid van de
  organisatie.

## 12. Oplevercontrole

Controleer na de configuratie minimaal het volgende:

1. **EazyMatch > EazyMatch** toont een werkende verbinding.
2. De pagina **Vacatures** toont gepubliceerde vacatures.
3. Zoeken en het wissen van filters werken.
4. Een vacature opent op de pagina **Vacature** en geeft geen 404-fout.
5. De sollicitatieknop opent het juiste formulier.
6. Verplichte velden en reCAPTCHA worden correct gevalideerd.
7. Een testsollicitatie komt in de juiste EazyMatch-omgeving binnen.
8. De bedankpagina of succesmelding verschijnt na verzenden.
9. De weergave werkt op mobiel en desktop.
10. Caching en een cookiebanner blokkeren het formulier niet.

## 13. Problemen oplossen

### Geen verbinding met EazyMatch

Controleer de API-URL, instancenaam, key en secret. Sla de instellingen op, klik
op **Connectie met EazyMatch vernieuwen** en daarna op **Connect met eazymatch**.

### Een vacature- of sollicitatielink geeft een 404-fout

Controleer of de geselecteerde pagina's gepubliceerd zijn, alle slugs uniek zijn
en de dummy-slugs niet als gewone pagina bestaan. Sla daarna **Instellingen >
Permalinks** opnieuw op en leeg de websitecache.

### Het zoekformulier toont “Define settings please.”

Het verplichte `settings`-attribuut ontbreekt. Gebruik bijvoorbeeld:

```text
[eazymatch view="searchjobs" settings="title=Vacatures zoeken|button=Zoeken|reset=Alle vacatures"]
```

### Er verschijnen geen vacatures

Controleer of vacatures in EazyMatch gepubliceerd zijn. Controleer daarna het
ingestelde vacaturefilter, eventuele competentie-ID's en het aantal resultaten
per pagina.

### De aangepaste styling verschijnt niet

Controleer of WordPress naar de uploadmap mag schrijven. Sla **EazyMatch >
Styling** opnieuw op en leeg vervolgens de WordPress-, server- en browsercache.

### Het formulier wordt niet verzonden

Test zonder cache en controleer de verplichte velden. Als reCAPTCHA actief is,
controleer dan de combinatie van sitekey, secret en toegestaan domein.

## Ondersteuning

Noteer bij een supportmelding:

- de WordPress-, PHP- en EazyMatch-pluginversie;
- de URL waarop het probleem optreedt;
- de exacte foutmelding;
- de stappen waarmee de fout herhaald kan worden.

Stuur nooit API-secrets, persoonsgegevens of volledige sollicitatiegegevens mee
in een onbeveiligde e-mail.

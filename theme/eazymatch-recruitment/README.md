# EazyMatch Recruitment

WordPress-thema voor vacaturewebsites die draaien op de EazyMatch-plugin
(`wp-eazymatch`). Het thema levert de site-opmaak; alle vacaturedata komt uit de
plugin via de `[eazymatch]`-shortcodes.

- Vereist WordPress 6.0 of nieuwer en PHP 7.4 of nieuwer.
- Vereist de actieve EazyMatch-plugin voor vacatures, zoeken en solliciteren.

## Installeren

1. Installeer en activeer eerst de EazyMatch-plugin en stel de API-verbinding in
   (**EazyMatch > EazyMatch**).
2. Ga naar **Weergave > Thema's > Nieuw thema toevoegen > Thema uploaden** en
   upload `eazymatch-recruitment.zip`.
3. Activeer het thema.
4. Ga naar **EazyMatch > EazyTheme** voor de primaire kleur, het logo en
   klik op **Pagina's aanmaken en koppelen**.
5. Zet **Instellingen > Permalinks** op **Berichtnaam** en sla op.

## EazyTheme

Onder **EazyMatch > EazyTheme** stel je de primaire kleur en het logo in. Die
waarden gelden voor het recruitmentthema. Het hoofdmenu wijs je zelf toe via
**Weergave > Menu's** (menupositie **Hoofdmenu**).

## Wat de installatieassistent doet

De assistent maakt de vier pagina's die de plugin nodig heeft en zet ze meteen in
de juiste EazyMatch-instelling:

| Pagina | Slug | Inhoud | EazyMatch-instelling |
| --- | --- | --- | --- |
| Vacatures | `vacatures` | `[eazymatch view="searchjobs" settings="..."]` + `[eazymatch view="jobpage"]` | Job zoek page |
| Vacature | `vacature` | `[eazymatch view="job"]` | Vacature weergave pagina |
| Solliciteren | `solliciteren` | `[eazymatch view="apply"]` | Sollicitatie pagina |
| Bedankt voor je sollicitatie | `bedankt` | bevestigingstekst | Url met bedankpagina |

Daarnaast vult de assistent de dummy-url's (`vacature-detail`,
`vacatures-zoeken`, `reageren-vacature`, `open-sollicitatie`), zet hij de korte
omschrijving, regio en plaats in het zoekresultaat aan en slaat hij de
permalinks opnieuw op. Bestaande pagina's en al ingevulde instellingen worden
niet overschreven.

## Instellingen in de Customizer

Onder **Weergave > Customizer**:

- **Kleuren** – hoofdkleur en accentkleur. De hele site, inclusief de
  plugin-onderdelen, neemt deze kleuren over.
- **Header** – tekst en url van de knop rechts in de header. Leeg gelaten wijst
  de knop automatisch naar de vacaturepagina uit de EazyMatch-instellingen.
- **Homepage: hero** – bovenkopje, titel, introtekst, knop,
  achtergrondafbeelding, het zoekformulier in de hero en kerncijfers
  (één per regel als `120+ | openstaande vacatures`).
- **Homepage: vacatures** – titel, introtekst en de parameters van
  `[eazymatch view="jobs"]`: aantal, competentie-id's en het tekstblok dat als
  korte tekst dient (`job-teaser-text`).
- **Homepage: oproep** – het blok voor een open sollicitatie.
- **Footer** – de tekst onderin.

## Menu's en widgets

Menuposities: **Hoofdmenu**, **Footermenu** en **Juridisch menu (onderaan)**.
Widgetgebieden: **Zijbalk** (bij berichten en archieven) en **Footer kolom 1-3**.

## Sjablonen

- `front-page.php` – vacature-homepage met hero, zoekformulier, vacaturelijst,
  stappenplan, de eigen inhoud van de homepagina en een oproepblok.
- `template-vacatures.php` – paginasjabloon **Vacatureoverzicht** dat het
  zoekformulier en de resultaten zelf plaatst, zonder shortcodes in de inhoud.
- `page.php` – pagina's; pagina's met een `[eazymatch]`-shortcode krijgen de
  volle breedte zonder zijbalk.
- `single.php`, `archive.php`, `index.php`, `search.php`, `404.php` – berichten
  en overige weergaven.

## Styling van de plugin-onderdelen

`assets/css/eazymatch.css` stylet de markup die de plugin genereert:
vacaturekaarten (`.emol-job-result-item`), het zoekwidget (`.emol_widget`), de
vacaturedetailpagina (`#emol-job-container`), de paginering
(`.emol-pagnation`), formulieren (`.emol_form_row`, `.emol-text-input`), het
contactblok van de manager en de cv-database.

Aanvullende CSS blijft mogelijk via **EazyMatch > Styling**; die wordt na het
thema geladen.

## Aandachtspunten

- Sluit de sollicitatiepagina uit van volledige paginacache.
- Zonder actieve plugin tonen de vacatureonderdelen een melding die alleen voor
  beheerders zichtbaar is.
- Maak geen gewone pagina's met de dummy-slugs; die zijn virtueel.

## Licentie

GPLv2 of later.

# Fitbase – projektnoter til Claude

Fitbase (fitbase.dk, tidligere Playform) er en affiliate-side, der ligner en webshop. Den samler fitnessudstyr fra flere danske shops via Partner-ads-produktfeeds. Man køber ikke på siden: "Gå til butik" sender brugeren videre til shoppen via et affiliate-link.

Ejer: Frederik Baastrup. Al tekst på siden er på dansk.

Siden hed Playform. Synlige tekster og logo siger nu Fitbase (`playform_rebrand()` i `inc/setup.php` rettede databasen én gang). Kodenavne er bevidst bevaret: mappenavne `playform`/`playform-feeds`, namespace `Playform\Feeds`, funktioner `playform_*`, metafelter `_pf_*` og shortcoden `[playform_produkter]` – omdøb dem ikke, det bryder udrulning og data.

## Opbygning

- `wp-content/plugins/playform-feeds/` – importen (PHP 8.1+, kræver WooCommerce)
  - `src/Config.php` – shops (feed-URL'er), kategoritræ og sorteringsregler. **En ny niche = en ny Config.**
  - `src/FeedReader.php` – læser feeds som strøm (XMLReader). Feeds er iso-8859-1; XMLReader giver korrekt UTF-8.
  - `src/Normalizer.php` – ensretter felter (lager, levering, pris, EAN, billed-URL).
  - `src/Classifier.php` – placerer produkter i vores kategorier (regler; AI-fallback kommer).
  - `src/Attributes.php` – udtrækker filtre (foldbar, incline, km/t, maks. brugervægt, vægt i kg) fra tekst. Intet gættes.
  - `src/Offers.php` – tabel `wp_pf_offers`: ét tilbud pr. produkt pr. butik. Produkter matches på tværs af shops via EAN.
  - `src/PriceHistory.php` – tabel `wp_pf_price_history`: dagens laveste pris pr. produkt pr. butik.
  - `src/Sales.php` – butikkernes tilbud (højst 14 dage gamle) → `_pf_shop_sale`. Genberegnes efter hver import.
  - `src/ProductSync.php` – opretter/opdaterer WooCommerce "eksterne produkter".
  - `src/Subcategories.php` – underkategorier (landingssider) ud fra regler i `Config::subcategories()`.
  - `src/ImageCache.php` – produktbilleder gemmes på egen server (GDPR + hastighed).
  - `src/Ai.php`, `src/AiClassifier.php`, `src/AiWriter.php`, `src/AdminTexts.php` – AI-sortering og -tekster med godkendelse.
  - `src/Importer.php`, `src/Cron.php`, `src/Cli.php`, `src/Admin.php`, `src/Frontend.php`
- `wp-content/themes/playform/` – temaet, bygget ud fra designet (forside, kategoriside, produktside).
  - `inc/navigation.php` – menuen (`playform_menu_spec()`): Træningsmaskiner, Styrketræning, Yoga & pilates, Restitution, Tilbehør, Tilbud. Træningsmaskiner og Styrketræning er grupperingskategorier med oversigtsside.
  - `inc/permalinks.php` – flade kategori-URL'er (`/traeningsmaskiner/lobebaand/gaabaand/`) og `/…/tilbud/`-sider. WooCommerce' præfiks `kategori` bruges kun til gamle links, som omdirigeres.
  - `inc/search.php` – søgeforslag (`/wp-json/playform/v1/search`).
- `dev/setup-local.sh` – lokal WordPress med SQLite i `.local/` (ignoreres af git).

## Vigtige regler

1. **Tilbud = butikkernes egne tilbud, højst 14 dage gamle.** Et produkt er på tilbud, når den billigste butik på lager selv har markeret det med en førpris (`glpris` > pris), og netop det tilbud højst er 14 dage gammelt (Forbrugerombudsmandens to-ugers-regel). Se `Sales` (`Sales::DAYS`) og kolonnen `sale_since` i `wp_pf_offers`. Vi viser kun "Tilbud hos [butik]" og prisen, **aldrig butikkens førpris eller rabat i %** (`_pf_sale_percent` bruges kun til sortering). Vores egen prishistorik (`PriceHistory::drop()`) er ikke et tilbud. Den vises kun som faktuel prisinfo på produktsiden ("Laveste pris de 30 dage før").
2. **AI må aldrig opfinde fakta.** Produkttekster skrives kun ud fra feed-data (navn, mærke, beskrivelse, kategori) og generel købsrådgivning for kategorien. Ingen opdigtede specifikationer. Tekster gemmes som udkast og godkendes af ejeren.
3. **Affiliate-links** har `rel="sponsored nofollow"`, og siden skal tydeligt oplyse, at den indeholder annoncer.
4. **Udsolgte produkter slettes ikke.** Siden bliver liggende som "ikke på lager". Eksterne produkter kan ikke have WooCommerce-lagerstatus, så vi bruger metafeltet `_pf_in_stock`.
5. **Prissammenligning er en bonus.** De fleste produkter sælges af én butik. Produktsiden viser "Sælges af [butik]" ved én butik og prislisten ved flere.
6. **Ingen tredjepartskald fra den offentlige side.** Skrift ligger i `themes/playform/assets/fonts/`, billeder caches lokalt, WooCommerce' scripts og sporings-cookies er slået fra.
7. **Redaktørens rettelser vinder.** Metafeltet `_pf_locks` (`name`, `category`) forhindrer importen i at overskrive.

## Kommandoer

```bash
dev/setup-local.sh <kildemappe>     # lokal WordPress (admin/admin)
wp playform import                  # hent og importer alle feeds
wp playform import --shop=apuls --limit=50
wp playform import --file=apuls:/sti/feed.xml
wp playform stats                   # produkter pr. kategori
wp playform drops                   # genberegn målte prisfald (prishistorik)
wp playform sales                   # genberegn butikstilbud
```

På hosting: kør `wp playform import` fra et rigtigt cronjob kl. 05:30 og 17:30 og sæt `DISABLE_WP_CRON`.

## Udrulning

Hvert push til `main` udrulles via GitHub Actions (FTPS) til `frederikb7.sg-host.com/public_html` på FTP-kontoen. Bliver playform.dk hoveddomæne, kan SiteGround omdøbe mappen – ret så stien i `.github/workflows/deploy.yml`.

## Design

Designet ligger som et Claude-artefakt (Playform: forside, kategoriside, produktside). Stil: hvid baggrund, sort tekst, BDO Grotesk, afrundede hjørner (14–18 px), accentfarve #23D200 (fra logoet) brugt sparsomt, tynde linjeikoner (stroke 1.4), tynde streger med lav opacity.

## Shops

Apuls.dk, Fitnessshoppen.dk, Træningspartner, Den Intelligente Krop (kun træning, yoga, pilates, massage) og OptimaSport.dk (kun restitution). Padelshoppen og Watery er fravalgt (ikke fitness).

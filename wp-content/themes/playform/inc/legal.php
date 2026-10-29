<?php
/**
 * Udkast til privatlivspolitik og cookiepolitik.
 * Skrevet ud fra, hvad siden faktisk gør i dag. Felter i [firkantede parenteser] skal udfyldes.
 * Teksterne bør gennemlæses af en med juridisk indsigt, før siden går i luften.
 */

defined( 'ABSPATH' ) || exit;

function playform_legal_privacy(): string {
	return <<<'HTML'
<p><em>Senest opdateret: [dato]</em></p>

<h2>Hvem er ansvarlig?</h2>
<p>Fitbase (fitbase.dk) drives af [navn/firmanavn], [adresse], CVR [CVR-nummer]. Du kan altid kontakte os på [e-mail].</p>

<h2>Hvad Fitbase er</h2>
<p>Fitbase samler fitnessudstyr fra flere danske webshops ét sted. Du kan ikke købe noget direkte hos os. Når du klikker på "Gå til butik", sendes du videre til butikken, og det er butikken, der behandler dit køb og dine oplysninger i forbindelse med det.</p>

<h2>Hvilke oplysninger vi behandler</h2>
<h3>Når du besøger siden</h3>
<p>Vores webserver registrerer tekniske oplysninger som IP-adresse, browsertype og hvilke sider der vises. Det sker for at kunne levere siden, beskytte den mod misbrug og rette fejl. Oplysningerne gemmes i serverens logfiler i op til [antal] dage og bruges ikke til at følge dig eller lave profiler.</p>
<p>Retsgrundlaget er vores legitime interesse i at drive en sikker og velfungerende hjemmeside (databeskyttelsesforordningens artikel 6, stk. 1, litra f).</p>

<h3>Når du tilmelder dig nyhedsbrevet</h3>
<p>Tilmelder du dig nyhedsbrevet, gemmer vi din e-mailadresse og tidspunktet for tilmeldingen. Vi bruger den kun til at sende dig nyhedsbrevet. Du skal bekræfte tilmeldingen via en mail, og du kan altid afmelde dig via linket nederst i hver mail.</p>
<p>Retsgrundlaget er dit samtykke (artikel 6, stk. 1, litra a). Nyhedsbrevet sendes via [navn på nyhedsbrevstjeneste], som behandler oplysningerne på vores vegne som databehandler. Vi sletter din e-mail, når du afmelder dig.</p>

<h3>Når du kontakter os</h3>
<p>Skriver du til os, bruger vi dine oplysninger til at svare dig. Vi sletter korrespondancen, når den ikke længere er relevant, og senest efter [antal] måneder.</p>

<h2>Når du klikker videre til en butik</h2>
<p>Linkene til butikkerne går gennem affiliate-netværket Partner-ads. Partner-ads registrerer klikket, så butikken kan se, at du kom fra Fitbase, og så vi kan få provision, hvis du handler. Det sker hos Partner-ads og butikken – ikke på fitbase.dk – og er omfattet af deres egne privatlivspolitikker. Vi får ingen oplysninger om, hvem du er, eller hvad du køber, kun at der er sket et salg.</p>

<h2>Produktbilleder</h2>
<p>Produktbillederne er leveret af butikkerne og gemmes på vores egen server. Når du ser et produkt, sendes din IP-adresse derfor ikke til butikkerne.</p>

<h2>Hvem vi deler oplysninger med</h2>
<p>Vi sælger aldrig dine oplysninger. Vi bruger disse databehandlere:</p>
<ul>
<li>[Hostingudbyder] – drift af hjemmesiden (data opbevares i [land/EU])</li>
<li>[Nyhedsbrevstjeneste] – udsendelse af nyhedsbrev (data opbevares i [land/EU])</li>
</ul>

<h2>Dine rettigheder</h2>
<p>Du har ret til at få indsigt i de oplysninger, vi har om dig, og til at få dem rettet eller slettet. Du kan også gøre indsigelse mod behandlingen, få begrænset behandlingen og få udleveret dine oplysninger (dataportabilitet). Har du givet samtykke, kan du altid trække det tilbage.</p>
<p>Kontakt os på [e-mail], hvis du vil bruge dine rettigheder. Er du utilfreds med, hvordan vi behandler dine oplysninger, kan du klage til Datatilsynet, datatilsynet.dk.</p>
HTML;
}

function playform_legal_cookies(): string {
	return <<<'HTML'
<p><em>Senest opdateret: [dato]</em></p>

<h2>Bruger Fitbase cookies?</h2>
<p>Fitbase bruger ikke cookies til statistik, markedsføring eller til at følge dig. Derfor beder vi dig heller ikke om samtykke, når du besøger siden.</p>

<h2>Nødvendige cookies</h2>
<p>Siden sætter kun cookies, hvis du logger ind som redaktør i administrationen. De er nødvendige for, at login fungerer, og bruges ikke på den offentlige del af siden.</p>

<h2>Når du klikker videre til en butik</h2>
<p>Når du klikker på "Gå til butik", sendes du via affiliate-netværket Partner-ads videre til butikken. Partner-ads og butikken kan sætte deres egne cookies, når du lander hos dem. De er omfattet af Partner-ads' og butikkens egne cookie- og privatlivspolitikker.</p>

<h2>Hvis vi ændrer det</h2>
<p>Begynder vi at bruge fx statistikværktøjer, opdaterer vi denne side og beder om dit samtykke, før der sættes cookies, som ikke er nødvendige.</p>

<h2>Spørgsmål</h2>
<p>Kontakt os på [e-mail].</p>
HTML;
}

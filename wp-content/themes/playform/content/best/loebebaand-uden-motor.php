<?php
/**
 * Overført fra det gamle fitbase.dk (/loebebaand-uden-motor/ – og /curved-loebebaand/, der nu viderestiller hertil).
 * Kun få løbebånd uden motor hos butikkerne, derfor 2 valg. Fakta kun fra butikkernes data.
 */
defined( 'ABSPATH' ) || exit;

return [
	'slug'     => 'loebebaand-uden-motor',
	'title'    => 'Løbebånd uden motor: Vores bud – curved og manuelle løbebånd',
	'excerpt'  => 'Et løbebånd uden motor drives af dine egne skridt – fra buede "curved" løbebånd til enkle manuelle modeller. Sådan virker de, og hvem de passer til.',
	'more_cat' => 'lobebaand',
	'intro'    => <<<'HTML'
<p>På et løbebånd uden motor er det dig, der driver båndet. Det findes i to helt forskellige udgaver: de buede <strong>curved løbebånd</strong>, som du kender fra crossfit- og HYROX-centre, og enkle <strong>manuelle løbebånd</strong> med magnetisk bremse. Fælles for dem er, at tempoet følger dig – løber du hurtigere, kører båndet hurtigere – og at de ikke skal i stikkontakten for at køre.</p>
<p>Der er kun få løbebånd uden motor hos de danske butikker, så her er vores to bud – og en guide til, om et løbebånd uden motor overhovedet er det rigtige for dig.</p>
HTML,
	'picks'    => [
		[
			'ean'     => '7051944198141',
			'slug'    => 'trion-xct-2-0-lobeband',
			'name'    => 'Abilica Trion XCT 2.0',
			'role'    => 'Bedst samlet (curved)',
			'reviews' => [],
			'verdict' => 'Selvdrevet, buet lamelbåndsløbebånd med manuel modstand og app – bygget til intervaller, HYROX og funktionel fitness.',
			'why'     => <<<'HTML'
<p>Trion XCT 2.0 er et curved løbebånd: løbefladen er buet og består af lameller, og du driver det selv. Butikken beskriver det som selvdrevet med manuel modstand, app-tilslutning og en naturlig løbefornemmelse, udviklet til intervaltræning, HYROX og funktionel fitness.</p>
<p>På et buet løbebånd bestemmer din position farten: træder du frem mod toppen af buen, accelererer båndet; træder du tilbage, sænker det farten. Det gør det perfekt til sprint og intervaller, men det kræver, at du vænner dig til det. Prisen er høj, fordi det er bygget til hård brug.</p>
HTML,
			'good'    => [ 'Sprint og intervaller', 'HYROX- og crossfittræning', 'Dig, der vil træne uden strøm' ],
			'less'    => [ 'Rolig gang og lange, jævne ture', 'Dig med et normalt budget' ],
		],
		[
			'ean'     => '8029975998039',
			'slug'    => 'toorx-power-mag-lobeband-uden-motor',
			'name'    => 'Toorx Power Mag',
			'role'    => 'Manuelt løbebånd',
			'reviews' => [],
			'verdict' => 'Manuelt løbebånd med magnetisk modstand i 8 niveauer, 180 kg brugervægt og foldbart stel – det billigere alternativ.',
			'why'     => <<<'HTML'
<p>Toorx Power Mag er et manuelt løbebånd uden motor: du skubber båndet rundt med dine skridt. Ifølge butikken har det magnetisk modstand i 8 niveauer og et 20 kg dobbelt svinghjul, en stødabsorberende løbeflade på 125 x 46 cm og en maksimal brugervægt på 180 kg. Det kan foldes og flyttes på transporthjul.</p>
<p>Det er en enklere og billigere konstruktion end det buede løbebånd og fungerer bedst til rask gang og jævnt løb. Modellen er ikke altid på lager. Er den udsolgt, viser vi et alternativ ved produktet.</p>
HTML,
			'good'    => [ 'Gang med høj intensitet', 'Dig, der vil have et løbebånd uden strøm til en lavere pris' ],
			'less'    => [ 'Hurtigt løb og sprint – vælg et curved løbebånd' ],
		],
	],
	'guide'    => <<<'HTML'
<h2>Er et løbebånd uden motor det rigtige for dig?</h2>
<p>Løbebånd uden motor er fantastiske til nogle typer træning og ret upraktiske til andre. Her er fordele og ulemper.</p>
<h3>Fordele</h3>
<ul>
<li><strong>Du styrer tempoet med kroppen</strong> – ingen knapper, båndet følger dig. Det er perfekt til sprint og intervaller.</li>
<li><strong>Hårdere træning</strong> – du skal selv drive båndet, så pulsen kommer hurtigere op end på et motoriseret løbebånd ved samme fart.</li>
<li><strong>Ingen strøm</strong> – det kan stå, hvor der ikke er en stikkontakt, og der er ingen motor, der kan gå i stykker.</li>
</ul>
<h3>Ulemper</h3>
<ul>
<li><strong>Svært at holde et jævnt tempo</strong> – vil du løbe 10 km i samme fart, er et motoriseret løbebånd nemmere.</li>
<li><strong>Curved løbebånd er dyre</strong> og fylder meget.</li>
<li><strong>Manuelle løbebånd er tunge at gå på</strong> og passer bedst til gang.</li>
</ul>
<h3>Curved eller manuelt?</h3>
<p>Vil du sprinte og lave intervaller, er et curved løbebånd det rigtige. Vil du gå hurtigt og have et billigere løbebånd uden strøm, er et manuelt løbebånd nok. Vil du løbe lange ture i jævnt tempo, så vælg et almindeligt løbebånd med motor – se vores bud på <a href="/koebsguide/foldbart-loebebaand/">det bedste foldbare løbebånd</a> og <a href="/koebsguide/loebebaand-med-stigning/">løbebånd med stigning</a>.</p>
<h3>Hvor meget skal du give?</h3>
<p>Manuelle løbebånd koster typisk fra få tusinde kroner, mens curved løbebånd til hjemmet sjældent koster under 30.000 kr. Se alle <a href="/traeningsmaskiner/lobebaand/">løbebånd</a> med aktuelle priser.</p>
HTML,
	'faq'      => [
		[ 'Hvad er et curved løbebånd?', 'Et løbebånd med en buet løbeflade uden motor. Din position på buen styrer farten, så du accelererer ved at træde frem og bremser ved at træde tilbage.' ],
		[ 'Er løbebånd uden motor hårdere?', 'Ja, du skal selv drive båndet, så det kræver mere energi ved samme hastighed end et løbebånd med motor.' ],
		[ 'Kan man gå på et curved løbebånd?', 'Ja, men det er bedst til løb og sprint. Til rolig gang er et almindeligt løbebånd eller et gåbånd mere behageligt.' ],
	],
];

<?php
/**
 * Overført fra det gamle fitbase.dk (/lille-loebebaand/) og omskrevet.
 * Fakta om produkterne kommer kun fra butikkernes produktdata. Ingen påstand om egne test.
 */
defined( 'ABSPATH' ) || exit;

return [
	'slug'     => 'lille-loebebaand',
	'title'    => 'Lille løbebånd: Vores bud på de 3 bedste kompakte modeller',
	'excerpt'  => 'Et lille løbebånd eller gåbånd giver dig daglig bevægelse uden at fylde stuen. Her er vores bud på de 3 bedste kompakte modeller – og hvad de kan.',
	'more_cat' => 'gaabaand',
	'intro'    => <<<'HTML'
<p>Et lille løbebånd er for dig, der vil have mere bevægelse i hverdagen, men ikke har plads til et stort løbebånd. Mange af de kompakte modeller er egentlig gåbånd – flade bånd uden høje håndtag, som kan skubbes ind under sofaen eller skrivebordet – mens andre er slanke løbebånd, der kan foldes.</p>
<p>Herunder er vores bud på de 3 bedste små løbebånd og gåbånd hos danske butikker lige nu – og en guide til, hvad du kan forvente af dem.</p>
HTML,
	'picks'    => [
		[
			'ean'     => '5704796046062',
			'slug'    => 'camino-premium-gaband',
			'name'    => 'Camino Premium',
			'role'    => 'Bedst samlet',
			'reviews' => [],
			'verdict' => 'Foldbart gåbånd på kun 18,8 kg med 1–8 km/t og manuel hældning – let at flytte, nok til rask gang og let jogging.',
			'why'     => <<<'HTML'
<p>Camino Premium er et kompakt gåbånd, der ifølge butikken kører fra 1 til 8 km/t, har 6 programmer og en manuel hældning på 8,5–11 %. Det er foldbart og vejer kun 18,8 kg, så det er nemt at flytte og gemme væk.</p>
<p>Hældningen gør gangen hårdere uden højere fart – en stor fordel på et lille bånd, hvor du ikke kan løbe stærkt.</p>
HTML,
			'good'    => [ 'Gang hjemme og under skrivebordet', 'Dig, der skal kunne flytte båndet nemt' ],
			'less'    => [ 'Egentligt løb over 8 km/t' ],
		],
		[
			'ean'     => '8029975960487',
			'slug'    => 'everfit-tfk-355-slim-lobeband',
			'name'    => 'Everfit TFK-355 Slim',
			'role'    => 'Bedst til løb',
			'reviews' => [],
			'verdict' => 'Slankt, foldbart løbebånd med op til 14 km/t og støddæmpning – lille, men et rigtigt løbebånd.',
			'why'     => <<<'HTML'
<p>Skal du både gå og løbe, er Everfit TFK-355 Slim et godt kompromis. Ifølge butikken har det et slimline, foldbart design, en kraftig DC-motor med op til 14 km/t, 12 programmer og stødabsorbering med 12 elastomerer.</p>
<p>Det er stadig et kompakt løbebånd, så det passer bedst til jogging og moderat løb – men det kan meget mere end et fladt gåbånd.</p>
HTML,
			'good'    => [ 'Gang og jogging', 'Små boliger, hvor båndet skal foldes væk' ],
			'less'    => [ 'Hurtige løbere og lange løbeture' ],
		],
		[
			'ean'     => '5704796046246',
			'slug'    => 'asg-hillwalker-600-gaband',
			'name'    => 'ASG Hillwalker 600',
			'role'    => 'Bedst til gang op ad bakke',
			'reviews' => [],
			'verdict' => 'Gåbånd med elektronisk hældning, 100 x 40 cm gangflade og app – til effektiv gangtræning.',
			'why'     => <<<'HTML'
<p>ASG Hillwalker 600 er lavet til gang med hældning. Ifølge butikken har den elektronisk hældning i niveau 0–6, en gangflade på 100 x 40 cm, 6 programmer og manuel træning, og den er Bluetooth App Ready til blandt andre FIIT Home, Kinomap og Zwift.</p>
<p>Gang med hældning er en af de mest effektive og skånsomme måder at træne kondition på, og her kan du ændre hældningen undervejs.</p>
HTML,
			'good'    => [ 'Gang op ad "bakke" hjemme', 'Dig, der vil træne med app' ],
			'less'    => [ 'Løb' ],
		],
	],
	'guide'    => <<<'HTML'
<h2>Sådan vælger du et lille løbebånd</h2>
<p>Små løbebånd er gode til at få mere bevægelse ind i hverdagen, men det er vigtigt at have de rigtige forventninger.</p>
<h3>Gåbånd eller løbebånd?</h3>
<p>Et gåbånd er fladt, uden høje håndtag, og kører typisk op til 6–8 km/t. Det er perfekt under et hæve-sænke-bord eller foran tv'et. Et kompakt løbebånd har håndtag og kan køre hurtigere, men fylder mere. Se også vores guide <a href="/viden/gaabaand-til-kontoret/">Gåbånd til kontoret</a>.</p>
<h3>Tophastighed og løbeflade</h3>
<p>Vil du kunne jogge, så kig efter mindst 10–14 km/t og en løbeflade, der er lang nok til dit skridt. Til gang er en kortere og smallere flade fin.</p>
<h3>Opbevaring og vægt</h3>
<p>Tjek, om båndet kan foldes, stilles på højkant eller skubbes ind under sofaen – og hvor meget det vejer. Et let bånd med hjul bliver brugt mere, fordi det er nemt at tage frem.</p>
<h3>Hældning</h3>
<p>Hældning gør gangen markant hårdere uden højere fart. På et lille bånd er det en af de bedste måder at få mere ud af træningen.</p>
<h3>Hvor meget skal du give?</h3>
<p>Gåbånd koster fra under 2.000 kr. hos de danske butikker; med hældning, app eller foldning typisk 4.000–8.000 kr. Slanke løbebånd ligger fra ca. 7.000 kr. Se alle <a href="/traeningsmaskiner/lobebaand/gaabaand/">gåbånd</a> og <a href="/traeningsmaskiner/lobebaand/foldbart-lobebaand/">foldbare løbebånd</a> med aktuelle priser.</p>
HTML,
	'faq'      => [
		[ 'Kan man løbe på et gåbånd?', 'De fleste gåbånd kører kun op til 6–8 km/t og er ikke bygget til løb. Vil du jogge, så vælg et kompakt løbebånd.' ],
		[ 'Kan et gåbånd stå under en sofa?', 'Mange flade gåbånd kan skubbes ind under en sofa eller seng. Tjek højden på båndet og din sofa.' ],
		[ 'Hvor mange skridt kan man gå på et gåbånd?', 'Det afhænger helt af, hvor længe du går. En time i roligt tempo giver typisk flere tusinde skridt.' ],
	],
];

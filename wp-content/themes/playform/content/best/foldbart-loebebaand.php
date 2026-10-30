<?php
/**
 * Overført fra det gamle fitbase.dk (/foldbart-loebebaand/ – og /sammenklappeligt-loebebaand/, der nu viderestiller hertil).
 * Fakta om produkterne kommer kun fra butikkernes produktdata. Anmeldelser er af samme model (Sole F63, US-udgaven).
 */
defined( 'ABSPATH' ) || exit;

return [
	'slug'     => 'foldbart-loebebaand',
	'title'    => 'Foldbart løbebånd: Vores bud på de 3 bedste',
	'excerpt'  => 'Et foldbart løbebånd giver dig løbetræning hjemme uden at miste gulvpladsen. Her er vores bud på de 3 bedste – og hvad du skal tjekke før køb.',
	'more_cat' => 'foldbart-lobebaand',
	'intro'    => <<<'HTML'
<p>Et løbebånd er en af de mest effektive maskiner til kondition, men det fylder. Et foldbart løbebånd kan klappes op eller lægges sammen efter træningen, så det ikke optager stuen hele dagen.</p>
<p>Herunder er vores bud på de 3 bedste foldbare – også kaldet sammenklappelige – løbebånd hos danske butikker lige nu. Nederst finder du en købsguide til motor, løbeflade, foldning og pris.</p>
HTML,
	'picks'    => [
		[
			'ean'     => '7051944157773',
			'slug'    => 'f63-lobeband',
			'name'    => 'Sole F63',
			'role'    => 'Bedst samlet',
			'reviews' => [
				[ 'Garage Gym Reviews', 'https://www.garagegymreviews.com/sole-f63-treadmill-review' ],
				[ 'Treadmill Review Guru', 'https://www.treadmillreviewguru.com/sole-f63-treadmill-review/' ],
			],
			'verdict' => 'Et af de mest anmeldte foldbare løbebånd: 3 HK motor, 20 km/t, 15 % stigning og Bluetooth.',
			'why'     => <<<'HTML'
<p>Sole F63 er et af de løbebånd, der oftest dukker op i udenlandske anmeldelser af løbebånd til hjemmet – også af uafhængige testsites som Garage Gym Reviews. Butikken oplyser en 3 HK motor, op til 20 km/t, 15 % stigning og Bluetooth, og at båndet er kompakt og foldbart.</p>
<p>Det er altså et løbebånd, der kan bruges til rigtig løbetræning og ikke kun gang, og som stadig kan klappes sammen. Anmeldelserne er af den amerikanske udgave; detaljer kan afvige en smule fra den europæiske, så tjek specifikationerne hos butikken.</p>
HTML,
			'good'    => [ 'Dig, der løber og ikke kun går', 'Dig, der vil have stigning til bakketræning', 'Dig, der vil have et løbebånd med anmeldelser at læne sig op ad' ],
			'less'    => [ 'Dig, der skal gemme løbebåndet under sengen – det er et klassisk løbebånd, der klappes op' ],
		],
		[
			'ean'     => '8717842034347',
			'slug'    => 'tunturi-cardio-fit-t25-foldbart-lobeband',
			'name'    => 'Tunturi Cardio Fit T25',
			'role'    => 'Bedst til prisen',
			'reviews' => [],
			'verdict' => 'Kompakt, nemt at folde og med 19 programmer, stigning og Bluetooth – til under 7.000 kr.',
			'why'     => <<<'HTML'
<p>Tunturi Cardio Fit T25 er et kompakt foldbart løbebånd, der ifølge butikken er let at folde sammen. Det har 19 programmer, 3 stigningsniveauer og Bluetooth med app-kompatibilitet.</p>
<p>Det er et godt bud til dig, der mest vil gå og løbe roligt, og som har brug for, at løbebåndet hurtigt kan komme af vejen. Til hård intervaltræning og høje hastigheder bør du kigge i en højere prisklasse.</p>
HTML,
			'good'    => [ 'Gang og let løb', 'Dig, der folder løbebåndet sammen efter hver tur', 'Dig, der vil holde prisen nede' ],
			'less'    => [ 'Dig, der løber hurtigt og meget – vælg et kraftigere løbebånd' ],
		],
		[
			'ean'     => '8029975996653',
			'slug'    => 'toorx-street-compact-foldbart-ga-lobeband-sort',
			'name'    => 'Toorx Street Compact',
			'role'    => 'Bedst til lille plads',
			'reviews' => [],
			'verdict' => 'Kun 10 cm tykt og op til 10 km/t – et gå- og løbebånd, der kan gemmes under sengen eller sofaen.',
			'why'     => <<<'HTML'
<p>Toorx Street Compact er for dig, hvor pladsen er det vigtigste. Ifølge butikken er det kun 10 cm tykt og fylder 0,07 m², det går op til 10 km/t, styres med fjernbetjening og har støddæmpning med 4 elastomer-cushions, et LED-display og 12 programmer.</p>
<p>Det er et gå- og løbebånd til rask gang og let jogging – ikke til løbetræning i højt tempo. Til gengæld kan det gemmes væk på steder, hvor et almindeligt løbebånd aldrig ville kunne være.</p>
HTML,
			'good'    => [ 'Gang, også mens du arbejder', 'Meget små boliger', 'Dig, der vil kunne gemme løbebåndet helt væk' ],
			'less'    => [ 'Løb over 10 km/t', 'Dig, der vil have håndtag og stigning' ],
		],
	],
	'guide'    => <<<'HTML'
<h2>Sådan vælger du det rigtige foldbare løbebånd</h2>
<p>Foldbare løbebånd spænder fra flade gåbånd til fuldt udstyrede løbebånd, der klappes op. Start med at afgøre, hvad du skal bruge det til.</p>
<h3>Skal du gå eller løbe?</h3>
<p>Skal du mest gå – fx under et skrivebord – er et fladt gåbånd nok, og det kan gemmes under sofaen. Skal du løbe, har du brug for en kraftigere motor, højere tophastighed, en længere løbeflade og håndtag. Se også vores bud på <a href="/lille-loebebaand/">det bedste lille løbebånd</a> og <a href="/traeningsmaskiner/lobebaand/gaabaand/">gåbånd</a>.</p>
<h3>Motor</h3>
<p>Kig efter den kontinuerlige effekt (ofte angivet i HK), ikke kun spidseffekten. En stærkere motor arbejder roligere og holder typisk længere – især hvis I er flere, eller hvis du vejer meget.</p>
<h3>Løbeflade</h3>
<p>Jo hurtigere du løber, og jo højere du er, jo længere skal løbefladen være. Er den for kort, bliver skridtet kort og unaturligt. Kig også efter støddæmpning, som skåner knæ og led.</p>
<h3>Foldning</h3>
<p>Der er to typer: løbebånd, hvor løbefladen klappes op mod konsollen, og flade bånd, der lægges ned eller stilles på højkant. Tjek målene sammenfoldet, og om der er transporthjul. Mange løbebånd har også en dæmper, så løbefladen sænkes langsomt – det gør dem sikrere at folde ud.</p>
<h3>Stigning</h3>
<p>Stigning gør gåture og løb hårdere uden højere tempo og skåner leddene. Er det vigtigt for dig, så se vores bud på <a href="/loebebaand-med-stigning/">løbebånd med stigning</a>.</p>
<h3>Hvor meget skal du give?</h3>
<p>Foldbare løbebånd koster fra ca. 4.000 kr. til over 20.000 kr. hos de danske butikker. Under 8.000 kr. er de bedst til gang og let løb; mellem 8.000 og 15.000 kr. får du typisk kraftigere motor og stigning; over det er du i klassen af løbebånd til seriøs løbetræning. Se alle <a href="/traeningsmaskiner/lobebaand/foldbart-lobebaand/">foldbare løbebånd</a> med aktuelle priser.</p>
HTML,
	'faq'      => [
		[ 'Er et foldbart løbebånd lige så godt som et fast?', 'Til de fleste hjemmeløbere ja. De største faste løbebånd er dog ofte mere stabile ved høj fart og tåler mere brug.' ],
		[ 'Hvad er forskellen på et foldbart og et sammenklappeligt løbebånd?', 'Det er det samme. Begge ord bruges om løbebånd, der kan foldes eller klappes sammen for at spare plads.' ],
		[ 'Kan man løbe på et gåbånd?', 'Nogle gåbånd kan køre op til 8–10 km/t og bruges til let jogging, men de er ikke bygget til egentlig løbetræning. Tjek tophastigheden og løbefladens længde.' ],
		[ 'Larmer et løbebånd i en lejlighed?', 'Motoren er som regel ikke det største problem – det er dine skridt. En måtte under løbebåndet og god støddæmpning hjælper meget.' ],
	],
];

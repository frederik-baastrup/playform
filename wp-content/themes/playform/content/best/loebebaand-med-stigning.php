<?php
/**
 * Overført fra det gamle fitbase.dk (/loebebaand-med-stigning/) og omskrevet.
 * Fakta om produkterne kommer kun fra butikkernes produktdata. Anmeldelser er af samme model.
 */
defined( 'ABSPATH' ) || exit;

return [
	'slug'     => 'loebebaand-med-stigning',
	'title'    => 'Løbebånd med stigning: Vores bud på de 3 bedste',
	'excerpt'  => 'Stigning gør løbebåndet hårdere uden højere fart – og skåner leddene. Her er vores bud på de 3 bedste løbebånd med stigning og hvad du skal kigge efter.',
	'more_cat' => 'lobebaand-med-incline',
	'intro'    => <<<'HTML'
<p>Stigning – også kaldet incline – er en af de bedste funktioner på et løbebånd. Når båndet hælder, bliver hvert skridt hårdere, pulsen stiger, og du træner baglår og baller mere, uden at du behøver at løbe hurtigere. Det gør stigning populært både til bakkeløb og til hurtig gang, der føles som en tur op ad bakke.</p>
<p>Herunder er vores bud på de 3 bedste løbebånd med stigning hos danske butikker lige nu. To af dem er grundigt anmeldt af uafhængige medier – links står ved produkterne. Nederst finder du en købsguide.</p>
HTML,
	'picks'    => [
		[
			'ean'     => '7051943731653',
			'slug'    => 'f85-lobeband',
			'name'    => 'Sole F85',
			'role'    => 'Bedst samlet',
			'reviews' => [
				[ 'Garage Gym Reviews', 'https://www.garagegymreviews.com/sole-f85-review' ],
				[ 'BarBend', 'https://barbend.com/sole-f85-treadmill-review/' ],
			],
			'verdict' => 'Stor løbeflade på 56 x 152 cm, 22 km/t og 15 stigningsniveauer – et løbebånd til seriøs løbetræning.',
			'why'     => <<<'HTML'
<p>Sole F85 er et af de mest anmeldte løbebånd til hjemmet i sin klasse, blandt andet hos Garage Gym Reviews og BarBend. Butikken oplyser en tophastighed på 22 km/t, 15 stigningsniveauer og en stor løbeflade på 56 x 152 cm, og den er Zwift-kompatibel.</p>
<p>Den store løbeflade er værd at hæfte sig ved: jo hurtigere du løber, og jo højere du er, jo mere plads har du brug for. Anmeldelserne er af den amerikanske udgave; tjek de europæiske specifikationer hos butikken.</p>
HTML,
			'good'    => [ 'Løbere, der træner hurtigt og ofte', 'Høje løbere, der har brug for en lang løbeflade', 'Dig, der vil træne med Zwift' ],
			'less'    => [ 'Små rum – det er et stort løbebånd', 'Dig, der mest skal gå' ],
		],
		[
			'ean'     => '0043619667479',
			'slug'    => 'x24-stigningslobeband',
			'name'    => 'NordicTrack X24',
			'role'    => 'Bedst til stejle bakker',
			'reviews' => [
				[ 'Garage Gym Reviews', 'https://www.garagegymreviews.com/nordictrack-x24-treadmill-review' ],
				[ 'Treadmill Review Guru', 'https://www.treadmillreviewguru.com/nordictrack-x24-treadmill-review/' ],
			],
			'verdict' => 'Et "uphill"-løbebånd bygget til meget stejl gang og bakketræning – til dig, der vil gå op ad bjerge i stuen.',
			'why'     => <<<'HTML'
<p>NordicTrack X24 er et såkaldt incline trainer eller uphill-løbebånd. Hvor et almindeligt løbebånd typisk stopper omkring 12–15 % stigning, er X24 bygget til markant stejlere gang. Det er populært til gang op ad "bakke", der giver hård træning med lav belastning på knæene.</p>
<p>Den er anmeldt af blandt andre Garage Gym Reviews og Treadmill Review Guru, hvor du også kan læse om stigning, skærm og app. Den er dyr og stor, så den giver mest mening, hvis bakketræning er din primære træningsform.</p>
HTML,
			'good'    => [ 'Dig, der elsker stejl gang og bakketræning', 'Dig, der vil have hård træning med lav belastning på leddene' ],
			'less'    => [ 'Dig med et begrænset budget', 'Små rum' ],
		],
		[
			'ean'     => '0885652038065',
			'slug'    => 'reebok-ers-520t-lobeband',
			'name'    => 'Reebok ERS 520T',
			'role'    => 'Bedst til prisen',
			'reviews' => [],
			'verdict' => '20 elektroniske stigningsniveauer og op til 20 km/t med børsteløs motor – til under 8.000 kr.',
			'why'     => <<<'HTML'
<p>Reebok ERS 520T giver dig elektronisk stigning til en pris, hvor mange løbebånd kun har manuel stigning eller slet ingen. Ifølge butikken har den en børsteløs motor, hastigheder fra 1 til 20 km/t og 20 elektroniske stigningsniveauer, så du kan skifte mellem gang, løb og bakker.</p>
<p>Elektronisk stigning betyder, at du kan ændre hældningen fra konsollen undervejs – nødvendigt, hvis du vil lave bakkeintervaller eller følge programmer.</p>
HTML,
			'good'    => [ 'Dig, der vil have elektronisk stigning til en fornuftig pris', 'Gang og løb i moderat tempo' ],
			'less'    => [ 'Dig, der løber meget hurtigt og langt – kig mod en større løbeflade' ],
		],
	],
	'guide'    => <<<'HTML'
<h2>Sådan vælger du et løbebånd med stigning</h2>
<p>Stigning findes på mange løbebånd i dag, men der er stor forskel på, hvor meget og hvor nemt du kan bruge den.</p>
<h3>Elektronisk eller manuel stigning</h3>
<p>Elektronisk stigning ændrer du fra konsollen, mens du træner. Manuel stigning skal indstilles, før du går i gang. Vil du lave bakkeintervaller eller bruge programmer, skal stigningen være elektronisk.</p>
<h3>Hvor stejlt skal det være?</h3>
<p>De fleste løbebånd har en stigning på 10–15 %, hvilket er nok til langt de fleste. Vil du gå meget stejlt – fx som træning til bjergvandring – findes der særlige uphill-løbebånd med langt mere hældning.</p>
<h3>Motor og løbeflade</h3>
<p>Stigning belaster motoren mere, især ved løb. Kig efter en motor med god kontinuerlig effekt og en løbeflade, der passer til din højde og dit tempo – længere, jo hurtigere du løber.</p>
<h3>Støddæmpning</h3>
<p>God støddæmpning skåner knæ og led og dæmper samtidig lyden af dine skridt – vigtigt, hvis du bor i lejlighed.</p>
<h3>App og programmer</h3>
<p>Mange løbebånd med stigning kan styres af apps som Zwift eller Kinomap, hvor stigningen følger ruten. Det gør træningen langt mere varieret.</p>
<h3>Hvor meget skal du give?</h3>
<p>Løbebånd med elektronisk stigning koster fra ca. 5.000 kr. hos de danske butikker. Mellem 8.000 og 15.000 kr. får du typisk en kraftigere motor og større løbeflade; over 20.000 kr. er du i klassen af løbebånd til hård og hyppig løbetræning. Se alle <a href="/traeningsmaskiner/lobebaand/lobebaand-med-incline/">løbebånd med incline</a> med aktuelle priser, eller se vores bud på <a href="/foldbart-loebebaand/">det bedste foldbare løbebånd</a>.</p>
HTML,
	'faq'      => [
		[ 'Hvad betyder incline på et løbebånd?', 'Incline er det engelske ord for stigning: løbefladen hælder fremad, så det føles som at gå eller løbe op ad bakke.' ],
		[ 'Forbrænder man mere med stigning?', 'Ja, det kræver mere energi at gå eller løbe op ad bakke end på fladt underlag ved samme tempo.' ],
		[ 'Er stigning godt for knæene?', 'Gang med stigning kan være skånsomt, fordi du får høj intensitet ved lavt tempo. Har du knæproblemer, så start roligt og øg stigningen gradvist.' ],
		[ 'Hvor mange procent stigning skal et løbebånd have?', 'For de fleste er 10–15 % rigeligt. Vil du træne til bjerge eller lave meget stejl gang, findes der løbebånd med langt mere hældning.' ],
	],
];

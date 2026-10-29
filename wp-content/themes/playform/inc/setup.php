<?php
/**
 * Grundopsætning, når temaet aktiveres: danske URL'er, forside og standardsider.
 * Kan køres igen sikkert (opretter kun det, der mangler).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'playform_setup_site' );

/**
 * Opdateringer efter en udrulning: kategorier, tekster, plugin-tabeller og URL-regler.
 *
 * Udrulningen (GitHub Actions) uploader filerne én ad gangen og lægger til sidst et nyt id i
 * wp-content/playform-deploy.txt. Først når id'et skifter, er alle filer på plads, og så kører vi
 * opdateringerne. (Tidligere brugte vi kun versionsnumre, men en sidevisning midt i uploaden kunne
 * så køre opdateringen med halvt gamle filer og markere den som færdig.)
 * Lokalt, uden id-fil, køres de, når PLAYFORM_THEME_VERSION ændres.
 */
add_action(
	'init',
	static function () {
		$file      = WP_CONTENT_DIR . '/playform-deploy.txt';
		$deploy_id = is_readable( $file ) ? trim( (string) file_get_contents( $file ) ) : ''; // phpcs:ignore
		$want      = $deploy_id !== '' ? $deploy_id : PLAYFORM_THEME_VERSION;
		if ( get_option( 'playform_content_version' ) === $want || get_transient( 'playform_upgrading' ) ) {
			return;
		}
		set_transient( 'playform_upgrading', 1, 5 * MINUTE_IN_SECONDS ); // kun én ad gangen
		@set_time_limit( 300 ); // phpcs:ignore
		playform_run_upgrades();
		update_option( 'playform_content_version', $want, false );
		delete_transient( 'playform_upgrading' );
	},
	25
);

function playform_run_upgrades(): void {
	// Importens tabeller og kategorier (kolonner, underkategorier, omsortering).
	if ( class_exists( '\Playform\Feeds\Install' ) ) {
		\Playform\Feeds\Install::activate();
	}
	playform_restructure_menu_categories();
	playform_seed_category_texts();
	playform_seed_articles();
	playform_rebrand();
	foreach ( [ 'playform_front_popular', 'playform_front_deals', 'playform_front_new', 'playform_price_ranges' ] as $t ) {
		delete_transient( $t );
	}
	// WooCommerce kræver et præfiks. Det bruges kun til gamle links, som omdirigeres (se permalinks.php).
	$perma = (array) get_option( 'woocommerce_permalinks', [] );
	if ( ( $perma['category_base'] ?? '' ) !== 'kategori' ) {
		$perma['category_base'] = 'kategori';
		update_option( 'woocommerce_permalinks', $perma );
	}
	flush_rewrite_rules( false );
}

/**
 * Opretter grupperingskategorierne (Træningsmaskiner, Styrketræning) og flytter de rigtige
 * kategorier ind under dem. Kan køres igen sikkert.
 */
function playform_restructure_menu_categories(): void {
	// Kondiudstyr hed tidligere gruppen for maskiner.
	$old = get_term_by( 'slug', 'kondiudstyr', 'product_cat' );
	if ( $old && ! get_term_by( 'slug', 'traeningsmaskiner', 'product_cat' ) ) {
		wp_update_term( $old->term_id, 'product_cat', [ 'name' => 'Træningsmaskiner', 'slug' => 'traeningsmaskiner' ] );
	}
	foreach ( playform_virtual_groups() as $slug => $group ) {
		$parent = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $parent ) {
			$result    = wp_insert_term( $group['label'], 'product_cat', [ 'slug' => $slug ] );
			$parent_id = is_wp_error( $result ) ? 0 : (int) $result['term_id'];
		} else {
			$parent_id = (int) $parent->term_id;
		}
		if ( ! $parent_id ) {
			continue;
		}
		foreach ( $group['children'] as $child_slug ) {
			$child = get_term_by( 'slug', $child_slug, 'product_cat' );
			if ( $child && (int) $child->parent !== $parent_id ) {
				wp_update_term( $child->term_id, 'product_cat', [ 'parent' => $parent_id ] );
			}
		}
	}
	// Under Styrketræning er "Styrke & bænke" et dobbelt-ord – den hedder nu Bænke & racks.
	$benches = get_term_by( 'slug', 'styrke-baenke', 'product_cat' );
	if ( $benches && str_contains( wp_specialchars_decode( $benches->name ), 'Styrke & bænke' ) ) {
		wp_update_term( $benches->term_id, 'product_cat', [ 'name' => 'Bænke & racks' ] );
	}
}

/**
 * Navneskiftet Playform → Fitbase: sidens titel og tekster, der allerede ligger i databasen
 * (faste sider og artikeludkast). Kører én gang.
 */
function playform_rebrand(): void {
	if ( get_option( 'playform_brand' ) === 'fitbase' ) {
		return;
	}
	update_option( 'blogname', 'Fitbase' );
	$posts = get_posts(
		[
			'post_type'      => [ 'page', 'post' ],
			'post_status'    => 'any',
			'posts_per_page' => -1,
			's'              => 'Playform',
		]
	);
	$old_sale = '<p>Vi viser kun rabatter, som vi selv har målt i vores prishistorik – aldrig butikkernes egne "førpriser".</p>';
	$new_sale = '<p>Som tilbud viser vi kun butikkernes egne tilbud, og kun i højst 14 dage. Vi viser aldrig butikkernes førpriser eller rabatter i procent.</p>';
	foreach ( $posts as $p ) {
		$content = str_replace( [ 'Playform', 'playform.dk', $old_sale ], [ 'Fitbase', 'fitbase.dk', $new_sale ], $p->post_content );
		$title   = str_replace( 'Playform', 'Fitbase', $p->post_title );
		if ( $content !== $p->post_content || $title !== $p->post_title ) {
			wp_update_post( [ 'ID' => $p->ID, 'post_content' => $content, 'post_title' => $title ] );
		}
	}
	update_option( 'playform_brand', 'fitbase', false );
}

function playform_setup_site(): void {
	update_option( 'timezone_string', 'Europe/Copenhagen' );
	// Guides ligger under /viden/navn/
	update_option( 'permalink_structure', '/viden/%postname%/' );
	// WooCommerce' ordresporing sætter cookies (sourcebuster) – den bruges ikke, da der ikke handles på siden.
	update_option( 'woocommerce_feature_order_attribution_enabled', 'no' );
	update_option( 'date_format', 'j. F Y' );
	update_option( 'WPLANG', 'da_DK' );
	// Danske URL'er: /produkt/navn/. Kategorier får flade URL'er (se permalinks.php).
	$perma = (array) get_option( 'woocommerce_permalinks', [] );
	$perma['category_base'] = 'kategori';
	$perma['tag_base']      = 'emne';
	$perma['product_base']  = 'produkt';
	update_option( 'woocommerce_permalinks', $perma );

	$pages = [
		'forside'                => [ 'Forside', '' ],
		'produkter'              => [ 'Alle produkter', '' ],
		'viden'                  => [ 'Viden & guides', '' ],
		'vores-butikker'         => [ 'Vores butikker', playform_default_text( 'butikker' ) ],
		'saadan-tjener-vi-penge' => [ 'Sådan tjener vi penge', playform_default_text( 'penge' ) ],
		'privatlivspolitik'      => [ 'Privatlivspolitik', playform_legal_privacy() ],
		'cookies'                => [ 'Cookiepolitik', playform_legal_cookies() ],
	];
	foreach ( $pages as $slug => [ $title, $content ] ) {
		$existing = get_page_by_path( $slug );
		if ( $existing && str_starts_with( trim( $existing->post_content ), '<p>[' ) && $content ) {
			wp_update_post( [ 'ID' => $existing->ID, 'post_title' => $title, 'post_content' => $content ] );
		}
		if ( ! $existing ) {
			wp_insert_post( [ 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $title, 'post_content' => $content ] );
		}
	}
	$front = get_page_by_path( 'forside' );
	$blog  = get_page_by_path( 'viden' );
	if ( $front && $blog ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front->ID );
		update_option( 'page_for_posts', $blog->ID );
	}
	$shop = get_page_by_path( 'produkter' );
	if ( $shop ) {
		update_option( 'woocommerce_shop_page_id', $shop->ID );
	}
	playform_seed_category_texts();
	// WordPress' eksempelindhold fjernes.
	foreach ( [ 'hello-world' => 'post', 'sample-page' => 'page' ] as $slug => $type ) {
		$p = get_page_by_path( $slug, OBJECT, $type );
		if ( $p ) {
			wp_delete_post( $p->ID, true );
		}
	}
	flush_rewrite_rules();
}

function playform_default_text( string $which ): string {
	if ( $which === 'butikker' ) {
		return '<p>Fitbase samler fitnessudstyr fra flere danske webshops ét sted. Vi henter produkter, priser og lagerstatus direkte fra butikkerne to gange i døgnet. Når du klikker på "Gå til butik", sendes du videre til butikken, hvor du handler som normalt.</p><h2>Butikker på Fitbase</h2><p>[Listen over butikker vises automatisk her.]</p>';
	}
	return '<p>Fitbase er gratis at bruge. Når du klikker videre til en butik og handler, får vi en lille provision fra butikken. Det koster ikke dig noget ekstra, og det påvirker ikke, hvilke produkter vi viser, eller hvordan vi sorterer dem.</p><p>Som tilbud viser vi kun butikkernes egne tilbud, og kun i højst 14 dage. Vi viser aldrig butikkernes førpriser eller rabatter i procent.</p>';
}

/** Korte introtekster til underkategorierne. De finjusteres efter søgeordsanalysen. */
function playform_sub_intros(): array {
	return [
		'motionscykler'               => 'Find motionscykler fra flere danske webshops samlet ét sted – spinningcykler til intervaltræning, kondicykler til rolig motion og airbikes til hård træning.',
		'romaskiner-crosstrainere'    => 'Romaskiner og crosstrainere giver skånsom helkropstræning derhjemme. Se modeller fra flere danske webshops samlet ét sted.',
		'vaegte-kettlebells'          => 'Håndvægte, kettlebells, vægtskiver og vægtstænger fra flere danske webshops samlet ét sted – alt til styrketræning derhjemme.',
		'styrke-baenke'               => 'Træningsbænke, power racks, multigyms og pull-up-stænger til dit hjemmegym. Se udvalget fra flere danske webshops samlet ét sted.',
		'yoga-pilates'                => 'Yogamåtter, pilates reformere og tilbehør til yoga og pilates derhjemme. Se udvalget fra flere danske webshops samlet ét sted.',
		'restitution'                 => 'Massagepistoler, foam rollers, recovery boots og kuldeterapi, der hjælper kroppen med at restituere efter træning. Se udvalget fra flere danske webshops.',
		'tilbehor'                    => 'Træningsmåtter, elastikker, bolde, sjippetove og boksudstyr – alt det små udstyr, der gør træningen bedre. Se udvalget fra flere danske webshops.',
		'foldbart-lobebaand'          => 'Et foldbart løbebånd kan klappes sammen efter brug, så det fylder mindre i stuen eller lejligheden. Her finder du foldbare løbebånd fra flere danske webshops samlet ét sted.',
		'gaabaand'                    => 'Et gåbånd er et lavt, fladt løbebånd til gang i roligt tempo – ofte under et hæve-sænkebord på hjemmekontoret. Se gåbånd fra flere danske webshops.',
		'lobebaand-med-incline'       => 'Med incline kan løbebåndet hæve løbefladen, så du kan træne op ad bakke. Her er løbebånd med stigning fra flere danske webshops.',
		'manuelt-lobebaand'           => 'Et manuelt løbebånd drives af dine egne skridt og kræver ingen strøm. Se manuelle løbebånd fra flere danske webshops.',
		'lobebaand-til-tunge-brugere' => 'Løbebånd med høj maksimal brugervægt og kraftig konstruktion. Her er modeller, hvor butikken oplyser en brugervægt på mindst 150 kg.',
		'spinningcykel'               => 'En spinningcykel har et tungt svinghjul og en sportslig siddestilling, der er god til intervaltræning. Se spinningcykler fra flere danske webshops.',
		'kondicykel'                  => 'En kondicykel – også kaldet motionscykel – giver rolig og skånsom konditionstræning hjemme. Her finder du kondicykler fra flere danske webshops.',
		'liggecykel'                  => 'På en liggecykel sidder du tilbagelænet med ryglæn, hvilket aflaster ryg og led. Se liggecykler fra flere danske webshops.',
		'airbike'                     => 'En airbike bruger en blæser som modstand: Jo hårdere du træder, jo tungere bliver det. Se airbikes fra flere danske webshops.',
		'romaskine'                   => 'En romaskine træner både ben, ryg og arme i én bevægelse. Her finder du romaskiner med luft-, vand- og magnetmodstand fra flere danske webshops.',
		'crosstrainer'                => 'En crosstrainer giver skånsom helkropstræning uden stød på leddene. Se crosstrainere fra flere danske webshops.',
		'kettlebell'                  => 'Kettlebells i alle vægte – fra lette modeller til begyndere til tunge konkurrence-kettlebells. Se udvalget fra flere danske webshops.',
		'haandvaegte'                 => 'Håndvægte i gummi, vinyl og støbejern samt justerbare håndvægte, der sparer plads. Se håndvægte fra flere danske webshops.',
		'vaegtskiver'                 => 'Vægtskiver og bumper plates til vægtstænger og håndvægtsstænger. Her finder du vægtskiver fra flere danske webshops.',
		'vaegtstaenger'               => 'Vægtstænger, curlstænger og olympiske stænger til styrketræning hjemme. Se vægtstænger fra flere danske webshops.',
		'vaegtstativ'                 => 'Stativer og racks til håndvægte, kettlebells og vægtskiver, så hjemmegymmet holdes ryddeligt. Se vægtstativer fra flere danske webshops.',
		'traeningsbaenk'              => 'Flade, justerbare og foldbare træningsbænke til bænkpres, håndvægte og core-øvelser. Se træningsbænke fra flere danske webshops.',
		'power-rack'                  => 'Power racks, squat stands og half racks til tung styrketræning med vægtstang. Se udvalget fra flere danske webshops.',
		'multigym'                    => 'En multigym samler mange styrkeøvelser i én maskine med kabler og vægtstak. Se multigyms fra flere danske webshops.',
		'pull-up-stang'               => 'Pull-up-stænger og dip-stationer til træning med egen kropsvægt. Se udvalget fra flere danske webshops.',
		'yogamaatte'                  => 'Yogamåtter i forskellige tykkelser og materialer – til yoga, pilates og udstrækning. Se yogamåtter fra flere danske webshops.',
		'pilates-reformer'            => 'En pilates reformer er en træningsmaskine med fjedre og en kørende slæde til pilatesøvelser. Se reformere fra flere danske webshops.',
		'yogatilbehor'                => 'Yogablokke, stropper, bolstere og meditationspuder, der gør det lettere at komme ind i stillingerne. Se yogatilbehør fra flere danske webshops.',
		'massagepistol'               => 'En massagepistol giver hurtige stød, der kan løsne spændte muskler efter træning. Se massagepistoler fra flere danske webshops.',
		'foam-roller'                 => 'Foam rollers og massageruller til selvmassage af ben, ryg og skuldre. Se udvalget fra flere danske webshops.',
		'recovery-boots'              => 'Recovery boots er kompressionsstøvler, der masserer benene med luft. Se recovery boots fra flere danske webshops.',
		'kuldeterapi'                 => 'Isbade, isbadskar og udstyr til kuldeterapi efter træning. Se udvalget fra flere danske webshops.',
		'traeningsmaatte'             => 'Træningsmåtter og gulvmåtter, der beskytter gulvet og giver et godt underlag til træningen. Se måtter fra flere danske webshops.',
		'elastikker'                  => 'Træningselastikker og loop bands i flere modstande – lette at have med og gode til både styrke og genoptræning. Se udvalget fra flere danske webshops.',
		'boksning'                    => 'Boksehandsker, boksesække, håndbind og andet udstyr til boksning og kampsportstræning. Se udvalget fra flere danske webshops.',
		'traeningsbolde'              => 'Gymballs, medicinbolde, slam balls og wall balls til core- og styrketræning. Se træningsbolde fra flere danske webshops.',
		'sjippetov'                   => 'Sjippetove til konditionstræning og opvarmning – fra simple tove til hurtige speed ropes. Se udvalget fra flere danske webshops.',
	];
}

/** Introtekst, SEO-tekst og FAQ til kategorierne. Redaktøren kan rette dem under Produkter › Kategorier. */
function playform_seed_category_texts(): void {
	$texts = [
		'traeningsmaskiner' => [
			'intro' => 'Løbebånd, motionscykler, romaskiner og crosstrainere fra flere danske webshops samlet ét sted. Vælg en kategori og find maskinen, der passer til din træning og din plads.',
			'seo'   => '<h2>Træningsmaskiner til hjemmet – find den rigtige</h2><p>En træningsmaskine derhjemme gør det nemt at træne kondition, når det passer dig, uanset vejret. Her har vi samlet løbebånd, motionscykler, romaskiner og crosstrainere fra flere danske webshops, så du kan sammenligne udvalget ét sted. Priser og lagerstatus opdateres to gange i døgnet, og du handler direkte hos butikken.</p><h3>Hvilken træningsmaskine passer til dig?</h3><p>Et <strong>løbebånd</strong> er det oplagte valg, hvis du vil gå eller løbe, og det minder mest om at træne udenfor. En <strong>motionscykel</strong> er skånsom for knæ og ankler og fylder typisk mindre. En <strong>romaskine</strong> træner både ben, ryg, mave og arme i samme bevægelse, mens en <strong>crosstrainer</strong> giver en blød bevægelse uden stød, hvor du også bruger armene.</p><h3>Tænk på plads og støj</h3><p>Mål op, før du vælger. Løbebånd og romaskiner er lange, men mange modeller kan klappes sammen eller stilles op ad væggen. Bor du i lejlighed, er det værd at se på støjen: Motionscykler og romaskiner med magnetmodstand er typisk de mest stille, mens løb på løbebånd og luftmodstand larmer mere.</p><h3>Hvad skal du ellers kigge efter?</h3><p>Tjek den maksimale brugervægt, så den passer til dig med god margin, og om maskinen kan justeres til din højde. Et display med tid, distans og puls gør det nemmere at følge med i træningen.</p>',
			'faq'   => [
				[ 'Hvilken træningsmaskine forbrænder mest?', 'Det afhænger mest af, hvor hårdt du træner, og hvor længe. Maskiner, der bruger både arme og ben, som romaskiner og crosstrainere, får mange muskler i gang på én gang. Den bedste maskine er i praksis den, du får brugt fast.' ],
				[ 'Hvilken træningsmaskine er mest skånsom for leddene?', 'Motionscykler, crosstrainere og romaskiner er skånsomme, fordi der ikke er stød, når foden rammer underlaget. Gang på løbebånd er også skånsomt, mens løb belaster knæ og ankler mere.' ],
				[ 'Hvilken træningsmaskine fylder mindst?', 'En motionscykel fylder som regel mindst på gulvet. Har du brug for et løbebånd, er et foldbart løbebånd eller et gåbånd det mest pladsbesparende valg, og mange romaskiner kan stilles lodret.' ],
				[ 'Kan jeg have en træningsmaskine i en lejlighed?', 'Ja. Vælg en støjsvag model, fx en motionscykel eller romaskine med magnetmodstand, og læg en gummimåtte under maskinen, så vibrationer ikke forplanter sig til naboerne.' ],
			],
		],
		'styrketraening' => [
			'intro' => 'Håndvægte, kettlebells, vægtskiver, træningsbænke, racks og multigyms fra flere danske webshops samlet ét sted. Alt til styrketræning derhjemme.',
			'seo'   => '<h2>Udstyr til styrketræning derhjemme</h2><p>Med lidt udstyr kan du lave effektiv styrketræning hjemme. Her har vi samlet vægte og kettlebells, bænke, racks og multigyms fra flere danske webshops, så du kan sammenligne udvalget ét sted. Priser og lagerstatus opdateres to gange i døgnet, og du handler direkte hos butikken.</p><h3>Hvor skal du starte?</h3><p>Et par håndvægte eller en kettlebell er et godt første køb: Det fylder lidt og dækker mange øvelser for hele kroppen. Justerbare håndvægte er en fordel, hvis du vil kunne øge vægten uden at købe flere sæt. Med en træningsbænk får du flere øvelser, fx bænkpres og rows.</p><h3>Når du vil løfte tungere</h3><p>Vil du træne med vægtstang, giver et power rack eller et half rack sikkerhed, når du squatter eller bænkpresser alene. Husk, at vægtskiver og stang skal passe sammen: Der findes skiver med lille hul (standard) og stort hul (olympisk). En multigym samler mange øvelser i én maskine og er nem og sikker at bruge alene.</p><h3>Plads og gulv</h3><p>Mål loftshøjden, før du vælger et rack, og læg en træningsmåtte eller gummifliser på gulvet, så det er beskyttet mod tabte vægte. Et vægtstativ holder styr på håndvægte og skiver.</p>',
			'faq'   => [
				[ 'Hvad skal jeg købe først til styrketræning derhjemme?', 'Et par håndvægte eller en kettlebell er et godt sted at starte, fordi de fylder lidt og kan bruges til mange øvelser. Bagefter giver en justerbar træningsbænk flest nye muligheder.' ],
				[ 'Er justerbare håndvægte en god idé?', 'Ja, især hvis du har begrænset plads. Ét sæt justerbare håndvægte kan erstatte mange par faste håndvægte, og du kan øge vægten i takt med, at du bliver stærkere.' ],
				[ 'Har jeg brug for et power rack?', 'Kun hvis du vil træne tungt med vægtstang, fx squat og bænkpres, uden en makker. Racket har sikkerhedsstænger, der tager vægten, hvis du ikke kan løfte den op igen.' ],
				[ 'Hvordan beskytter jeg gulvet?', 'Læg en træningsmåtte eller gummifliser under det område, hvor du træner. Gummibelagte vægte og skiver er også mere skånsomme mod gulvet og larmer mindre, hvis de bliver sat hårdt ned.' ],
			],
		],
		'lobebaand' => [
			'intro' => 'Find løbebånd fra flere danske webshops samlet ét sted. Se foldbare løbebånd til lejligheden, gåbånd til hjemmekontoret og kraftige løbebånd til intervaltræning – med priser, der opdateres to gange i døgnet.',
			'seo'   => '<h2>Køb løbebånd – find den bedste pris</h2><p>Her finder du løbebånd fra flere danske webshops samlet ét sted. Vi opdaterer priser og lagerstatus to gange i døgnet. Når du har fundet den rigtige model, sender vi dig videre til butikken, hvor du handler som normalt.</p><h3>Sådan vælger du det rigtige løbebånd</h3><p>Start med, hvordan du vil træne. Til gang og rolig jogging er 12 km/t som regel nok, mens du til løb og intervaller bør gå efter mindst 16 km/t og en kraftig motor. Kig også på løbefladen: Jo længere dine skridt er, jo længere skal båndet være.</p><h3>Foldbart løbebånd eller fast model?</h3><p>Et foldbart løbebånd er oplagt, hvis det skal stå i stuen eller i en lejlighed. De faste modeller er til gengæld ofte mere stabile og bedre til tunge brugere og hårde pas.</p><h3>Hvad betyder incline?</h3><p>Incline er båndets stigning. Et løbebånd med incline lader dig simulere bakker, hvilket øger forbrændingen og skåner leddene i forhold til at løbe hurtigere.</p>',
			'faq'   => [
				[ 'Hvad koster et godt løbebånd til hjemmet?', 'Et solidt løbebånd til almindelig brug i hjemmet ligger typisk mellem 4.000 og 8.000 kr. Under 3.000 kr finder du mest gåbånd og kompakte modeller, mens løbebånd til hård intervaltræning eller tunge brugere ofte koster over 10.000 kr.' ],
				[ 'Hvad er forskellen på et gåbånd og et løbebånd?', 'Et gåbånd er lavt, fladt og beregnet til gang i roligt tempo, ofte under et hæve-sænkebord. Et løbebånd har en kraftigere motor, højere tophastighed og som regel håndtag og display, så du også kan løbe og træne intervaller.' ],
				[ 'Hvor meget plads kræver et løbebånd?', 'Regn med selve løbebåndets mål plus ca. en halv meter fri plads på hver side og to meter bagved af sikkerhedshensyn. Har du ikke meget plads, er et foldbart løbebånd eller et gåbånd oplagt.' ],
				[ 'Kan man have et løbebånd i en lejlighed?', 'Ja, men vælg en støjsvag model og læg en gummimåtte under, så vibrationer ikke forplanter sig til underboen. Gang og let løb larmer markant mindre end hurtigt løb.' ],
			],
		],
		'motionscykler' => [
			'intro' => 'Find motionscykler fra flere danske webshops samlet ét sted – spinningcykler til intervaltræning, kondicykler til rolig motion og airbikes til hård træning.',
			'seo'   => '<h2>Køb motionscykel – find den bedste pris</h2><p>Her finder du motionscykler fra flere danske webshops samlet ét sted, så du hurtigt kan sammenligne udvalg og priser. Vi opdaterer priser og lagerstatus to gange i døgnet, og når du har fundet den rigtige cykel, sender vi dig videre til butikken, hvor du gennemfører købet som normalt.</p><h3>Spinningcykel, kondicykel eller liggecykel?</h3><p>En spinningcykel har en sportslig siddestilling og et tungt svinghjul, der giver en jævn, tung trædemodstand – ideel til intervaltræning og spinninghold derhjemme. En kondicykel har en mere oprejst siddestilling og er typisk let at justere trinvist i modstand, hvilket gør den velegnet til rolig, jævn konditionstræning. En liggecykel har et ryglæn, så du sidder tilbagelænet med benene fremad. Siddestillingen aflaster ryg, nakke og led, hvilket gør den til et godt valg, hvis du er i genoptræning eller foretrækker en mere afslappet stilling.</p><h3>Hvad betyder svinghjulets vægt?</h3><p>Svinghjulets vægt afgør, hvor jævnt trædet føles. Et let svinghjul giver en mere rykvis fornemmelse, mens et tungt svinghjul giver en mere jævn og naturlig cykelfølelse, der minder om en rigtig cykel. Jo hårdere og mere seriøst du vil træne, jo mere betaler det sig at kigge efter et tungere svinghjul.</p><h3>Airbike til hård intervaltræning</h3><p>En airbike bruger en blæser som modstand i stedet for et svinghjul: Jo hårdere du træder og trækker i håndtagene, jo tungere bliver modstanden. Det gør airbiken velegnet til korte, hårde intervaller og helkropstræning, da du bruger både arme og ben samtidig. Til gengæld larmer en airbike typisk mere end en almindelig motionscykel, så den er ikke det bedste valg i en lejlighed med naboer tæt på.</p><h3>Hvor meget plads kræver en motionscykel?</h3><p>De fleste motionscykler fylder omkring en kvadratmeter. Sørg for at måle op, så der er plads nok til, at du kan sidde komfortabelt og sætte foden helt ned i bunden af trædet. Mange modeller har transporthjul, så det er nemmere at flytte cyklen væk, når den ikke bruges.</p><h3>Hvad skal du ellers være opmærksom på?</h3><p>Kig på den maksimale brugervægt, så den matcher dig med god margin, og tjek om sadel og styr kan justeres, så du kan finde en siddestilling, der passer til din højde. Et display, der viser tid, distance, hastighed og forbrænding, gør det lettere at følge med i fremgangen, og nogle modeller kan forbindes til træningsapps via Bluetooth.</p>',
			'faq'   => [
				[ 'Hvad er forskellen på en spinningcykel og en kondicykel?', 'En spinningcykel har et tungere svinghjul og en mere sportslig, foroverlænet siddestilling, der er god til hårde intervaller. En kondicykel har en oprejst siddestilling og trinvis modstandsjustering, hvilket gør den mere behagelig til rolig, jævn træning over længere tid.' ],
				[ 'Hvor meget skal jeg betale for en god motionscykel?', 'En almindelig kondicykel til hjemmebrug ligger typisk mellem 1.500 og 4.000 kr. Spinningcykler med et tungt svinghjul koster ofte 2.500-6.000 kr, mens airbikes og modeller med mange funktioner kan koste mere.' ],
				[ 'Er en airbike støjende?', 'Ja, airbikes larmer generelt mere end almindelige motionscykler, da blæseren, der skaber modstanden, også skaber luftstøj. Bor du i en lejlighed med naboer tæt på, kan det være værd at overveje en kondicykel eller spinningcykel i stedet.' ],
				[ 'Kan jeg bruge en motionscykel, hvis jeg har dårlig ryg eller knæ?', 'En liggecykel med ryglæn aflaster ryggen og er ofte et godt valg ved rygproblemer. Har du problemer med knæ eller led, er en jævn, skånsom modstand vigtigere end en høj tophastighed. Spørg altid din læge eller fysioterapeut til råds, hvis du er i aktiv genoptræning.' ],
			],
		],
		'romaskiner-crosstrainere' => [
			'intro' => 'Romaskiner og crosstrainere giver skånsom helkropstræning derhjemme. Se modeller fra flere danske webshops samlet ét sted.',
			'seo'   => '<h2>Køb romaskine eller crosstrainer – find den bedste pris</h2><p>Romaskiner og crosstrainere trækker flere muskelgrupper ind i træningen end en almindelig motionscykel og er samtidig skånsomme for led og ryg. Her finder du udvalget fra flere danske webshops samlet ét sted, med priser og lagerstatus, der opdateres to gange i døgnet.</p><h3>Romaskine: luft-, vand- eller magnetmodstand?</h3><p>En romaskine med luftmodstand bruger en blæser, så modstanden stiger, jo hårdere du trækker – populær i gyms og god til intervaltræning. Vandmodstand efterligner fornemmelsen af at ro på vand og er typisk mere stille. Magnetmodstand styres elektronisk, er ofte den mest støjsvage løsning og velegnet til lejligheder.</p><h3>Crosstrainer: skridtlængde og incline</h3><p>Crosstraineren efterligner en kombination af at gå, løbe og cykle, uden at fødderne forlader pedalerne, hvilket skåner knæ og ankler. Skridtlængden (afstanden pedalerne bevæger sig) betyder meget for komforten – jo højere du er, jo længere skridtlængde bør du kigge efter. Nogle modeller har incline, så du kan justere hældningen og dermed, hvilke muskler der belastes mest.</p><h3>Hvad giver den bedste træning?</h3><p>En romaskine træner typisk flere muskler på én gang – ben, ryg, mave og arme – i én sammenhængende bevægelse, og er derfor god til effektiv helkropstræning på kort tid. En crosstrainer er ofte lettere at gå til for begyndere og kan bruges i lavt tempo til lang, rolig kondition eller i højt tempo til intervaller.</p><h3>Plads og opbevaring</h3><p>Romaskiner er ofte lange, men mange modeller kan klappes sammen eller stilles lodret op efter brug, så de fylder mindre. Crosstrainere fylder typisk en fast plads på gulvet, da de sjældent kan klappes sammen, så mål op, inden du bestiller.</p><h3>Hvad skal du ellers kigge efter?</h3><p>Tjek den maksimale brugervægt, og om modstanden justeres manuelt eller elektronisk. Et display med puls, distance og forbrænding gør det lettere at følge sin træning, og transporthjul gør det nemmere at flytte maskinen, når den ikke bruges.</p>',
			'faq'   => [
				[ 'Er en romaskine eller en crosstrainer bedst?', 'Det afhænger af, hvad du vil opnå. En romaskine giver en mere intens helkropstræning på kort tid, mens en crosstrainer er skånsom og nem at gå til over længere perioder. Begge er gode til konditionstræning uden belastning af leddene.' ],
				[ 'Hvilken modstandstype er bedst på en romaskine?', 'Luftmodstand er populær til intervaltræning, da modstanden stiger med din indsats. Vandmodstand giver en naturlig rofornemmelse og er ret stille. Magnetmodstand er den mest støjsvage løsning og velegnet, hvis du bor tæt på naboer.' ],
				[ 'Hvor meget plads skal jeg have til en romaskine?', 'En romaskine er typisk 2-2,5 meter lang, men mange modeller kan klappes sammen eller stilles lodret til opbevaring, så de fylder markant mindre, når de ikke bruges.' ],
				[ 'Kan en crosstrainer bruges af flere forskellige højder i husstanden?', 'Ja, de fleste crosstrainere passer til et bredt spænd af højder, men har du meget høje eller lave brugere i husstanden, bør du kigge efter en model med justerbar skridtlængde eller tjekke producentens anbefalede højdeinterval.' ],
			],
		],
		'vaegte-kettlebells' => [
			'intro' => 'Håndvægte, kettlebells, vægtskiver og vægtstænger fra flere danske webshops samlet ét sted – alt til styrketræning derhjemme.',
			'seo'   => '<h2>Køb håndvægte, kettlebells og vægte – find den bedste pris</h2><p>Håndvægte, kettlebells og vægtstænger er noget af det mest alsidige udstyr, du kan have i et hjemmegym. Her finder du udvalget fra flere danske webshops samlet ét sted, med priser og lagerstatus, der opdateres to gange i døgnet.</p><h3>Faste eller justerbare håndvægte?</h3><p>Faste håndvægte fås i et bestemt vægtinterval og kræver, at du har flere par stående, hvis du vil variere vægten. Justerbare håndvægte har et drejesystem eller skiver, du selv sætter på, så du kan dække et bredt vægtspænd med ét sæt – en fordel, hvis du har begrænset plads.</p><h3>Hvilken kettlebell skal du vælge?</h3><p>Som nybegynder er en kettlebell på 8-12 kg til kvinder og 12-16 kg til mænd et fornuftigt udgangspunkt til de fleste øvelser. Trænede brugere går ofte efter 16-24 kg eller derover. Har du plads til det, kan det betale sig at have et par forskellige vægte, så du kan variere mellem tunge løft og mere dynamiske øvelser.</p><h3>Vægtskiver og vægtstænger</h3><p>Vægtskiver fås typisk i to standarder: et lille hul til standardstænger og et stort hul (olympisk) til kraftigere stænger, der bruges på de fleste power racks og multigyms. Sørg for, at skiver og stang passer sammen, før du køber. En vægtstang bør vælges ud fra, hvor tungt du planlægger at træne, og om den skal bruges til bænkpres, squat eller markløft.</p><h3>Opbevaring med et vægtstativ</h3><p>Et vægtstativ holder styr på håndvægte, kettlebells eller vægtskiver, så hjemmegymmet er ryddeligt, og udstyret er let at finde. Det er særligt en fordel, hvis du har flere sæt vægte, der ellers hurtigt kommer til at flyde på gulvet.</p><h3>Hvad skal du ellers være opmærksom på?</h3><p>Kig på materialet: Støbejern er holdbart og billigere, mens gummi- eller vinylbelagte vægte er mere skånsomme mod gulvet og mindre støjende, hvis de tabes. Tjek også, om håndtaget har et godt greb, især på kettlebells, hvor du holder fast gennem hele bevægelsen.</p>',
			'faq'   => [
				[ 'Hvilken vægt skal jeg starte med som nybegynder?', 'Til håndvægte er 2-6 kg pr. hånd et typisk startpunkt for nybegyndere, mens en kettlebell på 8-16 kg passer til de fleste øvelser i starten. Det vigtigste er, at du kan lave øvelserne med korrekt teknik, og at vægten kan øges, efterhånden som du bliver stærkere.' ],
				[ 'Er justerbare håndvægte lige så gode som faste?', 'Ja, justerbare håndvægte fungerer fint til de fleste øvelser og sparer plads, da de erstatter flere par faste håndvægte. Faste håndvægte er til gengæld hurtigere at skifte mellem under et sæt, hvilket nogle foretrækker til hård styrketræning.' ],
				[ 'Passer alle vægtskiver til alle vægtstænger?', 'Nej. Vægtskiver fås med enten et lille standardhul eller et stort olympisk hul, og de passer kun til stænger med samme diameter. Tjek altid specifikationerne, før du kombinerer skiver og stang fra forskellige producenter.' ],
				[ 'Hvor meget plads kræver et hjemmegym med vægte?', 'Et par håndvægte og en kettlebell eller to kan opbevares på meget lidt plads, gerne i et hjørne eller under en bænk. Vil du have et bredere udvalg af vægtskiver og stænger, kan et vægtstativ hjælpe med at holde det hele samlet og ryddeligt.' ],
			],
		],
		'styrke-baenke' => [
			'intro' => 'Træningsbænke, power racks, multigyms og pull-up-stænger til dit hjemmegym. Se udvalget fra flere danske webshops samlet ét sted.',
			'seo'   => '<h2>Køb træningsbænk, power rack eller multigym</h2><p>En god træningsbænk eller et rack er rygraden i mange hjemmegyms. Her finder du udvalget fra flere danske webshops samlet ét sted, med priser og lagerstatus, der opdateres to gange i døgnet.</p><h3>Flad, justerbar eller foldbar bænk?</h3><p>En flad bænk er simpel og stabil og dækker de fleste grundøvelser som bænkpres. En justerbar bænk kan vinkles op og ned, så du også kan lave skrå bænkpres og andre varianter – god investering, hvis du vil have flere øvelser med samme bænk. En foldbar bænk kan klappes sammen og stilles væk, hvilket er oplagt, hvis pladsen er begrænset.</p><h3>Power rack, half rack eller squat stand?</h3><p>Et power rack omslutter dig på alle sider med sikkerhedsstænger, hvilket gør det trygt at squatte og bænkpresse tungt alene. Et half rack fylder mindre og giver stadig god sikkerhed, mens en squat stand er den mest kompakte løsning, men med lidt mindre sikkerhed ved tunge løft uden en træningsmakker.</p><h3>Er en multigym noget for dig?</h3><p>En multigym samler mange øvelser i én maskine med kabler og en vægtstak, så du ikke behøver løse vægtskiver til hver øvelse. Det gør den nem og sikker at bruge alene, men den fylder til gengæld mere og er typisk dyrere end et rack og et sæt vægte.</p><h3>Pull-up-stænger og dip-stationer</h3><p>En pull-up-stang kan monteres i en dørkarm eller stå frit som en del af et rack, og er effektiv træning med egen kropsvægt til ryg og arme. Mange racks og multigyms har en indbygget pull-up-stang, så du slipper for at købe det separat.</p><h3>Hvad skal du ellers kigge efter?</h3><p>Tjek den maksimale brugervægt og belastning, og om rack og stænger passer til den vægtstang, du allerede har eller planlægger at købe. Er pladsen trang, så mål højden under loftet, før du vælger et rack – nogle modeller kræver god loftshøjde til pull-up-stangen.</p>',
			'faq'   => [
				[ 'Hvilken bænk skal jeg vælge til et lille hjemmegym?', 'En foldbar bænk er oftest det bedste valg med begrænset plads, da den kan stilles væk, når den ikke bruges. Vil du have flere øvelsesmuligheder, så kig efter en foldbar bænk, der også er justerbar i vinkel.' ],
				[ 'Hvad er forskellen på et power rack og et half rack?', 'Et power rack omslutter dig på alle fire sider og giver den højeste sikkerhed ved tunge løft alene. Et half rack har kun stænger på to sider, fylder mindre og er billigere, men giver lidt mindre beskyttelse, hvis du taber et tungt løft.' ],
				[ 'Er en multigym bedre end frie vægte?', 'Ingen af delene er entydigt bedre – de supplerer hinanden. En multigym er sikker og nem at bruge alene, mens frie vægte som stang og håndvægte typisk giver bedre udvikling af balance og stabiliserende muskler.' ],
				[ 'Hvor meget loftshøjde kræver et power rack med pull-up-stang?', 'Det varierer efter model, men regn med mindst 2,2-2,4 meter til racket selv, plus din egen rækkehøjde og armlængde ved hang fra pull-up-stangen. Tjek altid den enkelte models mål, hvis loftet er lavt.' ],
			],
		],
		'yoga-pilates' => [
			'intro' => 'Yogamåtter, pilates reformere og tilbehør til yoga og pilates derhjemme. Se udvalget fra flere danske webshops samlet ét sted.',
			'seo'   => '<h2>Køb yogamåtte, pilates reformer og tilbehør</h2><p>Yoga og pilates kræver ikke meget udstyr for at komme i gang, men den rigtige måtte og det rette tilbehør gør en stor forskel for komfort og udbytte. Her finder du udvalget fra flere danske webshops samlet ét sted, med priser og lagerstatus, der opdateres to gange i døgnet.</p><h3>Hvilken yogamåtte skal du vælge?</h3><p>Tykkelsen afgør, hvor meget polstring du har mod gulvet: 4-6 mm giver god kontakt til gulvet og er populær til dynamiske yogaformer, mens 8-10 mm giver mere komfort til knæ og ryg, hvilket passer godt til restorativ yoga eller hvis du træner på et hårdt gulv. Materialet har også betydning – PVC er holdbart og billigt, TPE er mere miljøvenligt, og kork giver godt greb, især når du sveder.</p><h3>Hvad er en pilates reformer?</h3><p>En pilates reformer er en træningsmaskine med en kørende slæde og fjedermodstand, der giver modstand i begge retninger af bevægelsen. Den bruges til at træne styrke, bevægelighed og kropsholdning med kontrollerede bevægelser, og modstanden kan justeres, så øvelserne passer til alt fra begyndere til øvede.</p><h3>Tilbehør, der gør træningen bedre</h3><p>Yogablokke hjælper dig med at nå gulvet i stillinger, hvor du endnu ikke har bevægeligheden til det, mens en yogastrop gør det lettere at strække og holde positioner korrekt. En bolster – en fast pude – bruges ofte i restorativ yoga til at støtte kroppen i afslappede stillinger.</p><h3>Hvad skal du ellers være opmærksom på?</h3><p>Tjek måttens længde, hvis du er høj, da standardmål ikke altid passer. Vælger du en pilates reformer, så kig på den maksimale brugervægt og slædens længde, og om den kan foldes sammen til opbevaring, hvis pladsen er begrænset.</p>',
			'faq'   => [
				[ 'Hvilken tykkelse skal min yogamåtte have?', 'En måtte på 4-6 mm giver god stabilitet og kontakt til gulvet og passer godt til dynamiske yogaformer som vinyasa. En tykkere måtte på 8-10 mm er mere behagelig for knæ og ryg og velegnet til restorativ yoga eller træning på et hårdt gulv.' ],
				[ 'Er en pilates reformer noget for begyndere?', 'Ja, modstanden på en reformer kan justeres fra meget let til krævende, så den kan bruges af både begyndere og øvede. Mange starter dog med enkle pilatesøvelser på måtte, før de investerer i en reformer.' ],
				[ 'Hvilket materiale er bedst til en yogamåtte?', 'PVC er holdbart, billigt og giver godt greb, men er mindre miljøvenligt. TPE er et mere bæredygtigt alternativ med lignende egenskaber. Kork giver ekstra godt greb, især når du sveder, men kan være dyrere.' ],
				[ 'Fylder en pilates reformer meget?', 'Ja, en reformer er typisk 2-2,5 meter lang og kræver fast gulvplads, da de fleste modeller ikke kan klappes helt sammen. Nogle modeller kan dog klappes delvist eller stilles på højkant for at spare plads.' ],
			],
		],
		'restitution' => [
			'intro' => 'Massagepistoler, foam rollers, recovery boots og kuldeterapi, der hjælper kroppen med at restituere efter træning. Se udvalget fra flere danske webshops.',
			'seo'   => '<h2>Køb massagepistol, foam roller og andet restitutionsudstyr</h2><p>God restitution er lige så vigtig som selve træningen, når du vil undgå ømhed og skader. Her finder du udstyr til restitution fra flere danske webshops samlet ét sted, med priser og lagerstatus, der opdateres to gange i døgnet.</p><h3>Massagepistol til hurtig løsning af spændte muskler</h3><p>En massagepistol giver hurtige, kraftige stød mod musklen og kan hjælpe med at løsne spændinger og øge blodgennemstrømningen efter træning. De fleste modeller har flere hastighedstrin og udskiftelige hoveder til forskellige muskelgrupper – et bredt, fladt hoved til store muskler som lår og ryg, og et mindre, spidst hoved til mere afgrænsede punkter.</p><h3>Foam roller til selvmassage</h3><p>En foam roller bruges ved at rulle kropsvægten frem og tilbage over musklen, hvilket kan hjælpe med at løsne stramme områder og forbedre bevægeligheden. Overfladen varierer fra glat til kraftigt riflet – jo mere struktur, jo kraftigere (og ofte mere ubehagelig) er effekten.</p><h3>Recovery boots og kuldeterapi</h3><p>Recovery boots er kompressionsstøvler, der skiftevis presser og slipper benene med luft, hvilket kan hjælpe blodet med at cirkulere tilbage efter hård træning. Kuldeterapi, som isbade og specielle isbadskar, bruges til at køle kroppen ned efter træning – en metode mange konkurrenceidrætsfolk bruger, selvom effekten diskuteres i forskningen.</p><h3>Hvornår skal du bruge hvad?</h3><p>Massagepistol og foam roller er gode til at bruge dagligt eller lige efter træning på specifikke muskelgrupper. Recovery boots egner sig til længere restitutionssessioner, fx efter en hård løbetur eller styrkepas, mens kuldeterapi typisk bruges som en afsluttende rutine efter særligt hårde træningspas eller konkurrencer.</p><h3>Hvad skal du ellers være opmærksom på?</h3><p>Tjek batteritid og støjniveau på massagepistoler, hvis du vil bruge den ofte eller om aftenen. På recovery boots er det værd at kigge på, hvor mange trykzoner støvlerne har, da flere zoner giver en mere målrettet massage.</p>',
			'faq'   => [
				[ 'Hjælper en massagepistol faktisk på ømme muskler?', 'Mange oplever, at en massagepistol løsner spændte muskler og gør ømhed mere udholdelig, især brugt kort efter træning. Den erstatter ikke opvarmning eller udstrækning, men kan være et godt supplement i din restitutionsrutine.' ],
				[ 'Hvor ofte bør jeg bruge en foam roller?', 'Du kan bruge en foam roller flere gange om ugen, gerne efter træning eller som en del af opvarmningen. Start roligt, især hvis musklerne er meget ømme, og undgå at rulle direkte på led og knogler.' ],
				[ 'Virker kuldeterapi og isbade?', 'Forskningen er delt: Nogle studier peger på, at kuldeterapi kan reducere oplevet muskelømhed kortvarigt, mens andre viser begrænset effekt på selve restitutionen. Mange bruger det alligevel som en fast rutine efter hårde træningspas.' ],
				[ 'Hvad er fordelen ved recovery boots frem for almindelig hvile?', 'Recovery boots skaber en aktiv, rytmisk kompression, der kan hjælpe blodcirkulationen på vej, mens almindelig hvile ikke gør noget aktivt for at fremskynde processen. De bruges ofte efter særligt lange eller hårde træningspas.' ],
			],
		],
		'tilbehor' => [
			'intro' => 'Træningsmåtter, elastikker, bolde, sjippetov og boksudstyr – alt det små udstyr, der gør træningen bedre. Se udvalget fra flere danske webshops.',
			'seo'   => '<h2>Køb træningsmåtter, elastikker og andet træningstilbehør</h2><p>Det rigtige tilbehør kan gøre en stor forskel for både komfort og variation i din træning, uden at det fylder meget eller koster meget. Her finder du udvalget fra flere danske webshops samlet ét sted, med priser og lagerstatus, der opdateres to gange i døgnet.</p><h3>Træningsmåtte til gulvøvelser</h3><p>En træningsmåtte beskytter både gulvet og dine led, når du laver øvelser som planke, mavebøjninger eller stræk. Den er typisk tykkere og mere robust end en yogamåtte og velegnet til, at du kan have udstyr som håndvægte stående på den uden at ødelægge gulvet.</p><h3>Elastikker i flere modstande</h3><p>Træningselastikker og loop bands fås i forskellige modstandsniveauer, ofte farvekodet fra let til hård. De er lette at tage med på farten og kan bruges til både styrketræning, opvarmning og genoptræning, da modstanden er skånsom mod led og sener.</p><h3>Boksudstyr til hjemmetræning</h3><p>Boksehandsker, håndbind og en boksesæk giver en hård konditionstræning, der samtidig træner teknik og koordination. Vælg boksehandsker ud fra vægt (oz) efter, hvor hårdt du skal træne, og husk håndbind, der beskytter håndled og knoer under slagene.</p><h3>Træningsbolde og sjippetov</h3><p>Medicinbolde og slam balls bruges til eksplosive styrkeøvelser, mens en gymball er god til core-træning og stabilitetsøvelser. Et sjippetov er noget af det mest effektive og billigste udstyr til konditionstræning, og en speed rope med kuglelejer gør det muligt at hoppe hurtigere og mere kontrolleret.</p><h3>Hvad skal du ellers være opmærksom på?</h3><p>Tjek materialet på elastikker – naturgummi er holdbart, men kan mærkes af sved og sol over tid. På træningsmåtter er tykkelse og overfladestruktur vigtigst, så måtten ikke glider under hårde øvelser.</p>',
			'faq'   => [
				[ 'Hvilken modstand skal jeg vælge på træningselastikker?', 'De fleste sæt har flere farvekodede modstande, så du kan starte let og øge styrken efterhånden. Som begynder er det en god idé at starte med en let til mellem modstand og supplere med en kraftigere, når du bliver stærkere.' ],
				[ 'Hvilken vægt skal en boksesæk have?', 'En boksesæk på 30-40 kg passer til de fleste hjemmebrugere, mens tungere sække på 50 kg eller mere kræver kraftigere montering i loft eller stativ. Jo tungere sækken er, jo mindre bevæger den sig, når du rammer den.' ],
				[ 'Hvad er forskellen på en medicinbold og en slam ball?', 'En medicinbold studser typisk, når den kastes, og bruges ofte til kast mod en makker eller væg. En slam ball er designet til at blive smidt hårdt i gulvet uden at studse tilbage, hvilket gør den mere velegnet til eksplosive, hårde kast.' ],
				[ 'Hvilket sjippetov skal jeg vælge?', 'Et almindeligt sjippetov med stof- eller PVC-line er godt til begyndere og almindelig kondition. Vil du hoppe hurtigt og præcist, fx til dobbeltunders, er et speed rope med tynd stålline og kuglelejer i håndtagene at foretrække.' ],
			],
		],
	];
	// Underkategoriernes tekster ligger i content/category-texts.php.
	$texts += require get_template_directory() . '/content/category-texts.php';

	foreach ( playform_sub_intros() as $slug => $intro ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && ! $term->description ) {
			wp_update_term( $term->term_id, 'product_cat', [ 'description' => $intro ] );
		}
	}
	foreach ( $texts as $slug => $t ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term ) {
			continue;
		}
		if ( ! $term->description && ! empty( $t['intro'] ) ) {
			wp_update_term( $term->term_id, 'product_cat', [ 'description' => $t['intro'] ] );
		}
		if ( ! get_term_meta( $term->term_id, 'pf_seo_text', true ) ) {
			update_term_meta( $term->term_id, 'pf_seo_text', $t['seo'] );
			update_term_meta( $term->term_id, 'pf_faq', $t['faq'] );
		}
	}
}

/**
 * Opretter artiklerne i content/articles-*.php som KLADDER, hvis de ikke findes i forvejen
 * (uanset status). Eksisterende indlæg røres aldrig, så redaktørens rettelser bevares.
 */
function playform_seed_articles(): void {
	$articles = [];
	foreach ( glob( get_template_directory() . '/content/articles-*.php' ) ?: [] as $file ) {
		$articles = array_merge( $articles, (array) require $file );
	}
	if ( ! $articles ) {
		return;
	}
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$author = get_users( [ 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ] );
	foreach ( $articles as $a ) {
		$exists = get_posts( [ 'name' => $a['slug'], 'post_type' => 'post', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ] );
		if ( $exists ) {
			continue;
		}
		$cat = term_exists( $a['cat'], 'category' ) ?: wp_insert_term( $a['cat'], 'category' );
		$id  = wp_insert_post(
			[
				'post_type'     => 'post',
				'post_status'   => 'draft',
				'post_name'     => $a['slug'],
				'post_title'    => $a['title'],
				'post_excerpt'  => $a['excerpt'],
				'post_content'  => trim( $a['html'] ),
				'post_category' => is_wp_error( $cat ) ? [] : [ (int) ( is_array( $cat ) ? $cat['term_id'] : $cat ) ],
				'post_author'   => $author ? (int) $author[0] : 1,
			]
		);
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		$src = get_template_directory() . '/assets/img/' . $a['img'];
		if ( is_readable( $src ) ) {
			$tmp = wp_tempnam( $a['img'] );
			copy( $src, $tmp );
			$att = media_handle_sideload( [ 'name' => $a['slug'] . '.jpg', 'tmp_name' => $tmp ], $id );
			if ( ! is_wp_error( $att ) ) {
				set_post_thumbnail( $id, $att );
			}
		}
	}
}

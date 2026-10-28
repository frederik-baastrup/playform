<?php
/**
 * Grundopsætning, når temaet aktiveres: danske URL'er, forside og standardsider.
 * Kan køres igen sikkert (opretter kun det, der mangler).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'playform_setup_site' );

function playform_setup_site(): void {
	update_option( 'timezone_string', 'Europe/Copenhagen' );
	update_option( 'date_format', 'j. F Y' );
	update_option( 'WPLANG', 'da_DK' );
	// Danske URL'er: /kategori/lobebaand/ og /produkt/navn/
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
		'privatlivspolitik'      => [ 'Privatlivspolitik', '<p>[Privatlivspolitik skal skrives]</p>' ],
		'cookies'                => [ 'Cookies', '<p>[Cookiepolitik skal skrives]</p>' ],
	];
	foreach ( $pages as $slug => [ $title, $content ] ) {
		if ( ! get_page_by_path( $slug ) ) {
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
		return '<p>Playform samler fitnessudstyr fra flere danske webshops ét sted. Vi henter produkter, priser og lagerstatus direkte fra butikkerne to gange i døgnet. Når du klikker på "Gå til butik", sendes du videre til butikken, hvor du handler som normalt.</p><h2>Butikker på Playform</h2><p>[Listen over butikker vises automatisk her.]</p>';
	}
	return '<p>Playform er gratis at bruge. Når du klikker videre til en butik og handler, får vi en lille provision fra butikken. Det koster ikke dig noget ekstra, og det påvirker ikke, hvilke produkter vi viser, eller hvordan vi sorterer dem.</p><p>Vi viser kun rabatter, som vi selv har målt i vores prishistorik – aldrig butikkernes egne "førpriser".</p>';
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
	];
	foreach ( playform_sub_intros() as $slug => $intro ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && ! $term->description ) {
			wp_update_term( $term->term_id, 'product_cat', [ 'description' => $intro ] );
		}
	}
	foreach ( playform_menu() as $item ) {
		$term = get_term_by( 'slug', $item['slug'], 'product_cat' );
		if ( ! $term ) {
			continue;
		}
		$t = $texts[ $item['slug'] ] ?? null;
		if ( $t && ! $term->description ) {
			wp_update_term( $term->term_id, 'product_cat', [ 'description' => $t['intro'] ] );
		}
		if ( $t && ! get_term_meta( $term->term_id, 'pf_seo_text', true ) ) {
			update_term_meta( $term->term_id, 'pf_seo_text', $t['seo'] );
			update_term_meta( $term->term_id, 'pf_faq', $t['faq'] );
		}
	}
}

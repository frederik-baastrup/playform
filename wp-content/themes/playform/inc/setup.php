<?php
/**
 * Grundopsætning, når temaet aktiveres: danske URL'er, forside og standardsider.
 * Kan køres igen sikkert (opretter kun det, der mangler).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'playform_setup_site' );

function playform_setup_site(): void {
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
	flush_rewrite_rules();
}

function playform_default_text( string $which ): string {
	if ( $which === 'butikker' ) {
		return '<p>Playform samler fitnessudstyr fra flere danske webshops ét sted. Vi henter produkter, priser og lagerstatus direkte fra butikkerne to gange i døgnet. Når du klikker på "Gå til butik", sendes du videre til butikken, hvor du handler som normalt.</p><h2>Butikker på Playform</h2><p>[Listen over butikker vises automatisk her.]</p>';
	}
	return '<p>Playform er gratis at bruge. Når du klikker videre til en butik og handler, får vi en lille provision fra butikken. Det koster ikke dig noget ekstra, og det påvirker ikke, hvilke produkter vi viser, eller hvordan vi sorterer dem.</p><p>Vi viser kun rabatter, som vi selv har målt i vores prishistorik – aldrig butikkernes egne "førpriser".</p>';
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

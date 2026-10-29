<?php
/**
 * Hovedmenu og mega-menu. Kategorierne svarer til Playform Feeds' kategoritræ.
 * Links i mega-menuen peger på kategorien med et filter. De bliver til egne landingssider senere.
 */

defined( 'ABSPATH' ) || exit;

/**
 * "Virtuelle" kategorier findes kun for at gruppere menuen (fx Kondiudstyr samler
 * Motionscykler og Romaskiner & crosstrainere). De har ingen egne produkter eller
 * landingsside – kun rigtige kategorier og underkategorier har det.
 */
function playform_is_virtual_category( string $slug ): bool {
	return in_array( $slug, [ 'kondiudstyr', 'styrketraening' ], true );
}

/** De virtuelle gruppers rigtige underkategorier, i den rækkefølge de skal vises. */
function playform_virtual_groups(): array {
	return [
		'kondiudstyr'   => [ 'label' => 'Kondiudstyr', 'img' => 'mm-crosstrainer.jpg', 'children' => [ 'motionscykler', 'romaskiner-crosstrainere' ] ],
		'styrketraening' => [ 'label' => 'Styrketræning', 'img' => 'mm-vaegte.jpg', 'children' => [ 'vaegte-kettlebells', 'styrke-baenke' ] ],
	];
}

function playform_category_url( string $slug, array $args = [] ): string {
	if ( isset( $args['sub'] ) ) {
		$slug = $args['sub'];
		unset( $args['sub'] );
	}
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	$url  = $term ? get_term_link( $term ) : home_url( '/' );
	if ( is_wp_error( $url ) ) {
		$url = home_url( '/' );
	}
	return $args ? add_query_arg( $args, $url ) : $url;
}

/** Underkategorier med produkter: [ [ navn, [ 'sub' => slug ], antal ], … ] */
function playform_sub_links( string $parent ): array {
	$out  = [];
	$defs = class_exists( '\Playform\Feeds\Config' ) ? ( \Playform\Feeds\Config::subcategories()[ $parent ] ?? [] ) : [];
	foreach ( $defs as $slug => $def ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && $term->count > 0 ) {
			$out[] = [ $term->name, [ 'sub' => $slug ], (int) $term->count ];
		}
	}
	return $out;
}

/**
 * Topmenuen. To slags punkter:
 * - almindelige: egen landingsside, ét niveau med underkategorier + pris + genveje (som før).
 * - grupper ('is_group'): ingen egen side, kun en hover-menu med de rigtige kategorier den
 *   samler ('primary'). Hver af dem åbner sine egne underkategorier i et andet panel ved hover.
 *
 * @return array<int,array>
 */
function playform_menu(): array {
	static $menu = null;
	if ( $menu !== null ) {
		return $menu;
	}
	$price = [
		'title' => 'Pris',
		'links' => [
			[ 'Under 1.000 kr', [ 'pris' => '0-1000' ] ],
			[ '1.000–3.000 kr', [ 'pris' => '1000-3000' ] ],
			[ '3.000–6.000 kr', [ 'pris' => '3000-6000' ] ],
			[ 'Over 6.000 kr', [ 'pris' => '6000-10000,10000-' ] ],
		],
	];
	$shortcuts = [ 'title' => 'Genveje', 'links' => [ [ 'På lager', [ 'egenskab' => 'lager' ] ], [ 'Laveste pris først', [ 'sortering' => 'pris-lav' ] ], [ 'Nyeste', [ 'sortering' => 'nyeste' ] ] ] ];
	$names     = [
		'motionscykler'            => [ 'Motionscykler', 'hero-motionscykler.jpg' ],
		'romaskiner-crosstrainere' => [ 'Romaskiner & crosstrainere', 'mm-crosstrainer.jpg' ],
		'vaegte-kettlebells'       => [ 'Vægte & kettlebells', 'mm-vaegte.jpg' ],
		'styrke-baenke'            => [ 'Styrke & bænke', 'mm-baenk.jpg' ],
	];
	$items = [
		[ 'Løbebånd', 'lobebaand', 'hero-lobebaand.jpg' ],
	];
	$menu = [];
	foreach ( $items as [ $label, $slug, $img ] ) {
		$cols = [];
		$subs = playform_sub_links( $slug );
		if ( $subs ) {
			$cols[] = [ 'title' => 'Kategorier', 'links' => $subs ];
		}
		$cols[] = $price;
		$cols[] = $shortcuts;
		$menu[] = [ 'is_group' => false, 'label' => $label, 'slug' => $slug, 'img' => $img, 'cols' => $cols ];
	}
	foreach ( playform_virtual_groups() as $group_slug => $group ) {
		$primary = [];
		foreach ( $group['children'] as $child_slug ) {
			[ $label, $img ] = $names[ $child_slug ];
			$primary[] = [ 'label' => $label, 'slug' => $child_slug, 'img' => $img, 'subs' => playform_sub_links( $child_slug ) ];
		}
		$menu[] = [ 'is_group' => true, 'label' => $group['label'], 'slug' => $group_slug, 'img' => $group['img'], 'primary' => $primary ];
	}
	foreach ( [ [ 'Yoga & pilates', 'yoga-pilates', 'mm-pilates.jpg' ], [ 'Restitution', 'restitution', 'mm-restitution.jpg' ], [ 'Tilbehør', 'tilbehor', 'mm-tilbehor.jpg' ] ] as [ $label, $slug, $img ] ) {
		$cols = [];
		$subs = playform_sub_links( $slug );
		if ( $subs ) {
			$cols[] = [ 'title' => 'Kategorier', 'links' => $subs ];
		}
		$cols[] = $price;
		$cols[] = $shortcuts;
		$menu[] = [ 'is_group' => false, 'label' => $label, 'slug' => $slug, 'img' => $img, 'cols' => $cols ];
	}
	return $menu;
}

<?php
/**
 * Hovedmenu og mega-menu. Kategorierne svarer til Playform Feeds' kategoritræ.
 * Links i mega-menuen peger på kategorien med et filter. De bliver til egne landingssider senere.
 */

defined( 'ABSPATH' ) || exit;

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

/** Underkategorier med produkter: [ [ navn, [ 'sub' => slug ] ], … ] */
function playform_sub_links( string $parent ): array {
	$out  = [];
	$defs = class_exists( '\Playform\Feeds\Config' ) ? ( \Playform\Feeds\Config::subcategories()[ $parent ] ?? [] ) : [];
	foreach ( $defs as $slug => $def ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && $term->count > 0 ) {
			$out[] = [ $term->name, [ 'sub' => $slug ] ];
		}
	}
	return $out;
}

/** @return array<int,array{label:string,slug:string,img:string,cols:array}> */
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
	$items = [
		[ 'Løbebånd', 'lobebaand', 'hero-lobebaand.jpg' ],
		[ 'Motionscykler', 'motionscykler', 'hero-motionscykler.jpg' ],
		[ 'Romaskiner & crosstrainere', 'romaskiner-crosstrainere', 'mm-crosstrainer.jpg' ],
		[ 'Vægte & kettlebells', 'vaegte-kettlebells', 'mm-vaegte.jpg' ],
		[ 'Styrke & bænke', 'styrke-baenke', 'mm-baenk.jpg' ],
		[ 'Yoga & pilates', 'yoga-pilates', 'mm-pilates.jpg' ],
		[ 'Restitution', 'restitution', 'mm-restitution.jpg' ],
		[ 'Tilbehør', 'tilbehor', 'mm-tilbehor.jpg' ],
	];
	$menu = [];
	foreach ( $items as [ $label, $slug, $img ] ) {
		$cols = [];
		$subs = playform_sub_links( $slug );
		if ( $subs ) {
			$cols[] = [ 'title' => 'Kategorier', 'links' => $subs ];
		}
		$cols[] = $price;
		$cols[] = [ 'title' => 'Genveje', 'links' => [ [ 'På lager', [ 'egenskab' => 'lager' ] ], [ 'Laveste pris først', [ 'sortering' => 'pris-lav' ] ], [ 'Nyeste', [ 'sortering' => 'nyeste' ] ] ] ];
		$menu[] = [ 'label' => $label, 'slug' => $slug, 'img' => $img, 'cols' => $cols ];
	}
	return $menu;
}

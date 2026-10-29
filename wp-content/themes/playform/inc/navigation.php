<?php
/**
 * Hovedmenu og mega-menu.
 *
 * Alle menupunkter har samme panel: Kategorier (venstre) | underkategorier for den markerede (midt) | Pris (højre).
 * Træningsmaskiner og Styrketræning er grupperingskategorier: de samler rigtige kategorier og har en
 * oversigtsside i stedet for et produktgitter.
 */

defined( 'ABSPATH' ) || exit;

/** Grupperingskategorier og de rigtige kategorier, de samler (bruges også af opsætningen). */
function playform_virtual_groups(): array {
	return [
		'traeningsmaskiner' => [ 'label' => 'Træningsmaskiner', 'children' => [ 'lobebaand', 'motionscykler', 'romaskiner-crosstrainere' ] ],
		'styrketraening'    => [ 'label' => 'Styrketræning', 'children' => [ 'vaegte-kettlebells', 'styrke-baenke' ] ],
	];
}

/** Er det en grupperingskategori (oversigtsside uden egne produkter)? */
function playform_is_virtual_category( string $slug ): bool {
	return isset( playform_virtual_groups()[ $slug ] ) || $slug === 'kondiudstyr';
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

/** Den øverste kategori (menupunktet) for en kategori-slug. */
function playform_root_slug( string $slug ): string {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( ! $term ) {
		return '';
	}
	foreach ( get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) as $id ) {
		$a = get_term( $id, 'product_cat' );
		if ( $a instanceof WP_Term ) {
			$term = $a;
		}
	}
	return $term->slug;
}

/**
 * Prisintervaller med produkter i en kategori (inkl. underkategorier). Caches, da de kræver en optælling.
 *
 * @return array<int,array{0:string,1:string}> [ [ label, nøgle til ?pris= ], … ]
 */
function playform_price_ranges( WP_Term $term ): array {
	$ranges = [
		'0-1000'     => [ 'Under 1.000 kr', 0, 1000 ],
		'1000-3000'  => [ '1.000–3.000 kr', 1000, 3000 ],
		'3000-6000'  => [ '3.000–6.000 kr', 3000, 6000 ],
		'6000-10000' => [ '6.000–10.000 kr', 6000, 10000 ],
		'10000-'     => [ 'Over 10.000 kr', 10000, PHP_INT_MAX ],
	];
	$all = get_transient( 'playform_price_ranges' );
	if ( ! is_array( $all ) ) {
		$all = [];
	}
	if ( ! isset( $all[ $term->term_id ] ) ) {
		global $wpdb;
		$ids   = array_merge( [ $term->term_id ], array_map( 'intval', (array) get_term_children( $term->term_id, 'product_cat' ) ) );
		$tt    = array_map( 'intval', (array) get_terms( [ 'taxonomy' => 'product_cat', 'include' => $ids, 'hide_empty' => false, 'fields' => 'tt_ids' ] ) );
		$found = [];
		if ( $tt ) {
			$in    = implode( ',', $tt );
			$cases = [];
			$i = 0;
			foreach ( $ranges as [ , $min, $max ] ) {
				$cases[] = $wpdb->prepare( 'SUM(CASE WHEN pm.meta_value + 0 >= %d AND pm.meta_value + 0 < %d THEN 1 ELSE 0 END)', $min, min( $max, 999999999 ) ) . ' AS r' . $i++;
			}
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$row = $wpdb->get_row(
				'SELECT ' . implode( ', ', $cases ) . " FROM (SELECT DISTINCT tr.object_id FROM {$wpdb->term_relationships} tr WHERE tr.term_taxonomy_id IN ($in)) x
				JOIN {$wpdb->posts} p ON p.ID = x.object_id AND p.post_status = 'publish'
				JOIN {$wpdb->postmeta} pm ON pm.post_id = x.object_id AND pm.meta_key = '_price'",
				ARRAY_A
			);
			$i = 0;
			foreach ( array_keys( $ranges ) as $key ) {
				if ( (int) ( $row[ 'r' . $i++ ] ?? 0 ) > 0 ) {
					$found[] = $key;
				}
			}
		}
		$all[ $term->term_id ] = $found;
		set_transient( 'playform_price_ranges', $all, 12 * HOUR_IN_SECONDS );
	}
	$out = [];
	foreach ( $all[ $term->term_id ] as $key ) {
		$out[] = [ $ranges[ $key ][0], $key ];
	}
	return $out;
}

/** Billede til oversigtssidernes kategorikort. */
function playform_category_image( string $slug ): string {
	$map = [
		'lobebaand'                => 'hero-lobebaand.jpg',
		'motionscykler'            => 'hero-motionscykler.jpg',
		'romaskine'                => 't-romaskine.jpg',
		'crosstrainer'             => 'mm-crosstrainer.jpg',
		'romaskiner-crosstrainere' => 'mm-crosstrainer.jpg',
		'vaegte-kettlebells'       => 'hero-kettlebells.jpg',
		'styrke-baenke'            => 'mm-baenk.jpg',
		'yoga-pilates'             => 'mm-pilates.jpg',
		'restitution'              => 'mm-restitution.jpg',
		'tilbehor'                 => 'mm-tilbehor.jpg',
	];
	return playform_asset( $map[ $slug ] ?? 't-hjemmegym.jpg' );
}

/**
 * Menuens opbygning: [ menupunkt, kategorier i venstre kolonne ]. null = menupunktets egne underkategorier.
 * En kategori kan angives som [ slug, visningsnavn ], fx når en underkategori står for sig selv.
 */
function playform_menu_spec(): array {
	return [
		[ 'traeningsmaskiner', [ 'lobebaand', 'motionscykler', [ 'romaskine', 'Romaskiner' ], [ 'crosstrainer', 'Crosstrainere' ] ] ],
		[ 'styrketraening', [ 'vaegte-kettlebells', 'styrke-baenke' ] ],
		[ 'yoga-pilates', null ],
		[ 'restitution', null ],
		[ 'tilbehor', null ],
	];
}

/** Visningsnavne fra menuen (fx "Romaskiner" for kategorien "Romaskine"), slug => navn. */
function playform_menu_labels(): array {
	// Kategorierne hedder ental (som folk søger), men i menu, faner og "Tilbud på …" læser flertal bedst.
	$out = [
		'romaskine'        => 'Romaskiner',
		'crosstrainer'     => 'Crosstrainere',
		'yogamaatte'       => 'Yogamåtter',
		'pilates-reformer' => 'Pilates reformere',
		'massagepistol'    => 'Massagepistoler',
		'foam-roller'      => 'Foam rollers',
		'traeningsmaatte'  => 'Træningsmåtter',
		'elastikker'       => 'Træningselastikker',
		'boksning'         => 'Boksudstyr',
		'traeningsbaenk'   => 'Træningsbænke',
		'spinningcykel'    => 'Spinningcykler',
		'kondicykel'       => 'Kondicykler',
		'liggecykel'       => 'Liggecykler',
		'airbike'          => 'Airbikes',
	];
	foreach ( playform_menu_spec() as [ $slug, $cats ] ) {
		if ( isset( playform_virtual_groups()[ $slug ] ) ) {
			$out[ $slug ] = playform_virtual_groups()[ $slug ]['label'];
		}
		foreach ( (array) $cats as $c ) {
			if ( is_array( $c ) ) {
				$out[ $c[0] ] = $c[1];
			}
		}
	}
	return $out;
}

/** Kategoriens navn, som det står i menuen. */
function playform_cat_label( WP_Term $term ): string {
	return playform_menu_labels()[ $term->slug ] ?? wp_specialchars_decode( $term->name );
}

/** Kategorier, der har en "Tilbud på …"-side: alle menupunkter og kategorierne i deres venstre kolonne. */
function playform_tilbud_slugs(): array {
	$out = [];
	foreach ( playform_menu_spec() as [ $slug, $cats ] ) {
		$out[] = $slug;
		if ( $cats === null ) {
			$cats = class_exists( '\Playform\Feeds\Config' ) ? array_keys( \Playform\Feeds\Config::subcategories()[ $slug ] ?? [] ) : [];
		}
		foreach ( $cats as $c ) {
			$out[] = is_array( $c ) ? $c[0] : $c;
		}
	}
	return $out;
}

/** URL til "Tilbud på …" for en kategori (eller alle tilbud). */
function playform_tilbud_url( ?WP_Term $term = null ): string {
	return $term ? trailingslashit( get_term_link( $term ) ) . user_trailingslashit( 'tilbud' ) : home_url( user_trailingslashit( 'tilbud' ) );
}

/** Står vi på en tilbudsside (/…/tilbud/ eller /tilbud/)? */
function playform_is_tilbud_page(): bool {
	return (bool) get_query_var( 'pf_tilbud' );
}

/**
 * Topmenuen. Hvert punkt: label, slug, url og 'primary' = kategorierne i venstre kolonne. Hver af dem har
 * 'subs' (midterkolonnen), 'prices' (højre kolonne) og 'tilbud' (link til Tilbud på …).
 *
 * @return array<int,array>
 */
function playform_menu(): array {
	static $menu = null;
	if ( $menu !== null ) {
		return $menu;
	}
	$menu = [];
	foreach ( playform_menu_spec() as [ $slug, $cats ] ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term ) {
			continue;
		}
		if ( $cats === null ) {
			$cats = array_map( static fn( $s ) => $s[1]['sub'], playform_sub_links( $slug ) );
		}
		$primary = [];
		foreach ( $cats as $c ) {
			$cslug = is_array( $c ) ? $c[0] : $c;
			$ct    = get_term_by( 'slug', $cslug, 'product_cat' );
			if ( ! $ct || $ct->count < 1 ) {
				continue;
			}
			$subs = [];
			foreach ( playform_sub_links( $cslug ) as [ $sl, $args ] ) {
				$subs[] = [ $sl, playform_category_url( $args['sub'] ) ];
			}
			$prices = [];
			foreach ( playform_price_ranges( $ct ) as [ $pl, $key ] ) {
				$prices[] = [ $pl, playform_category_url( $cslug, [ 'pris' => $key ] ) ];
			}
			$primary[] = [
				'label'  => playform_cat_label( $ct ),
				'slug'   => $cslug,
				'url'    => playform_category_url( $cslug ),
				'tilbud' => playform_tilbud_url( $ct ),
				'subs'   => $subs,
				'prices' => $prices,
			];
		}
		$menu[] = [
			'label'   => playform_cat_label( $term ),
			'slug'    => $slug,
			'url'     => playform_category_url( $slug ),
			'tilbud'  => playform_tilbud_url( $term ),
			'primary' => $primary,
		];
	}
	return $menu;
}

/** Populære kategorier, der vises i søgefeltet, før man har skrevet noget. */
function playform_search_shortcuts(): array {
	$out = [];
	foreach ( [ 'lobebaand', 'gaabaand', 'spinningcykel', 'romaskine', 'kettlebell', 'haandvaegte', 'massagepistol', 'yogamaatte' ] as $slug ) {
		$t = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $t && $t->count > 0 ) {
			$out[] = [ $t->name, get_term_link( $t ) ];
		}
	}
	return $out;
}

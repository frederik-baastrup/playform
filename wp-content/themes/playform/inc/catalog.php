<?php
/**
 * Filtre, sortering og paginering på kategorisider og søgning.
 *
 * Filtrene beregnes i PHP på kategoriens produkter (max et par tusinde), så vi kan vise
 * præcise tal ved hver mulighed. Hovedforespørgslen får derefter post__in med de matchende id'er.
 */

defined( 'ABSPATH' ) || exit;

const PLAYFORM_PER_PAGE = 30;

function playform_is_catalog( ?WP_Query $q = null ): bool {
	$q = $q ?: $GLOBALS['wp_query'];
	return $q->is_tax( 'product_cat' ) || ( $q->is_search() && in_array( $q->get( 'post_type' ), [ 'product', [ 'product' ] ], true ) ) || $q->is_post_type_archive( 'product' );
}

/** Filtergrupper. Hver mulighed har en test, der kører på et produkts metadata. */
function playform_filter_groups(): array {
	$range = static function ( string $key, ?float $min, ?float $max ) {
		return static function ( array $m ) use ( $key, $min, $max ) {
			$v = isset( $m[ $key ] ) ? (float) $m[ $key ] : null;
			return $v !== null && $v > 0 && ( $min === null || $v >= $min ) && ( $max === null || $v < $max );
		};
	};
	$shops = class_exists( '\Playform\Feeds\Config' ) ? \Playform\Feeds\Config::shops() : [];
	$shop_opts = [];
	foreach ( $shops as $key => $shop ) {
		$shop_opts[ $key ] = [ $shop['name'], static fn( array $m ) => str_contains( (string) ( $m['_pf_shop_list'] ?? '' ), "|$key|" ) ];
	}
	return [
		'pris'        => [
			'label'   => 'Pris',
			'options' => [
				'0-1000'     => [ 'Under 1.000 kr', $range( '_price', 0, 1000 ) ],
				'1000-3000'  => [ '1.000–3.000 kr', $range( '_price', 1000, 3000 ) ],
				'3000-6000'  => [ '3.000–6.000 kr', $range( '_price', 3000, 6000 ) ],
				'6000-10000' => [ '6.000–10.000 kr', $range( '_price', 6000, 10000 ) ],
				'10000-'     => [ 'Over 10.000 kr', $range( '_price', 10000, null ) ],
			],
		],
		'butik'       => [ 'label' => 'Butik', 'options' => $shop_opts ],
		'egenskab'    => [
			'label'   => 'Egenskaber',
			'all'     => true, // alle valgte skal være opfyldt
			'options' => [
				'lager'   => [ 'På lager', static fn( $m ) => ! empty( $m['_pf_in_stock'] ) ],
				'tilbud'  => [ 'Tilbud', static fn( $m ) => ! empty( $m['_pf_shop_sale'] ) ],
				'foldbar' => [ 'Foldbar', static fn( $m ) => ! empty( $m['_pf_foldable'] ) ],
				'incline' => [ 'Med incline', static fn( $m ) => ! empty( $m['_pf_incline'] ) ],
			],
		],
		'hastighed'   => [
			'label'   => 'Max hastighed',
			'only'    => [ 'lobebaand' ],
			'options' => [
				'0-13'  => [ 'Op til 12 km/t', $range( '_pf_max_speed', 0, 13 ) ],
				'13-17' => [ '13–16 km/t', $range( '_pf_max_speed', 13, 17 ) ],
				'17-'   => [ 'Over 16 km/t', $range( '_pf_max_speed', 17, null ) ],
			],
		],
		'brugervaegt' => [
			'label'   => 'Max brugervægt',
			'only'    => [ 'lobebaand', 'motionscykler', 'romaskiner-crosstrainere', 'styrke-baenke' ],
			'options' => [
				'0-121'   => [ 'Op til 120 kg', $range( '_pf_max_weight', 0, 121 ) ],
				'121-151' => [ '121–150 kg', $range( '_pf_max_weight', 121, 151 ) ],
				'151-'    => [ 'Over 150 kg', $range( '_pf_max_weight', 151, null ) ],
			],
		],
		'vaegt'       => [
			'label'   => 'Vægt',
			'only'    => [ 'vaegte-kettlebells' ],
			'options' => [
				'8'  => [ '8 kg', static fn( $m ) => (float) ( $m['_pf_weight_kg'] ?? 0 ) === 8.0 ],
				'12' => [ '12 kg', static fn( $m ) => (float) ( $m['_pf_weight_kg'] ?? 0 ) === 12.0 ],
				'16' => [ '16 kg', static fn( $m ) => (float) ( $m['_pf_weight_kg'] ?? 0 ) === 16.0 ],
				'20' => [ '20 kg', static fn( $m ) => (float) ( $m['_pf_weight_kg'] ?? 0 ) === 20.0 ],
				'24' => [ '24 kg', static fn( $m ) => (float) ( $m['_pf_weight_kg'] ?? 0 ) === 24.0 ],
			],
		],
	];
}

/** Valgte filtre fra URL'en, fx ?pris=3000-6000,6000-10000&egenskab=foldbar */
function playform_active_filters(): array {
	$active = [];
	foreach ( array_keys( playform_filter_groups() ) as $g ) {
		if ( empty( $_GET[ $g ] ) ) {
			continue;
		}
		$raw  = is_array( $_GET[ $g ] ) ? $_GET[ $g ] : explode( ',', (string) wp_unslash( $_GET[ $g ] ) );
		$vals = array_values( array_filter( array_map( 'sanitize_text_field', $raw ) ) );
		if ( $vals ) {
			$active[ $g ] = $vals;
		}
	}
	if ( ! empty( $_GET['tilbud'] ) ) {
		$active['egenskab'][] = 'tilbud';
	}
	return $active;
}

function playform_current_category_slug(): string {
	$term = get_queried_object();
	return $term instanceof WP_Term && $term->taxonomy === 'product_cat' ? $term->slug : '';
}

/** Hovedkategorien for den aktuelle side (for en underkategori: forælderen). */
function playform_current_top_slug(): string {
	$term = get_queried_object();
	if ( ! $term instanceof WP_Term || $term->taxonomy !== 'product_cat' ) {
		return '';
	}
	while ( $term->parent ) {
		$term = get_term( $term->parent, 'product_cat' );
	}
	return $term->slug;
}

/**
 * Som ovenfor, men stopper ved den nærmeste RIGTIGE kategori (springer de virtuelle
 * grupperings-kategorier som Kondiudstyr/Styrketræning over). Bruges til filterregler
 * ('only'), der er sat op pr. rigtig kategori og ikke kender til de virtuelle grupper.
 */
function playform_current_real_top_slug(): string {
	$term = get_queried_object();
	if ( ! $term instanceof WP_Term || $term->taxonomy !== 'product_cat' ) {
		return '';
	}
	while ( $term->parent ) {
		$parent_term = get_term( $term->parent, 'product_cat' );
		if ( ! $parent_term instanceof WP_Term || playform_is_virtual_category( $parent_term->slug ) ) {
			break;
		}
		$term = $parent_term;
	}
	return $term->slug;
}

/**
 * Beregner matchende produkter og tal pr. filtermulighed. Gemmes til resten af requestet.
 */
function playform_catalog_state(): array {
	static $state = null;
	if ( $state !== null ) {
		return $state;
	}
	$cat    = playform_current_category_slug();
	$q      = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
	$search = get_search_query();
	$args   = [
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	];
	if ( $cat ) {
		$args['tax_query'] = [ [ 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $cat ] ];
	}
	if ( $q !== '' || $search !== '' ) {
		$args['s'] = trim( "$search $q" );
	}
	$ids = get_posts( $args );
	update_meta_cache( 'post', $ids );

	$meta = [];
	foreach ( $ids as $id ) {
		$all = get_post_meta( $id );
		$m   = [];
		foreach ( [ '_price', '_pf_in_stock', '_pf_drop_percent', '_pf_foldable', '_pf_incline', '_pf_max_speed', '_pf_max_weight', '_pf_weight_kg', '_pf_shop_list', '_pf_shop_sale' ] as $k ) {
			if ( isset( $all[ $k ][0] ) ) {
				$m[ $k ] = $all[ $k ][0];
			}
		}
		// Tilbudssider viser kun produkter, butikken har på tilbud.
		if ( playform_is_tilbud_page() && empty( $m['_pf_shop_sale'] ) ) {
			continue;
		}
		$meta[ $id ] = $m;
	}

	$groups = playform_filter_groups();
	$active = playform_active_filters();

	$passes = static function ( array $m, ?string $skip ) use ( $groups, $active ): bool {
		foreach ( $active as $g => $vals ) {
			if ( $g === $skip || empty( $groups[ $g ] ) ) {
				continue;
			}
			$opts = $groups[ $g ]['options'];
			$hits = array_filter( $vals, static fn( $v ) => isset( $opts[ $v ] ) && $opts[ $v ][1]( $m ) );
			$known = array_filter( $vals, static fn( $v ) => isset( $opts[ $v ] ) );
			if ( ! $known ) {
				continue;
			}
			if ( ! empty( $groups[ $g ]['all'] ) ? count( $hits ) < count( $known ) : ! $hits ) {
				return false;
			}
		}
		return true;
	};

	$matching = [];
	foreach ( $meta as $id => $m ) {
		if ( $passes( $m, null ) ) {
			$matching[] = $id;
		}
	}

	$facets = [];
	foreach ( $groups as $g => $def ) {
		if ( ! empty( $def['only'] ) && ! in_array( playform_current_real_top_slug(), $def['only'], true ) ) {
			continue;
		}
		$opts = [];
		foreach ( $def['options'] as $key => [ $label, $test ] ) {
			if ( $key === 'tilbud' && playform_is_tilbud_page() ) {
				continue;
			}
			$n = 0;
			foreach ( $meta as $m ) {
				if ( ( empty( $def['all'] ) ? $passes( $m, $g ) : $passes( $m, null ) ) && $test( $m ) ) {
					$n++;
				}
			}
			$checked = in_array( $key, $active[ $g ] ?? [], true );
			if ( $n > 0 || $checked ) {
				$opts[] = [ 'key' => $key, 'label' => $label, 'count' => $n, 'checked' => $checked ];
			}
		}
		if ( $opts ) {
			$facets[ $g ] = [ 'label' => $def['label'], 'options' => $opts ];
		}
	}

	$tags = [];
	foreach ( $active as $g => $vals ) {
		foreach ( $vals as $v ) {
			if ( isset( $groups[ $g ]['options'][ $v ] ) ) {
				$rest = array_values( array_diff( $vals, [ $v ] ) );
				$tags[] = [ 'label' => $groups[ $g ]['options'][ $v ][0], 'url' => add_query_arg( $g, $rest ? implode( ',', $rest ) : false, remove_query_arg( [ 'paged', 'tilbud' ] ) ) ];
			}
		}
	}
	if ( $q !== '' ) {
		$tags[] = [ 'label' => '"' . $q . '"', 'url' => remove_query_arg( [ 'q', 'paged' ] ) ];
	}

	$state = [ 'ids' => $matching, 'facets' => $facets, 'tags' => $tags, 'total' => count( $matching ), 'q' => $q ];
	return $state;
}

add_action(
	'pre_get_posts',
	static function ( WP_Query $q ) {
		if ( is_admin() || ! $q->is_main_query() || ! playform_is_catalog( $q ) ) {
			return;
		}
		$state = playform_catalog_state_for_query( $q );
		$q->set( 'post__in', $state['ids'] ?: [ 0 ] );
		$q->set( 'posts_per_page', PLAYFORM_PER_PAGE );
		$q->set( 'post_type', 'product' );
		$q->set( 'meta_query', [ 'stock' => [ 'key' => '_pf_in_stock', 'type' => 'NUMERIC' ] ] );

		switch ( playform_current_sort() ) {
			case 'pris-lav':
				$q->set( 'meta_query', [ 'stock' => [ 'key' => '_pf_in_stock', 'type' => 'NUMERIC' ], 'price' => [ 'key' => '_price', 'type' => 'DECIMAL(12,2)' ] ] );
				$q->set( 'orderby', [ 'stock' => 'DESC', 'price' => 'ASC' ] );
				break;
			case 'pris-hoj':
				$q->set( 'meta_query', [ 'stock' => [ 'key' => '_pf_in_stock', 'type' => 'NUMERIC' ], 'price' => [ 'key' => '_price', 'type' => 'DECIMAL(12,2)' ] ] );
				$q->set( 'orderby', [ 'stock' => 'DESC', 'price' => 'DESC' ] );
				break;
			case 'nyeste':
				$q->set( 'orderby', [ 'stock' => 'DESC', 'date' => 'DESC' ] );
				break;
			case 'rabat':
				// Største rabat ifølge butikken først. Produkter uden tilbud kommer bagefter (NOT EXISTS).
				$q->set(
					'meta_query',
					[
						'stock' => [ 'key' => '_pf_in_stock', 'type' => 'NUMERIC' ],
						[
							'relation' => 'OR',
							'sale'     => [ 'key' => '_pf_sale_percent', 'type' => 'NUMERIC', 'compare' => 'EXISTS' ],
							[ 'key' => '_pf_sale_percent', 'compare' => 'NOT EXISTS' ],
						],
					]
				);
				$q->set( 'orderby', [ 'stock' => 'DESC', 'sale' => 'DESC' ] );
				break;
			default:
				// "Mest populære": på lager først, derefter produkter med billede og højest pris (de vigtigste maskiner).
				$q->set( 'meta_query', [ 'stock' => [ 'key' => '_pf_in_stock', 'type' => 'NUMERIC' ], 'price' => [ 'key' => '_price', 'type' => 'DECIMAL(12,2)' ] ] );
				$q->set( 'orderby', [ 'stock' => 'DESC', 'menu_order' => 'ASC', 'price' => 'DESC' ] );
		}
	},
	20
);

/** Valgt sortering. Tilbudssider sorterer som standard efter største rabat. */
function playform_current_sort(): string {
	$sort = isset( $_GET['sortering'] ) ? sanitize_key( $_GET['sortering'] ) : ''; // phpcs:ignore
	$ok   = [ 'populaer', 'pris-lav', 'pris-hoj', 'nyeste', 'rabat' ];
	return in_array( $sort, $ok, true ) ? $sort : ( playform_is_tilbud_page() ? 'rabat' : 'populaer' );
}

function playform_catalog_state_for_query( WP_Query $q ): array {
	return playform_catalog_state();
}

/** URL til en filtermulighed (slår den til/fra). */
function playform_filter_toggle_url( string $group, string $key ): string {
	$active = playform_active_filters()[ $group ] ?? [];
	$next   = in_array( $key, $active, true ) ? array_diff( $active, [ $key ] ) : array_merge( $active, [ $key ] );
	return add_query_arg( $group, $next ? implode( ',', $next ) : false, remove_query_arg( [ 'paged', 'tilbud' ], get_pagenum_link( 1, false ) ) );
}

/** Er de aktuelle URL-parametre præcis $args (bortset fra side og sortering)? */
function playform_query_equals( array $args ): bool {
	$get = [];
	foreach ( $_GET as $k => $v ) { // phpcs:ignore
		if ( in_array( $k, [ 'paged', 'sortering' ], true ) || is_array( $v ) ) {
			continue;
		}
		$get[ $k ] = (string) wp_unslash( $v );
	}
	$want = array_map( 'strval', $args );
	ksort( $get );
	ksort( $want );
	return $want && $get === $want;
}

/** URL uden filtre (beholder kun søgeordet ved søgning). */
function playform_reset_url(): string {
	$term = get_queried_object();
	if ( $term instanceof WP_Term && $term->taxonomy === 'product_cat' ) {
		return playform_is_tilbud_page() ? playform_tilbud_url( $term ) : get_term_link( $term );
	}
	if ( playform_is_tilbud_page() ) {
		return playform_tilbud_url();
	}
	$search = get_search_query();
	return $search !== '' ? add_query_arg( [ 's' => $search, 'post_type' => 'product' ], home_url( '/' ) ) : get_permalink( wc_get_page_id( 'shop' ) );
}

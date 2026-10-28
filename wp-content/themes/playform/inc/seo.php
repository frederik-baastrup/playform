<?php
/**
 * Grundlæggende SEO: titler, metabeskrivelser og noindex på filtrerede URL'er.
 * Bruger du senere Rank Math eller Yoast, slår temaet sine egne beskrivelser fra automatisk.
 */

defined( 'ABSPATH' ) || exit;

function playform_has_seo_plugin(): bool {
	return defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' );
}

/** Filtre, sortering og fritekst skaber uendeligt mange URL'er – de skal ikke i Google. */
add_filter(
	'wp_robots',
	static function ( array $robots ): array {
		if ( ! playform_is_catalog() ) {
			return $robots;
		}
		$noise = array_intersect( array_keys( $_GET ), array_merge( array_keys( playform_filter_groups() ), [ 'sortering', 'q', 'tilbud' ] ) ); // phpcs:ignore
		if ( $noise || is_search() ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
			unset( $robots['max-image-preview'] );
		}
		return $robots;
	}
);

add_filter(
	'document_title_parts',
	static function ( array $parts ): array {
		if ( playform_has_seo_plugin() ) {
			return $parts;
		}
		if ( is_tax( 'product_cat' ) ) {
			$parts['title'] = 'Køb ' . mb_strtolower( single_term_title( '', false ) ) . ' – stort udvalg fra danske shops';
		} elseif ( is_singular( 'product' ) ) {
			$parts['title'] = get_the_title() . ' – se pris';
		}
		$parts['site'] = 'Playform';
		unset( $parts['tagline'] );
		return $parts;
	}
);
add_filter( 'document_title_separator', static fn() => '|' );

add_action(
	'wp_head',
	static function () {
		if ( playform_has_seo_plugin() ) {
			return;
		}
		$desc = '';
		if ( is_front_page() ) {
			$desc = 'Fitnessudstyr fra flere danske webshops samlet ét sted: løbebånd, motionscykler, vægte, kettlebells, yoga og meget mere. Priserne opdateres to gange i døgnet.';
		} elseif ( is_tax( 'product_cat' ) ) {
			$desc = wp_strip_all_tags( term_description() );
		} elseif ( is_singular( 'product' ) ) {
			$id   = get_the_ID();
			$p    = wc_get_product( $id );
			$shop = playform_shop_name( (string) get_post_meta( $id, '_pf_best_shop', true ) );
			$desc = $p ? sprintf( '%s til %s hos %s. Se pris, lagerstatus og prishistorik på Playform.', $p->get_name(), playform_kr( (float) $p->get_price() ), $shop ) : '';
		}
		if ( $desc ) {
			echo '<meta name="description" content="' . esc_attr( mb_substr( $desc, 0, 160 ) ) . '">' . "\n";
		}
		if ( is_tax( 'product_cat' ) && ! array_intersect( array_keys( $_GET ), array_keys( playform_filter_groups() ) ) ) { // phpcs:ignore
			$paged = max( 1, (int) get_query_var( 'paged' ) );
			echo '<link rel="canonical" href="' . esc_url( $paged > 1 ? get_pagenum_link( $paged ) : get_term_link( get_queried_object() ) ) . '">' . "\n";
		}
	},
	2
);

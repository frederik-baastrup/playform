<?php
/** MIDLERTIDIG: skriver status til wp-content/uploads/playform-diag.json (højst hvert minut). Fjernes igen. */
defined( 'ABSPATH' ) || exit;
add_action(
	'shutdown',
	static function () {
		$file = WP_CONTENT_DIR . '/uploads/playform-diag.json';
		if ( is_file( $file ) && filemtime( $file ) > time() - 60 ) {
			return;
		}
		global $wpdb;
		$t     = $wpdb->prefix . 'pf_offers';
		$cols  = (array) $wpdb->get_col( "SHOW COLUMNS FROM $t" );
		$rules = (array) get_option( 'rewrite_rules', [] );
		$terms = [];
		foreach ( [ 'romaskine', 'romaskine-vand', 'romaskine-luftmodstand', 'traeningsmaskiner', 'lobebaand' ] as $s ) {
			$x           = get_term_by( 'slug', $s, 'product_cat' );
			$terms[ $s ] = $x ? [ 'count' => (int) $x->count, 'parent' => (int) $x->parent ] : null;
		}
		$data = [
			'time'            => gmdate( 'c' ),
			'url'             => (string) ( $_SERVER['REQUEST_URI'] ?? '' ), // phpcs:ignore
			'feeds_const'     => defined( 'PLAYFORM_FEEDS_VERSION' ) ? PLAYFORM_FEEDS_VERSION : null,
			'feeds_db'        => get_option( 'playform_feeds_db_version' ),
			'theme_const'     => PLAYFORM_THEME_VERSION,
			'theme_db'        => get_option( 'playform_content_version' ),
			'has_sale_since'  => in_array( 'sale_since', $cols, true ),
			'offers_sale'     => in_array( 'sale_since', $cols, true ) ? (int) $wpdb->get_var( "SELECT COUNT(*) FROM $t WHERE sale_since IS NOT NULL" ) : null,
			'products_sale'   => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = '_pf_shop_sale'" ),
			'terms'           => $terms,
			'rule_tilbud'     => $rules['^tilbud/?$'] ?? null,
			'rules_count'     => count( $rules ),
			'flush_flag'      => get_option( 'playform_flush_rules' ),
			'sales_class'     => class_exists( '\Playform\Feeds\Sales' ),
			'last_error'      => error_get_last(),
			'max_exec'        => ini_get( 'max_execution_time' ),
			'is_tilbud'       => function_exists( 'playform_is_tilbud_page' ) ? playform_is_tilbud_page() : null,
		];
		@file_put_contents( $file, wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) ); // phpcs:ignore
	}
);

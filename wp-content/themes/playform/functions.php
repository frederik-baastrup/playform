<?php
/**
 * Playform-temaet.
 */

defined( 'ABSPATH' ) || exit;

define( 'PLAYFORM_THEME_VERSION', '0.1.0' );

require_once __DIR__ . '/inc/navigation.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/catalog.php';
require_once __DIR__ . '/inc/setup.php';
require_once __DIR__ . '/inc/seo.php';

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
		add_theme_support( 'woocommerce' );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		$uri = get_template_directory_uri();
		wp_enqueue_style( 'playform-fonts', 'https://fonts.googleapis.com/css2?family=BDO+Grotesk:wght@400;500;600;700&display=swap', [], null );
		wp_enqueue_style( 'playform', "$uri/assets/css/main.css", [], PLAYFORM_THEME_VERSION );
		wp_enqueue_script( 'playform', "$uri/assets/js/main.js", [], PLAYFORM_THEME_VERSION, [ 'in_footer' => true, 'strategy' => 'defer' ] );
		// WooCommerce' egen CSS bruges ikke – temaet styler alt selv.
		wp_dequeue_style( 'woocommerce-general' );
		wp_dequeue_style( 'woocommerce-layout' );
		wp_dequeue_style( 'woocommerce-smallscreen' );
		wp_dequeue_style( 'wc-blocks-style' );
	},
	20
);

add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

add_action(
	'wp_head',
	static function () {
		echo '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	},
	1
);

<?php
namespace Playform\Feeds;

final class Plugin {

	public static function boot(): void {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		Install::maybe_upgrade();
		Cron::register();
		Frontend::register();
		if ( is_admin() ) {
			Admin::register();
			AdminTexts::register();
		}
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::add_command( 'playform', Cli::class );
		}
	}
}

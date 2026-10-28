<?php
namespace Playform\Feeds;

/**
 * Oversigt under Værktøjer › Playform Feeds: seneste import, antal pr. kategori og en knap til at køre importen nu.
 */
final class Admin {

	public static function register(): void {
		add_action( 'admin_menu', [ self::class, 'menu' ] );
		add_action( 'admin_post_playform_run_import', [ self::class, 'queue_import' ] );
	}

	public static function menu(): void {
		add_management_page( 'Playform Feeds', 'Playform Feeds', 'manage_woocommerce', 'playform-feeds', [ self::class, 'page' ] );
	}

	public static function queue_import(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( 'Ingen adgang' );
		}
		check_admin_referer( 'playform_run_import' );
		wp_schedule_single_event( time(), Cron::HOOK );
		wp_safe_redirect( admin_url( 'tools.php?page=playform-feeds&queued=1' ) );
		exit;
	}

	public static function page(): void {
		global $wpdb;
		$last = get_option( 'playform_feeds_last_run' );
		$t    = Offers::table();
		$cats = $wpdb->get_results( "SELECT category, COUNT(DISTINCT product_id) AS n, SUM(active AND in_stock) AS stock FROM $t GROUP BY category ORDER BY n DESC", ARRAY_A ) ?: [];
		$names = wp_list_pluck( Config::categories(), 'name' );
		echo '<div class="wrap"><h1>Playform Feeds</h1>';
		if ( isset( $_GET['queued'] ) ) {
			echo '<div class="notice notice-success"><p>Importen er sat i gang og kører i baggrunden.</p></div>';
		}
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'playform_run_import' );
		echo '<input type="hidden" name="action" value="playform_run_import"><p><button class="button button-primary">Kør import nu</button></p></form>';

		if ( $last ) {
			echo '<h2>Seneste import</h2><p>' . esc_html( get_date_from_gmt( $last['finished'], 'j. M Y H:i' ) ) . '</p>';
			echo '<table class="widefat striped"><thead><tr><th>Butik</th><th>Læst</th><th>Fravalgt</th><th>Nye</th><th>Ændret</th><th>Uændret</th><th>Væk</th><th>Fejl</th></tr></thead><tbody>';
			foreach ( $last['shops'] as $s ) {
				printf(
					'<tr><td>%s</td><td>%d</td><td>%d</td><td>%d</td><td>%d</td><td>%d</td><td>%d</td><td>%s</td></tr>',
					esc_html( $s['name'] ), $s['read'], $s['excluded'], $s['new'], $s['updated'], $s['unchanged'], $s['removed'], esc_html( $s['error'] )
				);
			}
			echo '</tbody></table>';
		}
		echo '<h2>Produkter pr. kategori</h2><table class="widefat striped" style="max-width:520px"><thead><tr><th>Kategori</th><th>Produkter</th><th>På lager</th></tr></thead><tbody>';
		foreach ( $cats as $c ) {
			$label = $names[ $c['category'] ] ?? ( $c['category'] === Config::uncategorized_slug() ? 'Ukategoriseret (skjult)' : $c['category'] );
			printf( '<tr><td>%s</td><td>%d</td><td>%d</td></tr>', esc_html( $label ), $c['n'], $c['stock'] );
		}
		echo '</tbody></table></div>';
	}
}

<?php
namespace Playform\Feeds;

/**
 * Oversigt under Værktøjer › Playform Feeds: seneste import, antal pr. kategori og en knap til at køre importen nu.
 */
final class Admin {

	public static function register(): void {
		add_action( 'admin_menu', [ self::class, 'menu' ] );
		add_action( 'admin_post_playform_run_import', [ self::class, 'queue_import' ] );
		add_action( 'admin_post_playform_save_ai', [ self::class, 'save_ai' ] );
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

	public static function save_ai(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Ingen adgang' );
		}
		check_admin_referer( 'playform_save_ai' );
		$key = isset( $_POST['api_key'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) ) : '';
		if ( $key !== '' ) {
			update_option( 'playform_anthropic_api_key', $key, false );
		}
		if ( ! empty( $_POST['remove_key'] ) ) {
			delete_option( 'playform_anthropic_api_key' );
		}
		update_option( 'playform_ai_texts_per_run', max( 0, (int) ( $_POST['texts_per_run'] ?? 0 ) ) );
		$provider = sanitize_key( $_POST['nl_provider'] ?? '' );
		update_option( 'playform_nl_provider', in_array( $provider, [ 'brevo', 'mailerlite' ], true ) ? $provider : '' );
		$nl_key = trim( sanitize_text_field( wp_unslash( $_POST['nl_key'] ?? '' ) ) );
		if ( $nl_key !== '' ) {
			update_option( 'playform_nl_key', $nl_key, false );
		}
		update_option( 'playform_nl_list', sanitize_text_field( wp_unslash( $_POST['nl_list'] ?? '' ) ) );
		update_option( 'playform_nl_template', (int) ( $_POST['nl_template'] ?? 0 ) );
		wp_safe_redirect( admin_url( 'tools.php?page=playform-feeds&saved=1' ) );
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
		echo '<h2>AI</h2><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'playform_save_ai' );
		echo '<input type="hidden" name="action" value="playform_save_ai"><table class="form-table"><tr><th><label for="api_key">Claude API-nøgle</label></th><td>';
		if ( defined( 'PLAYFORM_ANTHROPIC_API_KEY' ) ) {
			echo 'Sat i wp-config.php';
		} else {
			echo '<input type="password" id="api_key" name="api_key" class="regular-text" autocomplete="off" placeholder="' . ( Ai::enabled() ? 'Gemt – skriv en ny for at skifte' : 'sk-ant-…' ) . '">';
			if ( Ai::enabled() ) {
				echo ' <label><input type="checkbox" name="remove_key" value="1"> Fjern nøglen</label>';
			}
		}
		echo '</td></tr><tr><th><label for="texts_per_run">Nye tekster pr. import</label></th><td><input type="number" min="0" max="500" id="texts_per_run" name="texts_per_run" value="' . (int) get_option( 'playform_ai_texts_per_run', 0 ) . '"> <span class="description">0 = kun når du selv beder om det. Teksterne skal altid godkendes.</span></td></tr></table>';
		$prov = (string) get_option( 'playform_nl_provider', '' );
		echo '<h2>Nyhedsbrev</h2><table class="form-table">';
		echo '<tr><th><label for="nl_provider">Tjeneste</label></th><td><select id="nl_provider" name="nl_provider"><option value="">Ikke sat op</option><option value="brevo"' . selected( $prov, 'brevo', false ) . '>Brevo</option><option value="mailerlite"' . selected( $prov, 'mailerlite', false ) . '>MailerLite</option></select></td></tr>';
		echo '<tr><th><label for="nl_key">API-nøgle</label></th><td>' . ( defined( 'PLAYFORM_NEWSLETTER_KEY' ) ? 'Sat i wp-config.php' : '<input type="password" id="nl_key" name="nl_key" class="regular-text" autocomplete="off" placeholder="' . ( get_option( 'playform_nl_key' ) ? 'Gemt – skriv en ny for at skifte' : '' ) . '">' ) . '</td></tr>';
		echo '<tr><th><label for="nl_list">Liste-id (Brevo) / gruppe-id (MailerLite)</label></th><td><input type="text" id="nl_list" name="nl_list" value="' . esc_attr( (string) get_option( 'playform_nl_list', '' ) ) . '"></td></tr>';
		echo '<tr><th><label for="nl_template">Skabelon til bekræftelsesmail (kun Brevo)</label></th><td><input type="number" id="nl_template" name="nl_template" value="' . (int) get_option( 'playform_nl_template', 0 ) . '"> <span class="description">MailerLite: slå "Double opt-in for API" til i din konto.</span></td></tr></table>';
		echo '<p><button class="button">Gem</button></p></form>';
		echo '<h2>Produkter pr. kategori</h2><table class="widefat striped" style="max-width:520px"><thead><tr><th>Kategori</th><th>Produkter</th><th>På lager</th></tr></thead><tbody>';
		foreach ( $cats as $c ) {
			$label = $names[ $c['category'] ] ?? ( $c['category'] === Config::uncategorized_slug() ? 'Ukategoriseret (skjult)' : $c['category'] );
			printf( '<tr><td>%s</td><td>%d</td><td>%d</td></tr>', esc_html( $label ), $c['n'], $c['stock'] );
		}
		echo '</tbody></table></div>';
	}
}

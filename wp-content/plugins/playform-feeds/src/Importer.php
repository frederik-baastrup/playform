<?php
namespace Playform\Feeds;

/**
 * Hele importen: hent feeds → rens → sorter → gem tilbud → opdater produkter → gem prishistorik.
 */
final class Importer {

	private array $stats = [];
	private array $dirty = [];
	/** @var callable|null */
	private $log;

	public function __construct( ?callable $log = null ) {
		$this->log = $log;
	}

	/**
	 * @param string[]|null         $only   kun disse shops (nøgler)
	 * @param array<string,string>  $files  lokale filer pr. shop (til test); ellers hentes feedet
	 */
	public function run( ?array $only = null, array $files = [], int $limit = 0 ): array {
		if ( ! function_exists( 'download_url' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		@set_time_limit( 0 );
		wp_defer_term_counting( true );
		wp_suspend_cache_invalidation( true );

		$started = current_time( 'mysql', true );
		$today   = current_time( 'Y-m-d' );
		$shops   = Config::shops();

		foreach ( $shops as $key => $shop ) {
			if ( $only && ! in_array( $key, $only, true ) ) {
				continue;
			}
			$this->stats[ $key ] = [ 'name' => $shop['name'], 'read' => 0, 'excluded' => 0, 'new' => 0, 'updated' => 0, 'unchanged' => 0, 'removed' => 0, 'error' => '' ];
			try {
				$file = $files[ $key ] ?? FeedReader::download( $shop['url'], $key );
				$this->log( "Læser {$shop['name']} …" );
				$this->import_shop( $key, $shop['name'], $file, $started, $today, $limit );
				// Kun når feedet blev læst helt, må manglende varer markeres som væk.
				if ( ! $limit ) {
					$gone                           = Offers::deactivate_missing( $key, $started );
					$this->stats[ $key ]['removed'] = count( $gone );
					foreach ( $gone as $id ) {
						$this->dirty[ $id ] = true;
					}
				}
			} catch ( \Throwable $e ) {
				$this->stats[ $key ]['error'] = $e->getMessage();
				$this->log( "FEJL {$shop['name']}: " . $e->getMessage() );
			}
		}

		$this->log( 'Opdaterer ' . count( $this->dirty ) . ' produkter …' );
		$n = 0;
		foreach ( array_keys( $this->dirty ) as $product_id ) {
			ProductSync::refresh( (int) $product_id, $today );
			if ( ++$n % 500 === 0 ) {
				$this->log( "  $n …" );
				wp_cache_flush();
			}
		}

		// Én gang i døgnet genberegnes rabatter for alle, da de afhænger af datoen.
		if ( get_option( 'playform_feeds_drops_day' ) !== $today && ! $limit ) {
			$this->log( 'Genberegner rabatter …' );
			self::recalc_drops( $today );
			update_option( 'playform_feeds_drops_day', $today, false );
		}

		wp_suspend_cache_invalidation( false );
		wp_defer_term_counting( false );

		$summary = [ 'started' => $started, 'finished' => current_time( 'mysql', true ), 'shops' => $this->stats, 'products_refreshed' => $n ];
		update_option( 'playform_feeds_last_run', $summary, false );
		return $summary;
	}

	private function import_shop( string $key, string $name, string $file, string $now, string $today, int $limit ): void {
		$s = &$this->stats[ $key ];
		foreach ( FeedReader::read( $file ) as $row ) {
			if ( $limit && $s['read'] >= $limit ) {
				break;
			}
			$s['read']++;
			$offer = Normalizer::offer( $row, $key, $name );
			if ( $offer['shop_product_id'] === '' || $offer['url'] === '' ) {
				$s['excluded']++;
				continue;
			}
			$category = Classifier::classify( $offer );
			if ( $category === 'exclude' ) {
				$s['excluded']++;
				continue;
			}

			$existing   = Offers::find( $key, $offer['shop_product_id'] );
			$product_id = $existing ? (int) $existing['product_id'] : 0;
			if ( $product_id && ! get_post( $product_id ) ) {
				$product_id = 0; // produktet er slettet i WordPress
			}
			if ( ! $product_id ) {
				$product_id = Offers::product_for_ean( $offer['ean'], $key );
			}
			if ( ! $product_id ) {
				$product_id = ProductSync::create( $offer, $category );
			}

			$result = Offers::save( $offer, $product_id, $category, $now );
			ProductSync::save_texts_and_attributes( $product_id, $offer, $category );
			PriceHistory::record( $product_id, $key, $offer['in_stock'] ? $offer['price'] : 0, $today );

			if ( $result['is_new'] ) {
				$s['new']++;
			} elseif ( $result['changed'] ) {
				$s['updated']++;
			} else {
				$s['unchanged']++;
			}
			if ( $result['changed'] ) {
				$this->dirty[ $product_id ] = true;
			}
		}
	}

	/** Opdaterer rabat-felterne for alle produkter ud fra prishistorikken. */
	public static function recalc_drops( string $today ): int {
		global $wpdb;
		$t    = Offers::table();
		$rows = $wpdb->get_results( "SELECT product_id, MIN(price) AS price FROM $t WHERE active = 1 AND in_stock = 1 AND product_id > 0 GROUP BY product_id", ARRAY_A ) ?: [];
		$n    = 0;
		foreach ( $rows as $r ) {
			$id   = (int) $r['product_id'];
			$drop = PriceHistory::drop( $id, (float) $r['price'], $today );
			if ( $drop ) {
				update_post_meta( $id, ProductSync::META_DROP, $drop['percent'] );
				update_post_meta( $id, ProductSync::META_REF_PRICE, $drop['reference'] );
				$n++;
			} else {
				delete_post_meta( $id, ProductSync::META_DROP );
				delete_post_meta( $id, ProductSync::META_REF_PRICE );
			}
		}
		return $n;
	}

	private function log( string $msg ): void {
		if ( $this->log ) {
			( $this->log )( $msg );
		}
	}
}

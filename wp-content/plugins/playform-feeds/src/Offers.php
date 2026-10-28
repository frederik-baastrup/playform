<?php
namespace Playform\Feeds;

/**
 * Én række pr. produkt pr. butik. Et WooCommerce-produkt kan have flere tilbud (samme EAN hos flere shops).
 */
final class Offers {

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'pf_offers';
	}

	public static function find( string $shop, string $shop_product_id ): ?array {
		global $wpdb;
		$row = $wpdb->get_row(
			$wpdb->prepare( 'SELECT * FROM ' . self::table() . ' WHERE shop = %s AND shop_product_id = %s', $shop, $shop_product_id ),
			ARRAY_A
		);
		return $row ?: null;
	}

	/** Et andet tilbud (fra en anden butik) med samme EAN, som allerede hører til et produkt. */
	public static function product_for_ean( string $ean, string $not_shop ): int {
		global $wpdb;
		if ( $ean === '' ) {
			return 0;
		}
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				'SELECT product_id FROM ' . self::table() . ' WHERE ean = %s AND shop <> %s AND product_id > 0 ORDER BY id LIMIT 1',
				$ean,
				$not_shop
			)
		);
	}

	public static function hash( array $o ): string {
		return md5( implode( '|', [ $o['price'], $o['list_price'], $o['shipping'], (int) $o['in_stock'], $o['delivery'], $o['restock_date'], $o['url'], $o['image'], $o['name'] ] ) );
	}

	/** @return array{id:int,changed:bool,is_new:bool} */
	public static function save( array $o, int $product_id, string $category, string $now ): array {
		global $wpdb;
		$existing = self::find( $o['shop'], $o['shop_product_id'] );
		$hash     = self::hash( $o );
		$data     = [
			'product_id'    => $product_id,
			'ean'           => $o['ean'],
			'name'          => $o['name'],
			'brand'         => $o['brand'],
			'shop_category' => mb_substr( $o['shop_category'], 0, 190 ),
			'category'      => $category,
			'price'         => $o['price'],
			'list_price'    => $o['list_price'],
			'shipping'      => $o['shipping'],
			'in_stock'      => $o['in_stock'] ? 1 : 0,
			'stock_qty'     => $o['stock_qty'],
			'delivery'      => mb_substr( $o['delivery'], 0, 190 ),
			'restock_date'  => $o['restock_date'],
			'url'           => $o['url'],
			'image'         => $o['image'],
			'hash'          => $hash,
			'active'        => 1,
			'last_seen'     => $now,
		];
		if ( $existing ) {
			$wpdb->update( self::table(), $data, [ 'id' => $existing['id'] ] );
			$changed = $existing['hash'] !== $hash || ! (int) $existing['active'] || (int) $existing['product_id'] !== $product_id;
			return [ 'id' => (int) $existing['id'], 'changed' => $changed, 'is_new' => false ];
		}
		$data['shop']            = $o['shop'];
		$data['shop_product_id'] = $o['shop_product_id'];
		$data['first_seen']      = $now;
		$wpdb->insert( self::table(), $data );
		return [ 'id' => (int) $wpdb->insert_id, 'changed' => true, 'is_new' => true ];
	}

	/**
	 * Tilbud fra en butik, der ikke var med i dagens feed, markeres inaktive.
	 * @return int[] berørte produkt-id'er
	 */
	public static function deactivate_missing( string $shop, string $run_started ): array {
		global $wpdb;
		$t   = self::table();
		$ids = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT product_id FROM $t WHERE shop = %s AND active = 1 AND last_seen < %s AND product_id > 0", $shop, $run_started ) );
		$wpdb->query( $wpdb->prepare( "UPDATE $t SET active = 0 WHERE shop = %s AND active = 1 AND last_seen < %s", $shop, $run_started ) );
		return array_map( 'intval', $ids );
	}

	/** Aktive tilbud for et produkt, billigste på lager først. */
	public static function for_product( int $product_id, bool $active_only = true ): array {
		global $wpdb;
		$where = $active_only ? 'AND active = 1' : '';
		return $wpdb->get_results(
			$wpdb->prepare( 'SELECT * FROM ' . self::table() . " WHERE product_id = %d $where ORDER BY in_stock DESC, price ASC", $product_id ),
			ARRAY_A
		) ?: [];
	}
}

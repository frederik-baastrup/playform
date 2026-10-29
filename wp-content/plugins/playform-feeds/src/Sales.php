<?php
namespace Playform\Feeds;

/**
 * Butikkernes egne tilbud.
 *
 * Et produkt er "på tilbud", når den billigste butik på lager selv har markeret det med en førpris,
 * og tilbuddet højst er DAYS dage gammelt. Forbrugerombudsmanden regner en tilbudspris, der har stået
 * mere end to uger, som en ny normalpris – så faste "førpriser" falder ud af sig selv.
 *
 * Playform viser kun, at butikken har varen på tilbud, og prisen – aldrig butikkens førpris eller rabat i %.
 * _pf_sale_percent bruges kun til sortering.
 */
final class Sales {

	public const DAYS         = 14;
	public const META_SALE    = '_pf_shop_sale';
	public const META_PERCENT = '_pf_sale_percent';

	/** Genberegner tilbuds-felterne for alle produkter. Kaldes efter hver import (tilbud udløber med datoen). */
	public static function refresh_all( string $today ): int {
		global $wpdb;
		$t   = Offers::table();
		$cut = gmdate( 'Y-m-d', strtotime( $today . ' -' . ( self::DAYS - 1 ) . ' days' ) );

		// Den billigste aktive butik på lager pr. produkt – samme som den, produktkortet viser.
		$rows = $wpdb->get_results(
			"SELECT product_id, price, list_price, sale_since FROM $t WHERE active = 1 AND in_stock = 1 AND product_id > 0 ORDER BY product_id, price ASC, id ASC",
			ARRAY_A
		) ?: [];
		$sale = [];
		$seen = [];
		foreach ( $rows as $r ) {
			$id = (int) $r['product_id'];
			if ( isset( $seen[ $id ] ) ) {
				continue;
			}
			$seen[ $id ] = true;
			$price       = (float) $r['price'];
			$list        = (float) $r['list_price'];
			if ( $r['sale_since'] && $r['sale_since'] >= $cut && $list > $price && $price > 0 ) {
				$sale[ $id ] = (int) round( ( $list - $price ) / $list * 100 );
			}
		}

		$old = array_map( 'intval', (array) $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s", self::META_SALE ) ) );
		foreach ( array_diff( $old, array_keys( $sale ) ) as $id ) {
			delete_post_meta( $id, self::META_SALE );
			delete_post_meta( $id, self::META_PERCENT );
		}
		foreach ( $sale as $id => $percent ) {
			update_post_meta( $id, self::META_SALE, 1 );
			update_post_meta( $id, self::META_PERCENT, $percent );
		}
		return count( $sale );
	}
}

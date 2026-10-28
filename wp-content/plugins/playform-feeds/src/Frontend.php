<?php
namespace Playform\Feeds;

/**
 * Det temaet skal bruge: billeder fra feeds, tilbud, prishistorik og egenskaber.
 * Billederne hentes direkte fra butikkerne (de downloades ikke), så mediebiblioteket ikke fyldes med tusindvis af filer.
 */
final class Frontend {

	public static function register(): void {
		add_filter( 'woocommerce_product_get_image', [ self::class, 'product_image' ], 10, 5 );
		add_filter( 'woocommerce_product_add_to_cart_url', [ self::class, 'cart_url' ], 10, 2 );
		add_filter( 'woocommerce_loop_add_to_cart_args', [ self::class, 'loop_button_args' ], 10, 2 );
		add_action( 'pre_get_posts', [ self::class, 'hide_uncategorized' ] );
	}

	public static function image_url( int $product_id ): string {
		return (string) get_post_meta( $product_id, ProductSync::META_IMAGE, true );
	}

	public static function product_image( $html, $product, $size, $attr, $placeholder ) {
		$url = self::image_url( $product->get_id() );
		if ( ! $url || has_post_thumbnail( $product->get_id() ) ) {
			return $html;
		}
		return sprintf(
			'<img src="%s" alt="%s" loading="lazy" decoding="async" class="pf-product-image">',
			esc_url( $url ),
			esc_attr( $product->get_name() )
		);
	}

	public static function cart_url( $url, $product ) {
		return $product instanceof \WC_Product_External ? $product->get_product_url() : $url;
	}

	public static function loop_button_args( $args, $product ) {
		if ( $product instanceof \WC_Product_External ) {
			$args['attributes']['rel']    = 'sponsored nofollow noopener';
			$args['attributes']['target'] = '_blank';
		}
		return $args;
	}

	/** Ukategoriserede produkter (kladder) vises aldrig, men skal også holdes ude af kategorisider. */
	public static function hide_uncategorized( \WP_Query $q ): void {
		if ( is_admin() || ! $q->is_main_query() ) {
			return;
		}
		if ( $q->is_tax( 'product_cat', Config::uncategorized_slug() ) ) {
			$q->set_404();
		}
	}

	/** Tilbud til prislisten på produktsiden. */
	public static function offers( int $product_id ): array {
		$shops = Config::shops();
		return array_map(
			static function ( $o ) use ( $shops ) {
				return [
					'shop'     => $shops[ $o['shop'] ]['name'] ?? $o['shop'],
					'price'    => (float) $o['price'],
					'shipping' => $o['shipping'] === null ? null : (float) $o['shipping'],
					'in_stock' => (bool) $o['in_stock'],
					'delivery' => $o['delivery'],
					'restock'  => $o['restock_date'],
					'url'      => $o['url'],
				];
			},
			Offers::for_product( $product_id )
		);
	}

	public static function history( int $product_id, int $days = 90 ): array {
		return PriceHistory::daily( $product_id, $days );
	}

	public static function attributes( int $product_id ): array {
		return (array) get_post_meta( $product_id, ProductSync::META_ATTR, true );
	}

	public static function shop_names(): array {
		return array_column( Config::shops(), 'name' );
	}
}

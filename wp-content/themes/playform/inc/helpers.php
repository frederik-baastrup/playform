<?php
/**
 * Små hjælpefunktioner til skabelonerne.
 */

defined( 'ABSPATH' ) || exit;

function playform_asset( string $file ): string {
	return get_template_directory_uri() . '/assets/img/' . $file;
}

function playform_kr( float $amount ): string {
	return number_format( round( $amount ), 0, ',', '.' ) . ' kr';
}

/** Tynde linjeikoner (stroke 1.4–1.6) i samme stil som designet. */
function playform_icon( string $name, int $size = 20, float $stroke = 1.6 ): string {
	$paths = [
		'arrow-right' => '<path d="M9 5l7 7-7 7"/>',
		'arrow-left'  => '<path d="M15 5l-7 7 7 7"/>',
		'chevron'     => '<path d="M6 9l6 6 6-6"/>',
		'search'      => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>',
		'external'    => '<path d="M7 17L17 7M9 7h8v8"/>',
		'check'       => '<path d="M5 12l5 5 9-10"/>',
		'close'       => '<path d="M6 6l12 12M18 6L6 18"/>',
		'plus'        => '<path d="M12 5v14M5 12h14"/>',
		'grid'        => '<rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/>',
		'refresh'     => '<path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 4v5h-5"/>',
		'tag'         => '<path d="M3 12V4h8l9 9-8 8-9-9z"/><circle cx="7.5" cy="7.5" r="1.2"/>',
		'store'       => '<path d="M3 9l1.5-5h15L21 9"/><path d="M3 9v11h18V9"/><path d="M3 9h18"/><path d="M9 20v-6h6v6"/>',
		'filter'      => '<path d="M4 6h16M7 12h10M10 18h4"/>',
		'menu'        => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'clock'       => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
		'calendar'    => '<rect x="4" y="5.5" width="16" height="14" rx="2"/><path d="M4 10h16M8.5 3.5v4M15.5 3.5v4"/>',
		'product'     => '<rect x="4" y="15" width="15" height="3" rx="1.5"/><path d="M15 15l2-8h3"/><path d="M16 11h3"/>',
	];
	return sprintf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%2$s" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%3$s</svg>',
		$size,
		$stroke,
		$paths[ $name ] ?? ''
	);
}

function playform_shop_name( string $key ): string {
	$shops = class_exists( '\Playform\Feeds\Config' ) ? \Playform\Feeds\Config::shops() : [];
	return $shops[ $key ]['name'] ?? $key;
}

function playform_shop_names(): array {
	return class_exists( '\Playform\Feeds\Config' ) ? array_column( \Playform\Feeds\Config::shops(), 'name' ) : [];
}

/** Data til et produktkort, så kortet ser ens ud overalt. */
function playform_card_data( WC_Product $p ): array {
	$id       = $p->get_id();
	$shops    = (int) get_post_meta( $id, '_pf_shop_count', true );
	$shop     = playform_shop_name( (string) get_post_meta( $id, '_pf_best_shop', true ) );
	$drop     = (int) get_post_meta( $id, '_pf_drop_percent', true );
	$ref      = (float) get_post_meta( $id, '_pf_reference_price', true );
	$in_stock = (bool) get_post_meta( $id, '_pf_in_stock', true );
	$attrs    = (array) get_post_meta( $id, '_pf_attributes', true );
	$brand    = (string) get_post_meta( $id, '_pf_brand', true );
	return [
		'url'      => get_permalink( $id ),
		'name'     => $p->get_name(),
		'brand'    => $brand,
		'image'    => class_exists( '\\Playform\\Feeds\\Frontend' ) ? \Playform\Feeds\Frontend::image_url( $id ) : (string) get_post_meta( $id, '_pf_image', true ),
		'price'    => (float) $p->get_price(),
		'drop'     => $drop,
		'ref'      => $ref,
		'in_stock' => $in_stock,
		'sale'     => $in_stock && (bool) get_post_meta( $id, '_pf_shop_sale', true ),
		// Kontrolleret førpris: laveste pris i de 30 dage før tilbuddet, målt af os (se Sales::reference).
		'was'      => $in_stock && get_post_meta( $id, '_pf_shop_sale', true ) ? (float) get_post_meta( $id, '_pf_sale_ref', true ) : 0.0,
		'foldable' => ! empty( $attrs['foldable'] ),
		'shop'     => $shop,
		'shops'    => $shops,
	];
}

function playform_product_card( WC_Product $p, string $class = 'pcard' ): void {
	$d = playform_card_data( $p );
	?>
	<a href="<?php echo esc_url( $d['url'] ); ?>" class="<?php echo esc_attr( $class ); ?>">
		<span class="pcard__img">
			<?php if ( $d['image'] ) : ?>
				<img src="<?php echo esc_url( $d['image'] ); ?>" alt="<?php echo esc_attr( $d['name'] ); ?>" loading="lazy" decoding="async">
			<?php else : ?>
				<span class="pcard__ph"><?php echo playform_icon( 'product', 72, 1.2 ); // phpcs:ignore ?></span>
			<?php endif; ?>
			<span class="pcard__badges">
				<?php if ( $d['sale'] ) : ?><span class="badge badge--sale">Tilbud</span><?php endif; ?>
				<?php if ( $d['foldable'] ) : ?><span class="badge">Foldbar</span><?php endif; ?>
				<?php if ( ! $d['in_stock'] ) : ?><span class="badge badge--muted">Ikke på lager</span><?php endif; ?>
			</span>
		</span>
		<span class="pcard__body">
			<?php if ( $d['brand'] ) : ?><span class="pcard__brand"><?php echo esc_html( $d['brand'] ); ?></span><?php endif; ?>
			<span class="pcard__name"><?php echo esc_html( $d['name'] ); ?></span>
			<span class="pcard__price">
				<?php if ( $d['was'] > $d['price'] ) : ?>
					<span class="is-sale"><?php echo esc_html( playform_kr( $d['price'] ) ); ?></span>
					<s title="Laveste pris de 30 dage før tilbuddet"><?php echo esc_html( playform_kr( $d['was'] ) ); ?></s>
				<?php else : ?>
					<span><?php echo esc_html( playform_kr( $d['price'] ) ); ?></span>
				<?php endif; ?>
			</span>
			<span class="pcard__shop">
				<?php
				echo esc_html(
					$d['sale']
						? sprintf( 'Tilbud hos %s', $d['shop'] )
						: ( $d['shops'] > 1
						? sprintf( 'Billigst hos %s · %d butikker', $d['shop'], $d['shops'] )
						: sprintf( 'Hos %s', $d['shop'] ) )
				);
				?>
			</span>
		</span>
	</a>
	<?php
}

function playform_breadcrumbs( array $items ): void {
	echo '<nav class="crumbs" aria-label="Brødkrummer">';
	$last = count( $items ) - 1;
	foreach ( $items as $i => [ $label, $url ] ) {
		if ( $i > 0 ) {
			echo '<span aria-hidden="true">/</span>';
		}
		if ( $i === $last || ! $url ) {
			echo '<span aria-current="page">' . esc_html( $label ) . '</span>';
		} else {
			echo '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
		}
	}
	echo '</nav>';
}

/** Hvornår priserne sidst blev opdateret, fx "i dag kl. 05:30". */
function playform_last_update_label(): string {
	$last = get_option( 'playform_feeds_last_run' );
	if ( empty( $last['finished'] ) ) {
		return '';
	}
	$ts   = strtotime( $last['finished'] . ' UTC' );
	$day  = wp_date( 'Y-m-d', $ts ) === wp_date( 'Y-m-d' ) ? 'i dag' : wp_date( 'j. F', $ts );
	return $day . ' kl. ' . wp_date( 'H:i', $ts );
}

/** FAQ som strukturerede data til Google. */
function playform_faq_schema( array $faq ): void {
	$data = [
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array_map(
			static fn( $qa ) => [ '@type' => 'Question', 'name' => $qa[0], 'acceptedAnswer' => [ '@type' => 'Answer', 'text' => $qa[1] ] ],
			$faq
		),
	];
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
}

/** Product-schema til Google. Én butik = Offer, flere = AggregateOffer. */
function playform_product_schema( WC_Product $product, array $offers, array $d ): void {
	$data = [
		'@context' => 'https://schema.org',
		'@type'    => 'Product',
		'name'     => $product->get_name(),
		'url'      => get_permalink( $product->get_id() ),
	];
	if ( $d['image'] ) {
		$data['image'] = $d['image'];
	}
	if ( $d['brand'] ) {
		$data['brand'] = [ '@type' => 'Brand', 'name' => $d['brand'] ];
	}
	$ean = (string) get_post_meta( $product->get_id(), '_pf_ean', true );
	if ( strlen( $ean ) === 13 ) {
		$data['gtin13'] = $ean;
	}
	$desc = wp_strip_all_tags( get_the_content( null, false, $product->get_id() ) ?: (string) get_post_meta( $product->get_id(), '_pf_feed_description', true ) );
	if ( $desc ) {
		$data['description'] = mb_substr( $desc, 0, 500 );
	}
	if ( $offers ) {
		$prices = array_column( $offers, 'price' );
		$avail  = ! empty( array_filter( array_column( $offers, 'in_stock' ) ) ) ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';
		if ( count( $offers ) === 1 ) {
			$data['offers'] = [ '@type' => 'Offer', 'price' => number_format( $offers[0]['price'], 2, '.', '' ), 'priceCurrency' => 'DKK', 'availability' => $avail, 'url' => $data['url'], 'seller' => [ '@type' => 'Organization', 'name' => $offers[0]['shop'] ] ];
		} else {
			$data['offers'] = [ '@type' => 'AggregateOffer', 'lowPrice' => number_format( min( $prices ), 2, '.', '' ), 'highPrice' => number_format( max( $prices ), 2, '.', '' ), 'offerCount' => count( $offers ), 'priceCurrency' => 'DKK', 'availability' => $avail ];
		}
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
}

/** Danske måneds- og dagnavne, også hvis WordPress' danske sprogpakke ikke er installeret. */
add_filter(
	'wp_date',
	static function ( $date ) {
		return strtr(
			(string) $date,
			[
				'January' => 'januar', 'February' => 'februar', 'March' => 'marts', 'April' => 'april', 'May' => 'maj', 'June' => 'juni',
				'July' => 'juli', 'August' => 'august', 'September' => 'september', 'October' => 'oktober', 'November' => 'november', 'December' => 'december',
				'Jan' => 'jan.', 'Feb' => 'feb.', 'Mar' => 'mar.', 'Apr' => 'apr.', 'Jun' => 'jun.', 'Jul' => 'jul.', 'Aug' => 'aug.', 'Sep' => 'sep.', 'Oct' => 'okt.', 'Nov' => 'nov.', 'Dec' => 'dec.',
				'Monday' => 'mandag', 'Tuesday' => 'tirsdag', 'Wednesday' => 'onsdag', 'Thursday' => 'torsdag', 'Friday' => 'fredag', 'Saturday' => 'lørdag', 'Sunday' => 'søndag',
			]
		);
	}
);

/**
 * Indlæggets udvalgte billede – men kun hvis det er stort nok til at stå skarpt.
 * Et lille billede strakt ud bliver pixeleret; så hellere intet billede (eller et reservebillede).
 */
function playform_post_image( $post, int $min_width, string $size = 'large' ): string {
	$id = get_post_thumbnail_id( $post );
	if ( ! $id ) {
		return '';
	}
	$meta = wp_get_attachment_metadata( $id );
	if ( (int) ( $meta['width'] ?? 0 ) < $min_width ) {
		return '';
	}
	return (string) wp_get_attachment_image_url( $id, $size );
}

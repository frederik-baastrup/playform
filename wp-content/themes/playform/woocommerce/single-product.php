<?php
/**
 * Produktside.
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		continue;
	}
	$id       = $product->get_id();
	$d        = playform_card_data( $product );
	$offers   = class_exists( '\Playform\Feeds\Frontend' ) ? \Playform\Feeds\Frontend::offers( $id ) : [];
	$history  = class_exists( '\Playform\Feeds\Frontend' ) ? \Playform\Feeds\Frontend::history( $id, 90 ) : [];
	$attrs    = (array) get_post_meta( $id, '_pf_attributes', true );
	$cat_slug = (string) get_post_meta( $id, '_pf_category', true );
	$cat_term = $cat_slug ? get_term_by( 'slug', $cat_slug, 'product_cat' ) : null;
	// Den mest præcise kategori, produktet ligger i (fx Kettlebells frem for Vægte & kettlebells).
	foreach ( (array) wp_get_post_terms( get_the_ID(), 'product_cat' ) as $pt ) {
		if ( $pt instanceof WP_Term && ( ! $cat_term || count( get_ancestors( $pt->term_id, 'product_cat', 'taxonomy' ) ) > count( get_ancestors( $cat_term->term_id, 'product_cat', 'taxonomy' ) ) ) ) {
			$cat_term = $pt;
		}
	}
	$best     = $offers[0] ?? null;
	$shops_n  = count( array_unique( array_column( $offers, 'shop' ) ) );
	$restock  = (string) get_post_meta( $id, '_pf_restock_date', true );
	$text     = trim( get_the_content() );
	$feed_txt = (string) get_post_meta( $id, '_pf_feed_description', true );
	$ean      = (string) get_post_meta( $id, '_pf_ean', true );

	$facts = [];
	if ( ! empty( $attrs['max_speed'] ) ) {
		$facts[] = 'Op til ' . rtrim( rtrim( number_format( (float) $attrs['max_speed'], 1, ',', '' ), '0' ), ',' ) . ' km/t';
	}
	if ( ! empty( $attrs['max_weight'] ) ) {
		$facts[] = 'Max ' . (int) $attrs['max_weight'] . ' kg brugervægt';
	}
	if ( ! empty( $attrs['weight_kg'] ) ) {
		$facts[] = rtrim( rtrim( number_format( (float) $attrs['weight_kg'], 1, ',', '' ), '0' ), ',' ) . ' kg';
	}
	if ( ! empty( $attrs['foldable'] ) ) {
		$facts[] = 'Foldbar';
	}
	if ( ! empty( $attrs['incline'] ) ) {
		$facts[] = 'Med incline';
	}

	$crumbs = [ [ 'Forside', home_url( '/' ) ] ];
	if ( $cat_term ) {
		foreach ( array_reverse( get_ancestors( $cat_term->term_id, 'product_cat', 'taxonomy' ) ) as $aid ) {
			$a = get_term( $aid, 'product_cat' );
			if ( $a instanceof WP_Term ) {
				$crumbs[] = [ $a->name, get_term_link( $a ) ];
			}
		}
		$crumbs[] = [ $cat_term->name, get_term_link( $cat_term ) ];
	}
	$crumbs[] = [ $product->get_name(), '' ];

	if ( $best && ! $best['in_stock'] && $restock ) {
		$stock_txt = 'Forventes på lager ' . wp_date( 'j. F', strtotime( $restock ) );
	} elseif ( $best && $best['in_stock'] ) {
		$stock_txt = 'På lager hos ' . $best['shop'];
	} else {
		$stock_txt = 'Ikke på lager lige nu';
	}
	?>
	<section class="wrap pd-top">
		<?php playform_breadcrumbs( $crumbs ); ?>
		<div class="pd">
			<div class="pd__media">
				<?php if ( $d['image'] ) : ?>
					<img src="<?php echo esc_url( $d['image'] ); ?>" alt="<?php echo esc_attr( $d['name'] ); ?>" fetchpriority="high">
				<?php else : ?>
					<span class="pcard__ph"><?php echo playform_icon( 'product', 160, 1 ); // phpcs:ignore ?></span>
				<?php endif; ?>
				<span class="pcard__badges">
					<?php if ( $d['sale'] ) : ?><span class="badge badge--sale">Tilbud hos <?php echo esc_html( $d['shop'] ); ?></span><?php endif; ?>
					<?php if ( $d['foldable'] ) : ?><span class="badge">Foldbar</span><?php endif; ?>
				</span>
			</div>

			<div class="pd__info">
				<div class="pd__head">
					<?php if ( $d['brand'] ) : ?><span class="pd__brand"><?php echo esc_html( $d['brand'] ); ?></span><?php endif; ?>
					<h1><?php echo esc_html( $d['name'] ); ?></h1>
					<?php if ( $facts ) : ?>
						<ul class="pd__chips">
							<?php foreach ( $facts as $f ) : ?><li><?php echo esc_html( $f ); ?></li><?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<div class="pricebox">
					<div class="pricebox__top">
						<div>
							<span class="muted"><?php echo $shops_n > 1 ? 'Laveste pris lige nu' : 'Pris'; ?></span>
							<span class="pricebox__price"><?php echo esc_html( playform_kr( $d['price'] ) ); ?></span>
							<?php if ( $d['drop'] && $d['ref'] ) : ?>
								<span class="pricebox__ref">Laveste pris de 30 dage før: <s><?php echo esc_html( playform_kr( $d['ref'] ) ); ?></s></span>
							<?php endif; ?>
						</div>
						<span class="stock<?php echo $best && $best['in_stock'] ? ' stock--in' : ''; ?>"><span class="stock__dot"></span><?php echo esc_html( $stock_txt ); ?></span>
					</div>
					<?php if ( $best ) : ?>
						<a href="<?php echo esc_url( $best['url'] ); ?>" class="btn-shop" rel="sponsored nofollow noopener" target="_blank">Gå til butik <?php echo playform_icon( 'external', 18 ); // phpcs:ignore ?></a>
						<span class="pricebox__note">
							<?php
							$bits = [];
							if ( $best['delivery'] ) {
								$bits[] = 'Levering ' . $best['delivery'];
							}
							if ( $best['shipping'] !== null ) {
								$bits[] = $best['shipping'] > 0 ? 'Fragt ' . playform_kr( $best['shipping'] ) : 'Gratis fragt';
							}
							$bits[] = 'Sælges af ' . $best['shop'];
							echo esc_html( implode( ' · ', $bits ) );
							?>
						</span>
						<span class="pricebox__legal">Du sendes videre til <?php echo esc_html( $best['shop'] ); ?>, hvor du gennemfører købet. Prisen er inkl. moms<?php $u = playform_last_update_label(); echo $u ? ' og blev opdateret ' . esc_html( $u ) : ''; ?>. Vi kan modtage provision, hvis du handler.</span>
					<?php endif; ?>
				</div>

				<?php if ( $shops_n > 1 ) : ?>
					<a href="#priser" class="pd__compare">Sammenlign priser fra <?php echo (int) $shops_n; ?> butikker ↓</a>
				<?php endif; ?>

				<ul class="pd__trust">
					<li><?php echo playform_icon( 'refresh', 20, 1.4 ); // phpcs:ignore ?><span><b>Opdateret 2× dagligt</b>Pris og lagerstatus hentes direkte fra butikken</span></li>
					<li><?php echo playform_icon( 'store', 20, 1.4 ); // phpcs:ignore ?><span><b>Du handler hos butikken</b>Levering, retur og garanti følger butikkens vilkår</span></li>
					<li><?php echo playform_icon( 'grid', 20, 1.4 ); // phpcs:ignore ?><span><b>Udstyr fra flere shops</b>Vi samler danske webshops ét sted</span></li>
				</ul>
			</div>
		</div>
	</section>

	<?php
	// Blokkene under toppen parres to og to, så der ikke står halvtomme rækker:
	// flere butikker: Sammenlign | Prishistorik, så Om produktet | Specifikationer.
	// én butik:        Om produktet | Specifikationer, så Prishistorik.
	ob_start();
	?>
	<div>
		<h2>Sammenlign priser</h2>
		<p class="muted"><?php echo (int) $shops_n; ?> butikker sælger dette produkt. Sorteret efter laveste pris.</p>
		<div class="offers">
			<?php foreach ( $offers as $i => $o ) : ?>
				<div class="offer<?php echo $i === 0 ? ' offer--best' : ''; ?>">
					<span class="offer__shop">
						<b><?php echo esc_html( $o['shop'] ); ?></b>
						<?php if ( $i === 0 ) : ?><span class="badge badge--accent">Billigst</span><?php endif; ?>
						<span class="stock<?php echo $o['in_stock'] ? ' stock--in' : ''; ?>"><span class="stock__dot"></span><?php echo $o['in_stock'] ? 'På lager' : 'Ikke på lager'; ?></span>
					</span>
					<span class="offer__meta"><?php echo esc_html( trim( ( $o['delivery'] ? 'Levering ' . $o['delivery'] : '' ) ) ); ?><br><?php echo $o['shipping'] === null ? '' : esc_html( $o['shipping'] > 0 ? 'Fragt ' . playform_kr( $o['shipping'] ) : 'Gratis fragt' ); ?></span>
					<span class="offer__price"><?php echo esc_html( playform_kr( $o['price'] ) ); ?></span>
					<a href="<?php echo esc_url( $o['url'] ); ?>" class="btn-out" rel="sponsored nofollow noopener" target="_blank">Gå til butik</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	$compare_html = ob_get_clean();
	if ( $shops_n < 2 ) {
		$compare_html = '';
	}

	ob_start();
	if ( count( $history ) >= 2 ) :
		?>
		<div>
			<h2>Prishistorik</h2>
			<p class="muted">Laveste pris pr. dag, målt af Playform</p>
			<?php get_template_part( 'template-parts/price-chart', null, [ 'history' => $history ] ); ?>
		</div>
		<?php
	endif;
	$history_html = ob_get_clean();

	ob_start();
	?>
	<div class="prose" id="om">
		<h2>Om produktet</h2>
		<?php
		if ( $text ) {
			the_content();
		} elseif ( $feed_txt ) {
			echo '<p>' . esc_html( $feed_txt ) . '</p>';
		} else {
			echo '<p class="muted">Der er endnu ingen beskrivelse af produktet.</p>';
		}
		$pros = array_filter( (array) get_post_meta( $id, '_pf_pros', true ) );
		$cons = array_filter( (array) get_post_meta( $id, '_pf_cons', true ) );
		if ( $pros || $cons ) :
			?>
			<div class="pc">
				<?php if ( $pros ) : ?>
					<div class="pc__box"><h3>Fordele</h3><ul><?php foreach ( $pros as $x ) : ?><li><span class="pc__plus" aria-hidden="true">+</span><?php echo esc_html( $x ); ?></li><?php endforeach; ?></ul></div>
				<?php endif; ?>
				<?php if ( $cons ) : ?>
					<div class="pc__box"><h3>Ulemper</h3><ul><?php foreach ( $cons as $x ) : ?><li><span class="pc__minus" aria-hidden="true">–</span><?php echo esc_html( $x ); ?></li><?php endforeach; ?></ul></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
	$about_html = ob_get_clean();

	ob_start();
	?>
	<div>
		<h2>Specifikationer</h2>
		<dl class="specs">
			<?php
			$rows = array_filter(
				[
					'Mærke'          => $d['brand'],
					'Max hastighed'  => ! empty( $attrs['max_speed'] ) ? rtrim( rtrim( number_format( (float) $attrs['max_speed'], 1, ',', '' ), '0' ), ',' ) . ' km/t' : '',
					'Max brugervægt' => ! empty( $attrs['max_weight'] ) ? (int) $attrs['max_weight'] . ' kg' : '',
					'Vægt'           => ! empty( $attrs['weight_kg'] ) ? rtrim( rtrim( number_format( (float) $attrs['weight_kg'], 1, ',', '' ), '0' ), ',' ) . ' kg' : '',
					'Foldbar'        => ! empty( $attrs['foldable'] ) ? 'Ja' : '',
					'Incline'        => array_key_exists( 'incline', $attrs ) ? ( $attrs['incline'] ? 'Ja' : 'Nej' ) : '',
					'Kategori'       => $cat_term ? wp_specialchars_decode( $cat_term->name ) : '',
					'Butikker'       => $shops_n > 1 ? $shops_n . ' butikker' : ( $best ? $best['shop'] : '' ),
					'EAN'            => $ean,
				]
			);
			foreach ( $rows as $k => $v ) {
				echo '<div><dt>' . esc_html( $k ) . '</dt><dd>' . esc_html( $v ) . '</dd></div>';
			}
			?>
		</dl>
		<p class="specs__note">Oplysningerne stammer fra butikkernes produktdata og kan variere.</p>
	</div>
	<?php
	$specs_html = ob_get_clean();

	$rows_out = $compare_html
		? [ [ $compare_html, $history_html ], [ $about_html, $specs_html ] ]
		: [ [ $about_html, $specs_html ], [ $history_html, '' ] ];
	foreach ( $rows_out as $i => [ $a, $b ] ) {
		if ( trim( $a . $b ) === '' ) {
			continue;
		}
		echo '<section class="wrap section"' . ( $i === 0 && $compare_html ? ' id="priser"' : '' ) . '><div class="two' . ( $b === '' ? ' two--half' : '' ) . '">' . $a . $b . '</div></section>'; // phpcs:ignore
	}
	?>

	<?php
	if ( $cat_term ) {
		$price   = max( 1, $d['price'] );
		$related = get_posts(
			[
				'post_type'      => 'product',
				'posts_per_page' => 4,
				'post__not_in'   => [ $id ],
				'fields'         => 'ids',
				'orderby'        => 'rand',
				'tax_query'      => [ [ 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $cat_term->term_id ] ],
				'meta_query'     => [
					[ 'key' => '_pf_in_stock', 'value' => 1, 'type' => 'NUMERIC' ],
					[ 'key' => '_price', 'value' => [ $price * 0.6, $price * 1.6 ], 'compare' => 'BETWEEN', 'type' => 'NUMERIC' ],
				],
			]
		);
		if ( $related ) {
			echo '<section class="wrap section section--end"><div class="section__head"><h2>Lignende ' . esc_html( mb_strtolower( playform_cat_label( $cat_term ) ) ) . '</h2><span class="section__line"></span><a class="section__more" href="' . esc_url( get_term_link( $cat_term ) ) . '">Se alle</a></div><div class="grid grid--4">';
			foreach ( $related as $rid ) {
				$rp = wc_get_product( $rid );
				if ( $rp ) {
					playform_product_card( $rp );
				}
			}
			echo '</div></section>';
		}
	}

	playform_product_schema( $product, $offers, $d );
endwhile;

get_footer();

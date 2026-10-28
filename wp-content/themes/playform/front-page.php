<?php
defined( 'ABSPATH' ) || exit;
get_header();

/** Produkter til forsidens sliders. Caches i 6 timer. */
function playform_front_products( string $which ): array {
	$key = "playform_front_$which";
	$ids = get_transient( $key );
	if ( $ids === false ) {
		$base = [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => 12,
			'no_found_rows'  => true,
			'meta_query'     => [
				[ 'key' => '_pf_in_stock', 'value' => 1, 'type' => 'NUMERIC' ],
				[ 'key' => '_pf_image', 'value' => '', 'compare' => '!=' ],
			],
		];
		if ( $which === 'deals' ) {
			$base['meta_query'][] = [ 'key' => '_pf_drop_percent', 'compare' => 'EXISTS' ];
			$base['meta_key']     = '_pf_drop_percent';
			$base['orderby']      = 'meta_value_num';
			$base['order']        = 'DESC';
		} elseif ( $which === 'new' ) {
			$base['orderby'] = 'rand';
			$base['tax_query'] = [ [ 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => [ 'lobebaand', 'motionscykler', 'romaskiner-crosstrainere', 'styrke-baenke', 'restitution' ] ] ];
		} else {
			// "Populære": større maskiner og udstyr fra hovedkategorierne, blandet hver dag.
			$base['tax_query']    = [ [ 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => [ 'lobebaand', 'motionscykler', 'romaskiner-crosstrainere', 'vaegte-kettlebells', 'restitution' ] ] ];
			$base['meta_query'][] = [ 'key' => '_price', 'value' => 400, 'compare' => '>=', 'type' => 'NUMERIC' ];
			$base['orderby']      = 'rand';
		}
		$ids = get_posts( $base );
		set_transient( $key, $ids, 6 * HOUR_IN_SECONDS );
	}
	return array_filter( array_map( 'wc_get_product', $ids ) );
}

$hero = [
	[ 'slug' => 'lobebaand', 'title' => 'Løbebånd', 'img' => 'hero-lobebaand.jpg', 'big' => true, 'subs' => [ [ 'Foldbart løbebånd', [ 'sub' => 'foldbart-lobebaand' ] ], [ 'Gåbånd', [ 'sub' => 'gaabaand' ] ], [ 'Løbebånd med incline', [ 'sub' => 'lobebaand-med-incline' ] ], [ 'Billigt løbebånd', [ 'sortering' => 'pris-lav' ] ] ] ],
	[ 'slug' => 'kettlebell', 'title' => 'Kettlebells', 'img' => 'hero-kettlebells.jpg', 'subs' => [ [ 'Kettlebell 12 kg', [ 'sub' => 'kettlebell', 'vaegt' => '12' ] ], [ 'Kettlebell 16 kg', [ 'sub' => 'kettlebell', 'vaegt' => '16' ] ], [ 'Kettlebell 24 kg', [ 'sub' => 'kettlebell', 'vaegt' => '24' ] ] ] ],
	[ 'slug' => 'motionscykler', 'title' => 'Motionscykler', 'img' => 'hero-motionscykler.jpg', 'subs' => [ [ 'Spinningcykel', [ 'sub' => 'spinningcykel' ] ], [ 'Kondicykel', [ 'sub' => 'kondicykel' ] ], [ 'Airbike', [ 'sub' => 'airbike' ] ], [ 'Liggecykel', [ 'sub' => 'liggecykel' ] ] ] ],
];
$deals     = playform_front_products( 'deals' );
$has_deals = count( $deals ) >= 4;
?>

<section class="wrap hero-wrap">
	<div class="hero">
		<?php foreach ( $hero as $i => $h ) : ?>
			<?php if ( $i === 1 ) : ?><div class="hero__side"><?php endif; ?>
			<div class="cat<?php echo ! empty( $h['big'] ) ? ' cat--big' : ''; ?>">
				<img class="cat__img" src="<?php echo esc_url( playform_asset( $h['img'] ) ); ?>" alt="" <?php echo $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
				<span class="cat__shade"></span>
				<a class="cat__main" href="<?php echo esc_url( playform_category_url( $h['slug'], $h['args'] ?? [] ) ); ?>" aria-label="<?php echo esc_attr( $h['title'] ); ?>"></a>
				<div class="cat__body">
					<span class="cat__title"><?php echo esc_html( $h['title'] ); ?></span>
					<nav class="cat__subs" aria-label="<?php echo esc_attr( 'Underkategorier til ' . $h['title'] ); ?>">
						<?php foreach ( $h['subs'] as [ $label, $args ] ) : ?>
							<a href="<?php echo esc_url( playform_category_url( $h['slug'], $args ) ); ?>"><?php echo esc_html( $label ); ?></a>
						<?php endforeach; ?>
					</nav>
				</div>
				<span class="cat__arrow" aria-hidden="true"><?php echo playform_icon( 'arrow-right', 20, 1.4 ); // phpcs:ignore ?></span>
			</div>
			<?php if ( $i === 2 ) : ?></div><?php endif; ?>
		<?php endforeach; ?>
	</div>

	<div class="usp">
		<div class="usp__item"><span class="usp__icon"><?php echo playform_icon( 'grid', 22, 1.4 ); // phpcs:ignore ?></span><span><b>Alt udstyr ét sted</b><span>Produkter fra flere danske shops</span></span></div>
		<div class="usp__item"><span class="usp__icon"><?php echo playform_icon( 'refresh', 22, 1.4 ); // phpcs:ignore ?></span><span><b>Opdateret 2× dagligt</b><span>Aktuelle priser og lagerstatus</span></span></div>
		<div class="usp__item"><span class="usp__icon"><?php echo playform_icon( 'store', 22, 1.4 ); // phpcs:ignore ?></span><span><b>Køb direkte i butikken</b><span>Du handler hos shoppen som normalt</span></span></div>
	</div>
</section>

<?php
get_template_part( 'template-parts/slider', null, [ 'title' => 'Populære produkter', 'products' => playform_front_products( 'popular' ), 'id' => 'slider-pop' ] );
get_template_part(
	'template-parts/slider',
	null,
	$has_deals
		? [ 'title' => 'Skarpe tilbud lige nu', 'products' => $deals, 'id' => 'slider-deals' ]
		: [ 'title' => 'Nyt i butikkerne', 'products' => playform_front_products( 'new' ), 'id' => 'slider-new' ]
);
?>

<section class="wrap section">
	<div class="news">
		<div>
			<h2>Få besked, når prisen falder</h2>
			<p>Ugens bedste tilbud på fitnessudstyr og nye guides direkte i din indbakke. Ca. én mail om ugen.</p>
		</div>
		<form class="news__form" method="post" action="#" data-newsletter>
			<label for="nl-email">Din e-mail</label>
			<div class="news__row">
				<input id="nl-email" type="email" name="email" placeholder="navn@eksempel.dk" required>
				<button type="submit">Tilmeld</button>
			</div>
			<span class="news__note">Du kan altid afmelde dig igen.</span>
		</form>
	</div>
</section>

<?php
$posts = get_posts( [ 'post_type' => 'post', 'posts_per_page' => 8 ] );
$fallback = [
	[ 'Bedste løbebånd til hjemmet: Sådan vælger du det rigtige', 'Købsguide', 'guide-lobebaand.jpg' ],
	[ 'Gåbånd til kontoret: Er det pengene værd?', 'Hjemmekontor', 'guide-gaabaand.jpg' ],
	[ 'Kettlebell for begyndere: 8 øvelser du skal kunne', 'Træning', 't-kettlebell.jpg' ],
	[ 'Crosstrainer eller motionscykel – hvad skal du vælge?', 'Købsguide', 't-cykel.jpg' ],
	[ 'Sådan indretter du et hjemmegym på 4 m²', 'Hjemmegym', 't-hjemmegym.jpg' ],
	[ 'Hvor tunge håndvægte skal du købe?', 'Købsguide', 't-haandvaegte.jpg' ],
	[ 'Træningsbænk: 5 ting du skal kigge efter', 'Købsguide', 't-baenk.jpg' ],
	[ 'Romaskine: Teknik og de typiske fejl', 'Træning', 't-romaskine.jpg' ],
];
$items = [];
if ( $posts ) {
	foreach ( $posts as $post_item ) {
		$cats    = get_the_category( $post_item->ID );
		$items[] = [
			'title' => get_the_title( $post_item ),
			'tag'   => $cats ? $cats[0]->name : 'Guide',
			'img'   => get_the_post_thumbnail_url( $post_item, 'large' ) ?: playform_asset( 'guide-lobebaand.jpg' ),
			'url'   => get_permalink( $post_item ),
			'date'  => get_the_date( 'j. M Y', $post_item ),
		];
	}
} else {
	foreach ( $fallback as [ $t, $tag, $img ] ) {
		$items[] = [ 'title' => $t, 'tag' => $tag, 'img' => playform_asset( $img ), 'url' => home_url( '/viden/' ), 'date' => '' ];
	}
}
?>
<section class="wrap section">
	<div class="section__head">
		<h2>Guides og artikler</h2>
		<span class="section__line"></span>
		<a class="section__more" href="<?php echo esc_url( home_url( '/viden/' ) ); ?>">Se alle</a>
	</div>
	<div class="guides-top">
		<?php $g = $items[0]; ?>
		<a href="<?php echo esc_url( $g['url'] ); ?>" class="guide-big">
			<img src="<?php echo esc_url( $g['img'] ); ?>" alt="" loading="lazy">
			<span class="guide-big__shade"></span>
			<span class="guide-big__body">
				<span class="tag-accent"><?php echo esc_html( $g['tag'] ); ?></span>
				<span class="guide-big__title"><?php echo esc_html( $g['title'] ); ?></span>
				<?php if ( $g['date'] ) : ?><span class="guide-big__meta"><?php echo esc_html( $g['date'] ); ?></span><?php endif; ?>
			</span>
		</a>
		<?php if ( isset( $items[1] ) ) : $g = $items[1]; ?>
			<a href="<?php echo esc_url( $g['url'] ); ?>" class="guide-side">
				<img src="<?php echo esc_url( $g['img'] ); ?>" alt="" loading="lazy">
				<span class="guide-side__tag"><?php echo esc_html( $g['tag'] ); ?></span>
				<span class="guide-side__title"><?php echo esc_html( $g['title'] ); ?></span>
				<?php if ( $g['date'] ) : ?><span class="guide-side__meta"><?php echo esc_html( $g['date'] ); ?></span><?php endif; ?>
			</a>
		<?php endif; ?>
	</div>
	<div class="guides-list">
		<?php foreach ( array_slice( $items, 2, 6 ) as $g ) : ?>
			<a href="<?php echo esc_url( $g['url'] ); ?>" class="guide-row">
				<img src="<?php echo esc_url( $g['img'] ); ?>" alt="" loading="lazy">
				<span><b><?php echo esc_html( $g['title'] ); ?></b><span><?php echo esc_html( trim( $g['tag'] . ( $g['date'] ? ' · ' . $g['date'] : '' ) ) ); ?></span></span>
			</a>
		<?php endforeach; ?>
	</div>
</section>

<?php
$front   = get_post( (int) get_option( 'page_on_front' ) );
$seo     = $front && trim( $front->post_content ) ? apply_filters( 'the_content', $front->post_content ) : '<h2>Fitnessudstyr til hjemmet – samlet ét sted</h2><p>Playform samler fitnessudstyr fra flere danske webshops ét sted. Vi henter priser og lagerstatus to gange i døgnet, så du hurtigt kan finde løbebåndet, kettlebellen eller motionscyklen, der passer til dig. Når du har fundet det rigtige produkt, sender vi dig videre til shoppen, hvor du gennemfører købet som normalt.</p><h3>Hvilket fitnessudstyr skal du vælge?</h3><p>Det afhænger af, hvad du vil træne, og hvor meget plads du har. Vil du forbedre din kondition, er et løbebånd, en crosstrainer eller en spinningcykel det oplagte valg. Har du begrænset plads, kan et foldbart løbebånd eller et gåbånd til hjemmekontoret være en god løsning. Til styrketræning kommer du langt med et par justerbare håndvægte, en kettlebell og en træningsbænk.</p><h3>Hvad skal du være opmærksom på?</h3><p>Kig efter maksimal brugervægt, garanti og støjniveau, især hvis du bor i lejlighed. På løbebånd er motorkraft og løbefladens størrelse vigtige, mens svinghjulets vægt betyder meget for, hvor jævn en spinningcykel føles.</p>';
get_template_part( 'template-parts/seo-text', null, [ 'html' => $seo, 'right' => true ] );
get_footer();

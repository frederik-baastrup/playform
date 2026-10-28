<?php
/**
 * Kategoriside, "Alle produkter" og produktsøgning.
 */
defined( 'ABSPATH' ) || exit;
get_header();

$state = playform_catalog_state();
$term  = get_queried_object();
$is_cat = $term instanceof WP_Term && $term->taxonomy === 'product_cat';
$search = get_search_query();

$parent = $is_cat && $term->parent ? get_term( $term->parent, 'product_cat' ) : null;
if ( $is_cat ) {
	$title = $term->name;
	$intro = $term->description ?: sprintf(
		'Se %s fra flere danske webshops samlet ét sted. Priser og lagerstatus opdateres to gange i døgnet, og du handler direkte hos butikken.',
		mb_strtolower( $term->name )
	);
} elseif ( $search !== '' ) {
	$title = sprintf( 'Søgeresultater for "%s"', $search );
	$intro = '';
} else {
	$title = ( playform_active_filters()['egenskab'] ?? [] ) === [ 'tilbud' ] ? 'Tilbud' : 'Alle produkter';
	$intro = $title === 'Tilbud' ? 'Produkter, hvor vi selv har målt et prisfald i forhold til den laveste pris de foregående 30 dage.' : '';
}

$top   = $parent ?: ( $is_cat ? $term : null );
$crumb = [ [ 'Forside', home_url( '/' ) ] ];
if ( $parent ) {
	$crumb[] = [ $parent->name, get_term_link( $parent ) ];
}
$crumb[] = [ $title, '' ];
$tabs    = $top ? playform_sub_links( $top->slug ) : [];
$sort    = isset( $_GET['sortering'] ) ? sanitize_key( $_GET['sortering'] ) : 'populaer';
$updated = playform_last_update_label();
global $wp_query;
?>
<section class="wrap cat-top">
	<?php playform_breadcrumbs( $crumb ); ?>
	<div class="cat-top__head">
		<h1><?php echo esc_html( $title ); ?></h1>
		<?php if ( $intro ) : ?><p><?php echo esc_html( wp_strip_all_tags( $intro ) ); ?></p><?php endif; ?>
	</div>
	<?php if ( $tabs ) : ?>
		<nav class="tabs" aria-label="Underkategorier">
			<?php $on = ! $parent; ?>
			<a href="<?php echo esc_url( get_term_link( $top ) ); ?>" class="tab<?php echo $on ? ' is-active' : ''; ?>"<?php echo $on ? ' aria-current="page"' : ''; ?>>Alle <?php echo esc_html( mb_strtolower( $top->name ) ); ?></a>
			<?php foreach ( $tabs as [ $label, $args ] ) : ?>
				<?php $on = $parent && $term->slug === $args['sub']; ?>
				<a href="<?php echo esc_url( playform_category_url( $top->slug, $args ) ); ?>" class="tab<?php echo $on ? ' is-active' : ''; ?>"<?php echo $on ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>
</section>

<section class="wrap catalog">
	<div class="layout">
		<aside class="filters" aria-label="Filtre">
			<details class="filters__mobile" open>
				<summary class="filters__head">
					<span>Filtrer</span>
					<?php if ( $state['tags'] ) : ?><a href="<?php echo esc_url( playform_reset_url() ); ?>">Nulstil</a><?php endif; ?>
				</summary>
				<?php foreach ( $state['facets'] as $g => $facet ) : ?>
					<details class="fsec"<?php echo in_array( $g, [ 'brugervaegt' ], true ) && empty( playform_active_filters()[ $g ] ) ? '' : ' open'; ?>>
						<summary><?php echo esc_html( $facet['label'] ); ?><?php echo playform_icon( 'chevron', 16 ); // phpcs:ignore ?></summary>
						<div class="fsec__opts">
							<?php foreach ( $facet['options'] as $o ) : ?>
								<a class="opt<?php echo $o['checked'] ? ' is-checked' : ''; ?>" href="<?php echo esc_url( playform_filter_toggle_url( $g, $o['key'] ) ); ?>" rel="nofollow" role="checkbox" aria-checked="<?php echo $o['checked'] ? 'true' : 'false'; ?>">
									<span class="opt__box"><?php echo $o['checked'] ? playform_icon( 'check', 14, 2.2 ) : ''; // phpcs:ignore ?></span>
									<?php echo esc_html( $o['label'] ); ?>
									<span class="opt__n"><?php echo (int) $o['count']; ?></span>
								</a>
							<?php endforeach; ?>
						</div>
					</details>
				<?php endforeach; ?>
			</details>
		</aside>

		<div class="results">
			<div class="results__bar">
				<span><b><?php echo (int) $state['total']; ?></b> produkter<?php echo $updated ? ' <span class="muted">· Priser opdateret ' . esc_html( $updated ) . '</span>' : ''; ?></span>
				<form method="get" class="sort">
					<?php foreach ( $_GET as $k => $v ) : // phpcs:ignore ?>
						<?php if ( $k !== 'sortering' && $k !== 'paged' && ! is_array( $v ) ) : ?><input type="hidden" name="<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( wp_unslash( $v ) ); ?>"><?php endif; ?>
					<?php endforeach; ?>
					<label for="sortering">Sortér efter</label>
					<span class="select">
						<select id="sortering" name="sortering" data-autosubmit>
							<?php foreach ( [ 'populaer' => 'Mest populære', 'pris-lav' => 'Laveste pris', 'pris-hoj' => 'Højeste pris', 'nyeste' => 'Nyeste' ] as $k => $label ) : ?>
								<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $sort, $k ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo playform_icon( 'chevron', 14, 1.8 ); // phpcs:ignore ?>
					</span>
					<noscript><button type="submit">Sortér</button></noscript>
				</form>
			</div>

			<?php if ( $state['tags'] ) : ?>
				<div class="tags">
					<?php foreach ( $state['tags'] as $t ) : ?>
						<a class="tag" href="<?php echo esc_url( $t['url'] ); ?>" rel="nofollow" aria-label="<?php echo esc_attr( 'Fjern filter ' . $t['label'] ); ?>"><?php echo esc_html( $t['label'] ); ?><?php echo playform_icon( 'close', 12, 2 ); // phpcs:ignore ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( have_posts() ) : ?>
				<div class="grid">
					<?php
					while ( have_posts() ) {
						the_post();
						$p = wc_get_product( get_the_ID() );
						if ( $p ) {
							playform_product_card( $p );
						}
					}
					?>
				</div>
				<?php
				$pages = (int) $wp_query->max_num_pages;
				$cur   = max( 1, (int) get_query_var( 'paged' ) );
				if ( $pages > 1 ) :
					?>
					<nav class="pager" aria-label="Sider">
						<div class="pager__row">
							<?php if ( $cur > 1 ) : ?><a class="pg" href="<?php echo esc_url( get_pagenum_link( $cur - 1 ) ); ?>" aria-label="Forrige side"><?php echo playform_icon( 'arrow-left', 16 ); // phpcs:ignore ?></a><?php endif; ?>
							<?php
							$window = array_unique( array_filter( [ 1, $cur - 1, $cur, $cur + 1, $pages ], static fn( $n ) => $n >= 1 && $n <= $pages ) );
							sort( $window );
							$prev = 0;
							foreach ( $window as $n ) {
								if ( $prev && $n > $prev + 1 ) {
									echo '<span class="pg pg--gap">…</span>';
								}
								printf( '<a class="pg%s" href="%s"%s>%d</a>', $n === $cur ? ' is-on' : '', esc_url( get_pagenum_link( $n ) ), $n === $cur ? ' aria-current="page"' : '', $n ); // phpcs:ignore
								$prev = $n;
							}
							?>
							<?php if ( $cur < $pages ) : ?><a class="pg" href="<?php echo esc_url( get_pagenum_link( $cur + 1 ) ); ?>" aria-label="Næste side"><?php echo playform_icon( 'arrow-right', 16 ); // phpcs:ignore ?></a><?php endif; ?>
						</div>
						<span class="muted">Side <?php echo (int) $cur; ?> af <?php echo (int) $pages; ?> · <?php echo (int) PLAYFORM_PER_PAGE; ?> produkter pr. side</span>
					</nav>
				<?php endif; ?>
			<?php else : ?>
				<div class="empty">
					<?php if ( in_array( 'tilbud', playform_active_filters()['egenskab'] ?? [], true ) ) : ?>
						<b>Ingen målte prisfald lige nu</b>
						<span>Vi viser kun tilbud, som vi selv har målt i vores prishistorik. Det kræver mindst 30 dages data, så de første tilbud dukker op efter ca. en måned.</span>
					<?php else : ?>
						<b>Ingen produkter matcher dine filtre</b>
						<span>Prøv at fjerne et eller flere filtre.</span>
					<?php endif; ?>
					<?php if ( $is_cat ) : ?><a class="btn" href="<?php echo esc_url( get_term_link( $term ) ); ?>">Nulstil filtre</a><?php endif; ?>
				</div>
			<?php endif; ?>

			<?php
			if ( $is_cat ) {
				$faq = get_term_meta( $term->term_id, 'pf_faq', true );
				if ( is_array( $faq ) && $faq && max( 1, (int) get_query_var( 'paged' ) ) === 1 ) {
					echo '<div class="faq-wrap"><h2>Ofte stillede spørgsmål om ' . esc_html( mb_strtolower( $term->name ) ) . '</h2>';
					foreach ( $faq as [ $q, $a ] ) {
						echo '<details class="faq"><summary>' . esc_html( $q ) . playform_icon( 'plus', 18 ) . '</summary><p>' . esc_html( $a ) . '</p></details>'; // phpcs:ignore
					}
					echo '</div>';
					playform_faq_schema( $faq );
				}
				$seo = get_term_meta( $term->term_id, 'pf_seo_text', true );
				if ( $seo && max( 1, (int) get_query_var( 'paged' ) ) === 1 ) {
					get_template_part( 'template-parts/seo-text', null, [ 'html' => $seo ] );
				}
			}
			?>
		</div>
	</div>
</section>
<?php
get_footer();

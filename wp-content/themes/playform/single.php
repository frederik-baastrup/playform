<?php
/**
 * Guide / artikel.
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$html          = apply_filters( 'the_content', get_the_content() );
	[ $html, $toc ] = playform_guide_prepare( $html );
	$cats          = get_the_category();
	$tag           = $cats ? $cats[0]->name : 'Guide';
	$blog          = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/viden/' );
	$updated       = get_the_modified_date( 'j. F Y' );
	$author        = playform_author( (int) get_the_author_meta( 'ID' ) );
	?>
	<article class="guide">
		<?php
		$hero    = playform_post_image( get_the_ID(), 1200 );
		$checked = (string) get_post_meta( get_the_ID(), '_pf_checked', true );
		$is_best = (int) ( playform_root_posts()[ get_post_field( 'post_name' ) ] ?? 0 ) === get_the_ID();
		?>
		<header class="wrap gh<?php echo $hero ? ' has-hero' : ''; ?>">
			<div class="gh__text">
				<?php playform_breadcrumbs( [ [ 'Forside', home_url( '/' ) ], $is_best ? [ 'Købsguides', playform_guide_url() ] : [ 'Viden & guides', $blog ], [ get_the_title(), '' ] ] ); ?>
				<?php if ( $cats ) : ?>
					<a class="gh__topic" href="<?php echo esc_url( get_category_link( $cats[0] ) ); ?>"><i aria-hidden="true"></i><?php echo esc_html( $tag ); ?></a>
				<?php endif; ?>
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?><p class="gh__lead"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
				<div class="gh__meta">
					<a class="gh__by" href="<?php echo esc_url( $author['url'] ); ?>" rel="author">
						<?php echo playform_author_avatar( $author, 32 ); // phpcs:ignore ?>
						<span>Af <b><?php echo esc_html( $author['name'] ); ?></b></span>
					</a>
					<span class="gh__facts">
						<span>Opdateret <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date( 'j. M Y' ) ); ?></time></span>
						<span><?php echo (int) playform_read_minutes( $html ); ?> min. læsning</span>
						<?php if ( $checked && playform_picks() ) : ?>
							<span class="gh__ok"><?php echo playform_icon( 'check', 13, 2 ); // phpcs:ignore ?>Produkter tjekket <?php echo esc_html( wp_date( 'j. M', strtotime( $checked ) ) ); ?></span>
						<?php endif; ?>
					</span>
				</div>
			</div>
			<?php if ( $hero ) : ?>
				<figure class="gh__img"><img src="<?php echo esc_url( $hero ); ?>" alt="" fetchpriority="high"></figure>
			<?php endif; ?>
		</header>
		<div class="wrap guide__layout">
			<aside class="guide__toc">
				<?php if ( count( $toc ) > 1 ) : ?>
					<nav class="toc" aria-label="Indhold" data-toc>
						<div class="toc__head"><span class="toc__title">Indhold</span><span class="toc__count"><?php echo count( $toc ); ?> afsnit</span></div>
						<ol>
							<?php foreach ( $toc as $i => [ $id, $text ] ) : ?>
								<li><a href="#<?php echo esc_attr( $id ); ?>"><span class="toc__n"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><span class="toc__t"><?php echo esc_html( $text ); ?></span></a></li>
							<?php endforeach; ?>
						</ol>
					</nav>
				<?php endif; ?>
			</aside>
			<div class="guide__body prose">
				<?php if ( ! playform_picks() ) : // anbefalinger har deres egen boks om metode og provision ?>
				<p class="guide__note">Guiden indeholder links til butikker. Køber du via et link, kan vi få en provision – det koster ikke dig noget ekstra og påvirker ikke vores råd.</p>
				<?php endif; ?>
				<?php echo $html; // phpcs:ignore -- indholdet er allerede filtreret af the_content ?>
				<?php
				$n_guides = (int) count_user_posts( $author['id'], 'post', true );
				$n_prods  = (int) wp_count_posts( 'product' )->publish;
				$n_shops  = count( playform_shop_names() );
				?>
				<aside class="author-card" aria-label="Om forfatteren">
					<div class="author-card__head">
						<?php echo playform_author_avatar( $author, 64 ); // phpcs:ignore ?>
						<div class="author-card__who">
							<span class="author-card__label">Skrevet af</span>
							<a class="author-card__name" href="<?php echo esc_url( $author['url'] ); ?>" rel="author"><?php echo esc_html( $author['name'] ); ?></a>
							<span class="author-card__role"><?php echo esc_html( $author['role'] ); ?> hos Fitbase</span>
						</div>
						<a class="author-card__profile" href="<?php echo esc_url( $author['url'] ); ?>">Se profil <?php echo playform_icon( 'arrow-right', 14, 1.6 ); // phpcs:ignore ?></a>
					</div>
					<p class="author-card__bio"><?php echo esc_html( $author['bio'] ); ?></p>
					<ul class="author-card__stats">
						<li><b><?php echo esc_html( number_format( $n_guides, 0, ',', '.' ) ); ?></b><span>guides skrevet</span></li>
						<li><b><?php echo esc_html( number_format( $n_prods, 0, ',', '.' ) ); ?></b><span>produkter vi følger</span></li>
						<li><b><?php echo esc_html( (string) $n_shops ); ?></b><span>danske butikker</span></li>
					</ul>
					<ul class="author-card__rules">
						<li><?php echo playform_icon( 'check', 14, 1.8 ); // phpcs:ignore ?>Ingen betalte placeringer</li>
						<li><?php echo playform_icon( 'check', 14, 1.8 ); // phpcs:ignore ?>Fakta fra butikkernes egne data</li>
						<li><?php echo playform_icon( 'check', 14, 1.8 ); // phpcs:ignore ?>Priser opdateres to gange i døgnet</li>
					</ul>
				</aside>
			</div>
		</div>
	</article>

	<?php
	$more = get_posts( [ 'post_type' => 'post', 'posts_per_page' => 3, 'post__not_in' => [ get_the_ID() ] ] );
	if ( $more ) :
		?>
		<section class="wrap section guide-more">
			<div class="section__head"><h2>Flere guides</h2><span class="section__line"></span><a class="section__more" href="<?php echo esc_url( $blog ); ?>">Se alle</a></div>
			<div class="guides-list">
				<?php foreach ( $more as $m ) : ?>
					<a href="<?php echo esc_url( get_permalink( $m ) ); ?>" class="guide-row">
						<img src="<?php echo esc_url( get_the_post_thumbnail_url( $m, 'thumbnail' ) ?: playform_asset( 't-hjemmegym.jpg' ) ); ?>" alt="" loading="lazy">
						<span><b><?php echo esc_html( get_the_title( $m ) ); ?></b><span><?php echo esc_html( get_the_date( 'j. M Y', $m ) ); ?></span></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;
get_footer();

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
		<header class="wrap guide__head">
			<?php playform_breadcrumbs( [ [ 'Forside', home_url( '/' ) ], [ 'Viden & guides', $blog ], [ get_the_title(), '' ] ] ); ?>
			<span class="kicker"><i aria-hidden="true"></i><?php echo esc_html( $tag ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?><p class="guide__lead"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
			<div class="guide__meta">
				<a class="guide__author" href="<?php echo esc_url( $author['url'] ); ?>" rel="author">
					<?php echo playform_author_avatar( $author, 40 ); // phpcs:ignore ?>
					<span><b><?php echo esc_html( $author['name'] ); ?></b><small><?php echo esc_html( $author['role'] ); ?></small></span>
				</a>
				<span class="guide__facts">
					<span><?php echo playform_icon( 'calendar', 15, 1.4 ); // phpcs:ignore ?>Opdateret <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( $updated ); ?></time></span>
					<span><?php echo playform_icon( 'clock', 15, 1.4 ); // phpcs:ignore ?><?php echo (int) playform_read_minutes( $html ); ?> min. læsning</span>
				</span>
			</div>
		</header>
		<?php $hero = playform_post_image( get_the_ID(), 1200 ); ?>
		<?php if ( $hero ) : ?>
			<div class="wrap"><div class="guide__hero"><img src="<?php echo esc_url( $hero ); ?>" alt="" fetchpriority="high"></div></div>
		<?php endif; ?>
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
				<aside class="author-box" aria-label="Om forfatteren">
					<?php echo playform_author_avatar( $author, 64 ); // phpcs:ignore ?>
					<div>
						<span class="author-box__label">Skrevet af</span>
						<a class="author-box__name" href="<?php echo esc_url( $author['url'] ); ?>" rel="author"><?php echo esc_html( $author['name'] ); ?></a>
						<span class="author-box__role"><?php echo esc_html( $author['role'] ); ?></span>
						<p><?php echo esc_html( $author['bio'] ); ?></p>
						<a class="author-box__more" href="<?php echo esc_url( $author['url'] ); ?>">Om <?php echo esc_html( strtok( $author['name'], ' ' ) ); ?> og vores guides &rarr;</a>
					</div>
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

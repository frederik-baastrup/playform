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
	?>
	<article class="guide">
		<header class="wrap guide__head">
			<?php playform_breadcrumbs( [ [ 'Forside', home_url( '/' ) ], [ 'Viden & guides', $blog ], [ get_the_title(), '' ] ] ); ?>
			<span class="tag-accent"><?php echo esc_html( $tag ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?><p class="guide__lead"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
			<span class="guide__meta">Af <?php the_author(); ?> · Opdateret <?php echo esc_html( $updated ); ?> · <?php echo (int) playform_read_minutes( $html ); ?> min. læsning</span>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="wrap"><div class="guide__hero"><?php the_post_thumbnail( 'large' ); ?></div></div>
		<?php endif; ?>
		<div class="wrap guide__layout">
			<aside class="guide__toc">
				<?php if ( count( $toc ) > 1 ) : ?>
					<nav aria-label="Indhold">
						<span class="guide__toc-title">Indhold</span>
						<?php foreach ( $toc as [ $id, $text ] ) : ?>
							<a href="#<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $text ); ?></a>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>
			</aside>
			<div class="guide__body prose">
				<p class="guide__note">Guiden indeholder links til butikker. Køber du via et link, kan vi få en provision – det koster ikke dig noget ekstra og påvirker ikke vores råd.</p>
				<?php echo $html; // phpcs:ignore -- indholdet er allerede filtreret af the_content ?>
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

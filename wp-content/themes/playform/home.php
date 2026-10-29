<?php
/**
 * Oversigt over guides (/viden/).
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="wrap cat-top">
	<?php playform_breadcrumbs( [ [ 'Forside', home_url( '/' ) ], [ 'Viden & guides', '' ] ] ); ?>
	<div class="cat-top__head">
		<h1>Viden &amp; guides</h1>
		<p>Købsguides, træningstips og svar på de spørgsmål, der dukker op, når du skal indrette dit hjemmegym.</p>
	</div>
</section>
<section class="wrap section" style="padding-top:40px">
	<?php if ( have_posts() ) : ?>
		<div class="guide-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				$cats = get_the_category();
				?>
				<a href="<?php the_permalink(); ?>" class="guide-card">
					<span class="guide-card__img"><img src="<?php echo esc_url( playform_post_image( get_the_ID(), 700 ) ?: playform_asset( 'guide-lobebaand.jpg' ) ); ?>" alt="" loading="lazy"></span>
					<span class="guide-side__tag"><?php echo esc_html( $cats ? $cats[0]->name : 'Guide' ); ?></span>
					<span class="guide-side__title"><?php the_title(); ?></span>
					<span class="guide-side__meta"><?php echo esc_html( get_the_date( 'j. F Y' ) ); ?></span>
				</a>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( [ 'prev_text' => '‹', 'next_text' => '›' ] ); ?>
	<?php else : ?>
		<p class="muted">Der er ingen guides endnu.</p>
	<?php endif; ?>
</section>
<?php
get_footer();

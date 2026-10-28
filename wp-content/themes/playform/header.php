<?php defined( 'ABSPATH' ) || exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#indhold">Gå til indhold</a>

<div class="topbar">
	<div class="wrap topbar__in">
		<span>Fitnessudstyr fra flere danske shops – samlet ét sted</span>
		<span class="topbar__extra">Priser opdateres 2 gange dagligt</span>
		<a href="<?php echo esc_url( home_url( '/saadan-tjener-vi-penge/' ) ); ?>">Annoncering: Siden indeholder affiliate-links</a>
	</div>
</div>

<header class="site-header">
	<div class="wrap">
		<div class="site-header__row">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo" aria-label="Playform forside">
				<img src="<?php echo esc_url( playform_asset( 'logo.svg' ) ); ?>" alt="Playform" width="174" height="30">
			</a>
			<form role="search" method="get" class="search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php echo playform_icon( 'search', 18, 2 ); // phpcs:ignore ?>
				<label for="site-search" class="sr-only">Søg efter produkter</label>
				<input id="site-search" type="search" name="s" placeholder="Hvad leder du efter?" value="<?php echo esc_attr( get_search_query() ); ?>">
				<input type="hidden" name="post_type" value="product">
				<button type="submit">Find produkter</button>
			</form>
			<nav class="knowledge" aria-label="Viden">
				<a href="<?php echo esc_url( home_url( '/viden/' ) ); ?>">Viden</a>
				<a href="<?php echo esc_url( home_url( '/viden/' ) ); ?>">Guides</a>
				<a href="<?php echo esc_url( home_url( '/viden/' ) ); ?>">Nyheder</a>
			</nav>
			<button type="button" class="menu-toggle" aria-expanded="false" aria-controls="hovedmenu" aria-label="Åbn menu"><?php echo playform_icon( 'menu', 22 ); // phpcs:ignore ?></button>
		</div>
		<nav id="hovedmenu" class="mm" aria-label="Kategorier">
			<?php
			$current = is_tax( 'product_cat' ) ? playform_current_top_slug() : '';
			if ( is_singular( 'product' ) ) {
				$current = (string) get_post_meta( get_the_ID(), '_pf_category', true );
			}
			foreach ( playform_menu() as $item ) :
				$url = playform_category_url( $item['slug'] );
				?>
				<div class="mm__item">
					<a href="<?php echo esc_url( $url ); ?>" class="mm__link<?php echo $current === $item['slug'] ? ' is-active' : ''; ?>"><?php echo esc_html( $item['label'] ); ?></a>
					<div class="mm__panel">
						<div class="mm__inner">
							<?php foreach ( $item['cols'] as $col ) : ?>
								<div class="mm__col">
									<span class="mm__title"><?php echo esc_html( $col['title'] ); ?></span>
									<?php foreach ( $col['links'] as [ $label, $args ] ) : ?>
										<a href="<?php echo esc_url( playform_category_url( $item['slug'], $args ) ); ?>"><?php echo esc_html( $label ); ?></a>
									<?php endforeach; ?>
								</div>
							<?php endforeach; ?>
							<a href="<?php echo esc_url( $url ); ?>" class="mm__promo">
								<img src="<?php echo esc_url( playform_asset( $item['img'] ) ); ?>" alt="" loading="lazy">
								<span>Se alle <?php echo esc_html( mb_strtolower( $item['label'] ) ); ?> →</span>
							</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
			<a href="<?php echo esc_url( add_query_arg( 'egenskab', 'tilbud', get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>" class="mm__link mm__link--sale">Tilbud</a>
		</nav>
	</div>
</header>
<?php if ( isset( $_GET['nyhedsbrev'] ) && $_GET['nyhedsbrev'] === 'bekraeftet' ) : // phpcs:ignore ?>
	<div class="notice-bar" role="status"><div class="wrap">Tak – din tilmelding til nyhedsbrevet er bekræftet.</div></div>
<?php endif; ?>
<main id="indhold">

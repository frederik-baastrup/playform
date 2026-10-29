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
	<a class="topbar__mob" href="<?php echo esc_url( home_url( '/saadan-tjener-vi-penge/' ) ); ?>"><u>Indeholder annoncer</u> · Priser opdateres 2× dagligt</a>
</div>

<header class="site-header">
	<div class="wrap">
		<div class="site-header__row">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo" aria-label="Fitbase forside">
				<img src="<?php echo esc_url( playform_asset( 'logo.svg' ) ); ?>" alt="Fitbase" width="146" height="26">
			</a>
			<nav id="hovedmenu" class="mm" aria-label="Kategorier">
				<button type="button" class="mm__back" hidden><?php echo playform_icon( 'chevron', 16 ); // phpcs:ignore ?><span>Tilbage</span></button>
				<?php
				$current = is_tax( 'product_cat' ) ? playform_current_top_slug() : '';
				if ( is_singular( 'product' ) ) {
					$current = playform_root_slug( (string) get_post_meta( get_the_ID(), '_pf_category', true ) );
				}
				foreach ( playform_menu() as $item ) :
					?>
					<div class="mm__item" data-mm>
						<a href="<?php echo esc_url( $item['url'] ); ?>" class="mm__link<?php echo $current === $item['slug'] ? ' is-active' : ''; ?>" aria-haspopup="true"><?php echo esc_html( $item['label'] ); ?></a>
						<div class="mm__panel">
							<div class="mm__inner">
								<div class="mm__cats">
									<span class="mm__title">Kategorier</span>
									<a class="mm__all" href="<?php echo esc_url( $item['url'] ); ?>">Se alle <?php echo esc_html( mb_strtolower( $item['label'] ) ); ?></a>
									<?php foreach ( $item['primary'] as $i => $p ) : ?>
										<a href="<?php echo esc_url( $p['url'] ); ?>" class="mm__cat<?php echo $i === 0 ? ' is-active' : ''; ?>" data-pane="<?php echo (int) $i; ?>"><?php echo esc_html( $p['label'] ); ?><?php echo playform_icon( 'chevron', 14 ); // phpcs:ignore ?></a>
									<?php endforeach; ?>
								</div>
								<?php foreach ( $item['primary'] as $i => $p ) : ?>
									<div class="mm__pane<?php echo $i === 0 ? ' is-active' : ''; ?>" data-pane="<?php echo (int) $i; ?>">
										<?php if ( $p['subs'] ) : ?>
											<div class="mm__col">
												<span class="mm__title"><?php echo esc_html( $p['label'] ); ?></span>
												<?php foreach ( $p['subs'] as [ $label, $url ] ) : ?>
													<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
												<?php endforeach; ?>
												<a href="<?php echo esc_url( $p['url'] ); ?>" class="mm__more">Se alle <?php echo esc_html( mb_strtolower( $p['label'] ) ); ?> &rarr;</a>
											</div>
											<?php if ( $p['prices'] ) : ?>
												<div class="mm__col">
													<span class="mm__title">Pris</span>
													<?php foreach ( $p['prices'] as [ $label, $url ] ) : ?>
														<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
													<?php endforeach; ?>
												</div>
											<?php endif; ?>
										<?php else : ?>
											<?php // Ingen underkategorier: pris står i midten, så panelet ikke ser tomt ud. ?>
											<div class="mm__col">
												<span class="mm__title"><?php echo esc_html( $p['label'] ); ?> efter pris</span>
												<?php foreach ( $p['prices'] as [ $label, $url ] ) : ?>
													<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
												<?php endforeach; ?>
												<a href="<?php echo esc_url( $p['url'] ); ?>" class="mm__more">Se hele kategorien &rarr;</a>
											</div>
										<?php endif; ?>
										<a href="<?php echo esc_url( $p['tilbud'] ); ?>" class="mm__promo">
											<img src="<?php echo esc_url( $p['img'] ); ?>" alt="" loading="lazy">
											<span class="mm__promo-body"><span class="mm__promo-title">Tilbud på <?php echo esc_html( mb_strtolower( $p['label'] ) ); ?></span><span class="mm__promo-sub">Butikkernes aktuelle tilbud</span></span>
											<span class="mm__promo-arrow" aria-hidden="true"><?php echo playform_icon( 'arrow-right', 18, 1.4 ); // phpcs:ignore ?></span>
										</a>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
				<div class="mm__item mm__item--plain">
					<a href="<?php echo esc_url( playform_tilbud_url() ); ?>" class="mm__link mm__link--sale<?php echo playform_is_tilbud_page() && ! is_tax() ? ' is-active' : ''; ?>">Tilbud</a>
				</div>
				<div class="mm__knowledge">
					<a href="<?php echo esc_url( home_url( '/viden/' ) ); ?>">Viden</a>
					<a href="<?php echo esc_url( home_url( '/viden/' ) ); ?>">Guides</a>
				</div>
			</nav>
			<form role="search" method="get" class="search" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-search data-endpoint="<?php echo esc_url( rest_url( 'playform/v1/search' ) ); ?>">
				<?php echo playform_icon( 'search', 18, 2 ); // phpcs:ignore ?>
				<label for="site-search" class="sr-only">Søg efter produkter</label>
				<input id="site-search" type="search" name="s" placeholder="Søg" autocomplete="off" value="<?php echo esc_attr( get_search_query() ); ?>">
				<input type="hidden" name="post_type" value="product">
				<button type="submit" class="search__go">Søg</button>
				<div class="search__drop" hidden>
					<div class="search__popular">
						<span class="mm__title">Populære kategorier</span>
						<div class="search__chips">
							<?php foreach ( playform_search_shortcuts() as [ $label, $url ] ) : ?>
								<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="search__results" hidden></div>
				</div>
			</form>
			<nav class="knowledge" aria-label="Viden">
				<a href="<?php echo esc_url( home_url( '/viden/' ) ); ?>">Viden</a>
				<a href="<?php echo esc_url( home_url( '/viden/' ) ); ?>">Guides</a>
				<a href="<?php echo esc_url( playform_tilbud_url() ); ?>" class="knowledge__sale">Tilbud</a>
			</nav>
			<button type="button" class="menu-toggle" aria-expanded="false" aria-controls="mobilmenu" aria-label="Åbn menu"><?php echo playform_icon( 'menu', 24, 1.6 ); // phpcs:ignore ?></button>
		</div>
	</div>
</header>
<?php get_template_part( 'template-parts/mobile-menu' ); ?>
<?php if ( isset( $_GET['nyhedsbrev'] ) && $_GET['nyhedsbrev'] === 'bekraeftet' ) : // phpcs:ignore ?>
	<div class="notice-bar" role="status"><div class="wrap">Tak – din tilmelding til nyhedsbrevet er bekræftet.</div></div>
<?php endif; ?>
<main id="indhold">

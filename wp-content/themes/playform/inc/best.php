<?php
/**
 * Købsguides af typen "Vores bud på de 3 bedste X" (overført fra det gamle fitbase.dk).
 *
 * - Ligger på samme URL som på det gamle site: /romaskine-med-vand/ (ikke /viden/…), så de beholder placeringer og links.
 * - Indholdet er almindelig tekst i indlægget. Kun de levende dele (pris, butik, lager, knap) kommer fra shortcodes,
 *   som læser produktvalgene i metafeltet _pf_picks.
 * - Top 3 bygger på rigtige anmeldelser, hvor de findes (links i 'reviews'); ellers står der tydeligt "vores vurdering".
 * - Ingen påstand om, at vi selv har testet noget.
 *
 * Shortcodes: [fitbase_metode] [fitbase_kort] [fitbase_valg nr="1"] [fitbase_tabel] [fitbase_flere kategori="…"]
 */
defined( 'ABSPATH' ) || exit;

/** Finder produktet bag et valg: EAN først (samme på tværs af importer), ellers URL-navnet. */
function playform_pick_product( array $pick ): ?WC_Product {
	static $cache = [];
	$key = ( $pick['ean'] ?? '' ) . '|' . ( $pick['slug'] ?? '' );
	if ( array_key_exists( $key, $cache ) ) {
		return $cache[ $key ];
	}
	$id = 0;
	if ( ! empty( $pick['ean'] ) ) {
		$ids = get_posts( [ 'post_type' => 'product', 'post_status' => 'publish', 'fields' => 'ids', 'posts_per_page' => 1, 'meta_key' => '_pf_ean', 'meta_value' => (string) $pick['ean'] ] ); // phpcs:ignore
		$id  = $ids ? (int) $ids[0] : 0;
	}
	if ( ! $id && ! empty( $pick['slug'] ) ) {
		$p  = get_page_by_path( (string) $pick['slug'], OBJECT, 'product' );
		$id = $p ? (int) $p->ID : 0;
	}
	$cache[ $key ] = $id ? ( wc_get_product( $id ) ?: null ) : null;
	return $cache[ $key ];
}

function playform_picks( int $post_id = 0 ): array {
	$picks = get_post_meta( $post_id ?: get_the_ID(), '_pf_picks', true );
	return is_array( $picks ) ? $picks : [];
}

/** Korte fakta om produktet ud fra feed-data (intet gættes). */
function playform_pick_facts( int $id ): array {
	$a = (array) get_post_meta( $id, '_pf_attributes', true );
	$f = [];
	if ( ! empty( $a['max_weight'] ) ) {
		$f[] = 'Maks. ' . (int) $a['max_weight'] . ' kg';
	}
	if ( ! empty( $a['max_speed'] ) ) {
		$f[] = 'Op til ' . rtrim( rtrim( number_format( (float) $a['max_speed'], 1, ',', '' ), '0' ), ',' ) . ' km/t';
	}
	if ( ! empty( $a['foldable'] ) ) {
		$f[] = 'Foldbar';
	}
	if ( ! empty( $a['incline'] ) ) {
		$f[] = 'Med incline';
	}
	return $f;
}

/** Billigste butik med lager (det er den, knappen sender videre til). */
function playform_pick_offer( int $id ): ?array {
	if ( ! class_exists( '\Playform\Feeds\Frontend' ) ) {
		return null;
	}
	foreach ( \Playform\Feeds\Frontend::offers( $id ) as $o ) {
		if ( $o['in_stock'] ) {
			return $o;
		}
	}
	return null;
}

// Sådan har vi valgt – står øverst i alle anbefalinger.
add_shortcode(
	'fitbase_metode',
	static function (): string {
		$money = esc_url( home_url( '/saadan-tjener-vi-penge/' ) );
		return '<aside class="method"><span class="method__title">' . playform_icon( 'check', 16, 1.6 ) . 'Sådan har vi valgt</span>'
			. '<p>Vi har ikke selv testet produkterne. Top 3 er vores bud ud fra anmeldelser fra andre medier og brugere, hvor de findes, samt specifikationer, pris og garanti fra butikkernes data. Hvor der ikke findes anmeldelser, står det tydeligt ved produktet.</p>'
			. '<p>Priser og lagerstatus opdateres to gange i døgnet. Vi får provision, hvis du køber via vores links – det påvirker ikke, hvilke produkter vi vælger. <a href="' . $money . '">Sådan tjener vi penge</a>.</p></aside>';
	}
);

// Kort fortalt: de 3 valg med rolle, pris og knap.
add_shortcode(
	'fitbase_kort',
	static function (): string {
		$out = '';
		foreach ( playform_picks() as $i => $pick ) {
			$p = playform_pick_product( $pick );
			if ( ! $p ) {
				continue;
			}
			$d    = playform_card_data( $p );
			$out .= '<a class="tldr__item" href="#valg-' . ( $i + 1 ) . '">'
				. '<span class="tldr__n">' . ( $i + 1 ) . '</span>'
				. ( $d['image'] ? '<img src="' . esc_url( $d['image'] ) . '" alt="" loading="lazy">' : '<span class="tldr__ph"></span>' )
				. '<span class="tldr__txt"><small>' . esc_html( $pick['role'] ?? '' ) . '</small><b>' . esc_html( $d['name'] ) . '</b></span>'
				. '<span class="tldr__price">' . ( $d['in_stock'] ? esc_html( playform_kr( $d['price'] ) ) : 'Ikke på lager' ) . '</span></a>';
		}
		return $out ? '<div class="tldr"><span class="tldr__title">Vores top 3 kort fortalt</span>' . $out . '</div>' : '';
	}
);

// Ét produktvalg: billede, navn, grundlag (anmeldelser eller vores vurdering), fakta, levende pris og knap.
add_shortcode(
	'fitbase_valg',
	static function ( $atts ): string {
		$n     = max( 1, (int) ( $atts['nr'] ?? 1 ) );
		$picks = playform_picks();
		$pick  = $picks[ $n - 1 ] ?? null;
		$p     = $pick ? playform_pick_product( $pick ) : null;
		// Udsolgt eller væk: vis et alternativ i samme kategori og prisklasse, indtil artiklen er opdateret.
		$alt_html = '';
		if ( ! $p || ! get_post_meta( $p->get_id(), '_pf_in_stock', true ) ) {
			$used = array_filter( array_map( static fn( $pk ) => ( $x = playform_pick_product( $pk ) ) ? $x->get_id() : 0, $picks ) );
			$alts = function_exists( 'playform_pick_alternatives' ) ? playform_pick_alternatives( playform_pick_category( $p, get_the_ID() ), $p ? (float) $p->get_price() : (float) ( $pick['price'] ?? 0 ), $used, 1 ) : [];
			if ( $alts ) {
				$a        = playform_card_data( $alts[0] );
				$ao       = playform_pick_offer( $alts[0]->get_id() );
				$alt_html = '<div class="pick-alt"><span class="pick-alt__label">' . ( $p ? 'Ikke på lager lige nu' : 'Ikke længere i butikkerne' ) . ' – et alternativ i samme prisklasse:</span>'
					. '<a class="pick-alt__item" href="' . esc_url( $a['url'] ) . '">' . ( $a['image'] ? '<img src="' . esc_url( $a['image'] ) . '" alt="" loading="lazy">' : '' )
					. '<span><b>' . esc_html( $a['name'] ) . '</b><small>' . esc_html( playform_kr( $a['price'] ) . ' · Hos ' . $a['shop'] ) . '</small></span></a>'
					. ( $ao ? '<a class="pick-alt__cta" href="' . esc_url( $ao['url'] ) . '" rel="sponsored nofollow noopener" target="_blank">Se pris</a>' : '' ) . '</div>';
			}
		}
		if ( ! $p ) {
			return '<div class="pick pick--gone"><p>Produktet findes ikke længere hos vores butikker.</p>' . $alt_html . '</div>';
		}
		$id    = $p->get_id();
		$d     = playform_card_data( $p );
		$offer = $d['in_stock'] ? playform_pick_offer( $id ) : null;
		$facts = playform_pick_facts( $id );
		$rev   = (array) ( $pick['reviews'] ?? [] );
		ob_start();
		?>
		<div class="pick">
			<a class="pick__media" href="<?php echo esc_url( $d['url'] ); ?>">
				<?php if ( $d['image'] ) : ?><img src="<?php echo esc_url( $d['image'] ); ?>" alt="<?php echo esc_attr( $d['name'] ); ?>" loading="lazy"><?php endif; ?>
				<span class="pick__rank"><?php echo (int) $n; ?></span>
			</a>
			<div class="pick__body">
				<span class="pick__role"><?php echo esc_html( $pick['role'] ?? '' ); ?></span>
				<a class="pick__name" href="<?php echo esc_url( $d['url'] ); ?>"><?php echo esc_html( $d['name'] ); ?></a>
				<?php if ( $rev ) : ?>
					<span class="pick__basis pick__basis--rev"><?php echo playform_icon( 'check', 14, 1.8 ); // phpcs:ignore ?>Anmeldt af
						<?php
						echo implode( // phpcs:ignore
							', ',
							array_map( static fn( $r ) => '<a href="' . esc_url( $r[1] ) . '" target="_blank" rel="noopener">' . esc_html( $r[0] ) . '</a>', $rev )
						);
						?>
					</span>
				<?php else : ?>
					<span class="pick__basis">Vores vurdering ud fra specifikationer, pris og garanti – vi har ikke fundet uafhængige anmeldelser</span>
				<?php endif; ?>
				<?php if ( $facts ) : ?>
					<ul class="pick__facts"><?php foreach ( $facts as $f ) : ?><li><?php echo esc_html( $f ); ?></li><?php endforeach; ?></ul>
				<?php endif; ?>
				<div class="pick__buy">
					<div class="pick__price">
						<?php if ( $d['in_stock'] ) : ?>
							<b<?php echo $d['was'] > $d['price'] ? ' class="is-sale"' : ''; ?>><?php echo esc_html( playform_kr( $d['price'] ) ); ?></b>
							<?php if ( $d['was'] > $d['price'] ) : ?><s title="Laveste pris de 30 dage før tilbuddet"><?php echo esc_html( playform_kr( $d['was'] ) ); ?></s><?php endif; ?>
							<small><?php echo esc_html( $d['sale'] ? 'Tilbud hos ' . $d['shop'] : ( $d['shops'] > 1 ? 'Billigst hos ' . $d['shop'] . ' · ' . $d['shops'] . ' butikker' : 'Hos ' . $d['shop'] ) ); ?></small>
						<?php else : ?>
							<b class="pick__out">Ikke på lager lige nu</b>
						<?php endif; ?>
					</div>
					<?php if ( $offer ) : ?>
						<a class="btn-shop pick__cta" href="<?php echo esc_url( $offer['url'] ); ?>" rel="sponsored nofollow noopener" target="_blank">Se pris hos <?php echo esc_html( $offer['shop'] ); ?> <?php echo playform_icon( 'external', 16 ); // phpcs:ignore ?></a>
					<?php else : ?>
						<a class="pick__more" href="<?php echo esc_url( $d['url'] ); ?>">Se produktet</a>
					<?php endif; ?>
				</div>
				<?php echo $alt_html; // phpcs:ignore -- bygget og escapet ovenfor ?>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}
);

// Sammenligning af de 3 valg.
add_shortcode(
	'fitbase_tabel',
	static function (): string {
		$rows = '';
		foreach ( playform_picks() as $i => $pick ) {
			$p = playform_pick_product( $pick );
			if ( ! $p ) {
				continue;
			}
			$d     = playform_card_data( $p );
			$rows .= '<tr><td><a href="#valg-' . ( $i + 1 ) . '">' . esc_html( $d['name'] ) . '</a></td><td>' . esc_html( $pick['role'] ?? '' ) . '</td><td>'
				. ( $d['in_stock'] ? esc_html( playform_kr( $d['price'] ) ) : 'Ikke på lager' ) . '</td><td>' . esc_html( implode( ' · ', playform_pick_facts( $p->get_id() ) ) ?: '–' ) . '</td><td>'
				. ( empty( $pick['reviews'] ) ? 'Vores vurdering' : 'Anmeldt' ) . '</td></tr>';
		}
		return $rows ? '<table><thead><tr><th>Model</th><th>Bedst til</th><th>Pris lige nu</th><th>Fakta</th><th>Grundlag</th></tr></thead><tbody>' . $rows . '</tbody></table>' : '';
	}
);

// Flere populære modeller i kategorien (automatisk – ingen påstand om anbefaling).
add_shortcode(
	'fitbase_flere',
	static function ( $atts ): string {
		$cat     = sanitize_title( (string) ( $atts['kategori'] ?? '' ) );
		$exclude = array_filter( array_map( static fn( $pk ) => ( $p = playform_pick_product( $pk ) ) ? $p->get_id() : 0, playform_picks() ) );
		$ids     = get_posts(
			[
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'posts_per_page' => max( 2, min( 8, (int) ( $atts['antal'] ?? 4 ) ) ),
				'post__not_in'   => $exclude, // phpcs:ignore
				'orderby'        => 'rand',
				'tax_query'      => $cat ? [ [ 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $cat ] ] : [], // phpcs:ignore
				'meta_query'     => [ // phpcs:ignore
					[ 'key' => '_pf_in_stock', 'value' => 1, 'type' => 'NUMERIC' ],
					[ 'key' => '_pf_image', 'value' => '', 'compare' => '!=' ],
				],
			]
		);
		if ( ! $ids ) {
			return '';
		}
		$term = $cat ? get_term_by( 'slug', $cat, 'product_cat' ) : null;
		ob_start();
		echo '<p class="more-note">Et skiftende udvalg af modeller på lager hos vores butikker – ikke en del af vores anbefaling.</p><div class="grid grid--4 guide-products">';
		foreach ( $ids as $id ) {
			$p = wc_get_product( $id );
			if ( $p ) {
				playform_product_card( $p );
			}
		}
		echo '</div>';
		if ( $term instanceof WP_Term ) {
			echo '<p><a class="btn-ghost" href="' . esc_url( get_term_link( $term ) ) . '">Se alle ' . esc_html( mb_strtolower( playform_cat_label( $term ) ) ) . ' (' . (int) $term->count . ')</a></p>';
		}
		return (string) ob_get_clean();
	}
);

// Struktureret data: listen af valg (ItemList). Ingen Review-markup – vi har ikke testet produkterne.
add_action(
	'wp_head',
	static function () {
		if ( ! is_singular( 'post' ) || ! function_exists( 'playform_json_ld' ) ) {
			return;
		}
		$items = [];
		foreach ( playform_picks( get_queried_object_id() ) as $i => $pick ) {
			$p = playform_pick_product( $pick );
			if ( $p ) {
				$items[] = [ '@type' => 'ListItem', 'position' => $i + 1, 'url' => get_permalink( $p->get_id() ), 'name' => $p->get_name() ];
			}
		}
		if ( $items ) {
			playform_json_ld( [ '@type' => 'ItemList', 'name' => get_the_title( get_queried_object_id() ), 'itemListElement' => $items ] );
		}
	}
);

/* -------------------------------------------------------------------------
 * Adresser fra det gamle fitbase.dk
 * ---------------------------------------------------------------------- */

/** Indlæg, der ligger direkte under roden (/slug/), som på det gamle site. slug => post-ID. */
function playform_root_posts(): array {
	return (array) get_option( 'playform_root_posts', [] );
}

add_filter(
	'post_link',
	static function ( string $link, WP_Post $post ): string {
		$root = playform_root_posts();
		return isset( $root[ $post->post_name ] ) && (int) $root[ $post->post_name ] === (int) $post->ID ? home_url( user_trailingslashit( $post->post_name ) ) : $link;
	},
	10,
	2
);

add_filter(
	'rewrite_rules_array',
	static function ( array $rules ): array {
		$new = [];
		foreach ( playform_root_posts() as $slug => $id ) {
			$new[ '^' . preg_quote( $slug, '#' ) . '/?$' ] = 'index.php?p=' . (int) $id;
		}
		return $new + $rules;
	},
	20
);

/** Gamle adresser, der ikke findes mere. Fravalgte emner går til forsiden (ejerens valg). */
function playform_legacy_redirects(): array {
	$home = home_url( '/' );
	return [
		'citybike-til-herre'                                              => $home,
		'bedste-citybike-til-dame'                                        => $home,
		'vaelg-den-rigtige-fodboldstoevle'                                => $home,
		'vaelg-en-fodboldtroeje-der-matcher-dit-yndlingshold'             => $home,
		'kosttilskud-og-traening-optimer-din-praestation-og-restitution' => $home,
		'cykling'                                                         => $home,
		'annoncoerer'                                                     => $home,
		'sammenklappeligt-loebebaand'                                     => home_url( '/foldbart-loebebaand/' ),
		'koebsguide'                                                      => home_url( '/viden/emne/kobsguide/' ),
		'koebsguide/fitness'                                              => home_url( '/viden/emne/kobsguide/' ),
		'koebsguide/sport'                                                => home_url( '/viden/emne/kobsguide/' ),
		'koebsguide/traeningsmaskiner'                                    => home_url( '/traeningsmaskiner/' ),
		'koebsguide/styrketraening'                                       => home_url( '/styrketraening/' ),
		'koebsguide/traeningsudstyr'                                      => home_url( '/tilbehor/' ),
		'generelt'                                                        => home_url( '/viden/' ),
		'viden/artikler'                                                  => home_url( '/viden/' ),
		'om'                                                              => home_url( '/viden/forfatter/frederik-baastrup/' ),
		'author/mondae'                                                   => home_url( '/viden/forfatter/frederik-baastrup/' ),
	];
}

add_action(
	'template_redirect',
	static function () {
		// Én adresse pr. artikel: /viden/romaskine-med-vand/ → /romaskine-med-vand/
		if ( is_singular( 'post' ) ) {
			$post = get_queried_object();
			$root = playform_root_posts();
			if ( $post instanceof WP_Post && (int) ( $root[ $post->post_name ] ?? 0 ) === (int) $post->ID ) {
				$path = trim( (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ), '/' ); // phpcs:ignore
				if ( $path !== $post->post_name ) {
					wp_safe_redirect( get_permalink( $post ), 301 );
					exit;
				}
			}
			return;
		}
		if ( ! is_404() ) {
			return;
		}
		$path = trim( rawurldecode( (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ) ), '/' ); // phpcs:ignore
		$path = preg_replace( '#/(feed|amp)$#', '', $path );
		$map  = playform_legacy_redirects();
		if ( isset( $map[ $path ] ) ) {
			wp_safe_redirect( $map[ $path ], 301 );
			exit;
		}
	},
	4
);

/* -------------------------------------------------------------------------
 * Indlæsning fra content/best/*.php
 * ---------------------------------------------------------------------- */

/**
 * Opretter eller opdaterer anbefalingerne. Et indlæg, redaktøren selv har rettet, røres ikke
 * (vi sammenligner med fingeraftrykket fra sidste indlæsning).
 */
function playform_seed_best(): void {
	$root = playform_root_posts();
	$cat  = get_term_by( 'slug', 'kobsguide', 'category' );
	if ( ! $cat ) {
		$res = wp_insert_term( 'Købsguides', 'category', [ 'slug' => 'kobsguide' ] );
		$cat = is_wp_error( $res ) ? null : get_term( (int) $res['term_id'], 'category' );
	}
	$author = get_users( [ 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ] );
	foreach ( glob( get_template_directory() . '/content/best/*.php' ) ?: [] as $file ) {
		$a = require $file;
		if ( ! is_array( $a ) || empty( $a['slug'] ) ) {
			continue;
		}
		$html     = playform_best_html( $a );
		$existing = get_posts( [ 'name' => $a['slug'], 'post_type' => 'post', 'post_status' => 'any', 'numberposts' => 1 ] );
		$data     = [
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_name'     => $a['slug'],
			'post_title'    => $a['title'],
			'post_excerpt'  => $a['excerpt'],
			'post_content'  => $html,
			'post_category' => $cat ? [ (int) $cat->term_id ] : [],
			'post_author'   => $author ? (int) $author[0] : 1,
		];
		if ( $existing ) {
			$post = $existing[0];
			if ( get_post_meta( $post->ID, '_pf_seed_hash', true ) !== md5( $post->post_content ) ) {
				$id = $post->ID; // redaktøren har rettet teksten – lad den være
			} else {
				$data['ID'] = $post->ID;
				$id         = wp_update_post( wp_slash( $data ) );
			}
		} else {
			$id = wp_insert_post( wp_slash( $data ) );
		}
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		$saved = get_post_field( 'post_content', $id );
		if ( ! $existing || (int) ( $data['ID'] ?? 0 ) === (int) $id ) {
			update_post_meta( $id, '_pf_seed_hash', md5( $saved ) );
		}
		update_post_meta( $id, '_pf_picks', $a['picks'] ?? [] );
		update_post_meta( $id, '_pf_more_cat', (string) ( $a['more_cat'] ?? '' ) );
		$root[ $a['slug'] ] = (int) $id;
	}
	update_option( 'playform_root_posts', $root, true );
}

/** Bygger indlæggets HTML ud fra artikelfilen. */
function playform_best_html( array $a ): string {
	$h  = trim( $a['intro'] ?? '' ) . "\n";
	$h .= "[fitbase_metode]\n";
	if ( ! empty( $a['picks'] ) ) {
		$h .= "[fitbase_kort]\n";
		foreach ( $a['picks'] as $i => $pk ) {
			$n  = $i + 1;
			$h .= '<h2 id="valg-' . $n . '">' . esc_html( $pk['role'] ) . ': ' . esc_html( $pk['name'] ) . "</h2>\n";
			$h .= '[fitbase_valg nr="' . $n . '"]' . "\n";
			if ( ! empty( $pk['verdict'] ) ) {
				$h .= '<p class="pick-verdict"><b>Kort sagt:</b> ' . esc_html( $pk['verdict'] ) . "</p>\n";
			}
			$h .= trim( $pk['why'] ?? '' ) . "\n";
			if ( ! empty( $pk['good'] ) || ! empty( $pk['less'] ) ) {
				$h .= '<div class="pick-fit"><div class="pick-fit__good"><b>Godt til</b><ul><li>' . implode( '</li><li>', array_map( 'esc_html', $pk['good'] ?? [] ) ) . '</li></ul></div>'
					. '<div class="pick-fit__less"><b>Mindre godt til</b><ul><li>' . implode( '</li><li>', array_map( 'esc_html', $pk['less'] ?? [] ) ) . "</li></ul></div></div>\n";
			}
		}
		$h .= "<h2>Sammenligning af vores top 3</h2>\n[fitbase_tabel]\n";
	}
	if ( ! empty( $a['more_cat'] ) ) {
		$h .= '<h2>Flere populære modeller</h2>' . "\n" . '[fitbase_flere kategori="' . esc_attr( $a['more_cat'] ) . '" antal="6"]' . "\n";
	}
	$h .= trim( $a['guide'] ?? '' ) . "\n";
	if ( ! empty( $a['faq'] ) ) {
		$h .= "<h2>Ofte stillede spørgsmål</h2>\n";
		foreach ( $a['faq'] as [ $q, $ans ] ) {
			$h .= '<h3>' . esc_html( $q ) . '</h3><p>' . esc_html( $ans ) . "</p>\n";
		}
	}
	return $h;
}

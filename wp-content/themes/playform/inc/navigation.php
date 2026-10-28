<?php
/**
 * Hovedmenu og mega-menu. Kategorierne svarer til Playform Feeds' kategoritræ.
 * Links i mega-menuen peger på kategorien med et filter. De bliver til egne landingssider senere.
 */

defined( 'ABSPATH' ) || exit;

function playform_category_url( string $slug, array $args = [] ): string {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	$url  = $term ? get_term_link( $term ) : home_url( '/' );
	if ( is_wp_error( $url ) ) {
		$url = home_url( '/' );
	}
	return $args ? add_query_arg( $args, $url ) : $url;
}

/** @return array<int,array{label:string,slug:string,img:string,cols:array}> */
function playform_menu(): array {
	$price = [
		'title' => 'Pris',
		'links' => [
			[ 'Under 3.000 kr', [ 'pris' => '0-3000' ] ],
			[ '3.000–6.000 kr', [ 'pris' => '3000-6000' ] ],
			[ 'Over 6.000 kr', [ 'pris' => '6000-' ] ],
			[ 'Tilbud', [ 'tilbud' => '1' ] ],
		],
	];
	$s = static fn( string $q ) => [ 'q' => $q ];
	return [
		[
			'label' => 'Løbebånd', 'slug' => 'lobebaand', 'img' => 'hero-lobebaand.jpg',
			'cols'  => [
				[ 'title' => 'Type', 'links' => [ [ 'Foldbart løbebånd', [ 'egenskab' => 'foldbar' ] ], [ 'Gåbånd', $s( 'gåbånd' ) ], [ 'Løbebånd med incline', [ 'egenskab' => 'incline' ] ], [ 'Manuelt løbebånd', $s( 'manuel' ) ] ] ],
				$price,
				[ 'title' => 'Brug', 'links' => [ [ 'Løbebånd til hjemmet', [] ], [ 'Gåbånd til kontoret', $s( 'gåbånd' ) ], [ 'Til tunge brugere', [ 'brugervaegt' => '150-' ] ] ] ],
			],
		],
		[
			'label' => 'Motionscykler', 'slug' => 'motionscykler', 'img' => 'hero-motionscykler.jpg',
			'cols'  => [
				[ 'title' => 'Type', 'links' => [ [ 'Spinningcykel', $s( 'spinning' ) ], [ 'Kondicykel', $s( 'motionscykel' ) ], [ 'Liggecykel', $s( 'liggecykel' ) ], [ 'Airbike', $s( 'air' ) ] ] ],
				$price,
				[ 'title' => 'Tilbehør', 'links' => [ [ 'Pedaltrænere', $s( 'pedaltræner' ) ] ] ],
			],
		],
		[
			'label' => 'Romaskiner & crosstrainere', 'slug' => 'romaskiner-crosstrainere', 'img' => 'mm-crosstrainer.jpg',
			'cols'  => [
				[ 'title' => 'Romaskiner', 'links' => [ [ 'Romaskiner', $s( 'romaskine' ) ], [ 'Romaskine med vandmodstand', $s( 'vand' ) ], [ 'Foldbar romaskine', [ 'egenskab' => 'foldbar' ] ] ] ],
				[ 'title' => 'Crosstrainere', 'links' => [ [ 'Crosstrainer', $s( 'crosstrainer' ) ] ] ],
				$price,
			],
		],
		[
			'label' => 'Vægte & kettlebells', 'slug' => 'vaegte-kettlebells', 'img' => 'mm-vaegte.jpg',
			'cols'  => [
				[ 'title' => 'Vægte', 'links' => [ [ 'Håndvægte', $s( 'håndvægt' ) ], [ 'Vægtskiver', $s( 'vægtskive' ) ], [ 'Vægtstænger', $s( 'stang' ) ], [ 'Vægtstativ', $s( 'stativ' ) ] ] ],
				[ 'title' => 'Kettlebells', 'links' => [ [ 'Kettlebell 12 kg', [ 'q' => 'kettlebell', 'vaegt' => '12' ] ], [ 'Kettlebell 16 kg', [ 'q' => 'kettlebell', 'vaegt' => '16' ] ], [ 'Kettlebell 24 kg', [ 'q' => 'kettlebell', 'vaegt' => '24' ] ], [ 'Alle kettlebells', $s( 'kettlebell' ) ] ] ],
				$price,
			],
		],
		[
			'label' => 'Styrke & bænke', 'slug' => 'styrke-baenke', 'img' => 'mm-baenk.jpg',
			'cols'  => [
				[ 'title' => 'Træningsbænke', 'links' => [ [ 'Træningsbænke', $s( 'bænk' ) ] ] ],
				[ 'title' => 'Styrke', 'links' => [ [ 'Power rack', $s( 'rack' ) ], [ 'Multigym', $s( 'multi' ) ], [ 'Pull-up-stang', $s( 'pull' ) ] ] ],
				$price,
			],
		],
		[
			'label' => 'Yoga & pilates', 'slug' => 'yoga-pilates', 'img' => 'mm-pilates.jpg',
			'cols'  => [
				[ 'title' => 'Yoga', 'links' => [ [ 'Yogamåtte', $s( 'yogamåtte' ) ], [ 'Yogablok', $s( 'blok' ) ] ] ],
				[ 'title' => 'Pilates', 'links' => [ [ 'Pilates reformer', $s( 'reformer' ) ], [ 'Pilatesbold', $s( 'bold' ) ], [ 'Pilatesring', $s( 'ring' ) ] ] ],
				$price,
			],
		],
		[
			'label' => 'Restitution', 'slug' => 'restitution', 'img' => 'mm-restitution.jpg',
			'cols'  => [
				[ 'title' => 'Massage', 'links' => [ [ 'Massagepistol', $s( 'massagepistol' ) ], [ 'Foam roller', $s( 'roller' ) ], [ 'Massagebold', $s( 'bold' ) ] ] ],
				[ 'title' => 'Recovery', 'links' => [ [ 'Recovery boots', $s( 'recovery' ) ], [ 'Kuldeterapi', $s( 'kulde' ) ] ] ],
				$price,
			],
		],
		[
			'label' => 'Tilbehør', 'slug' => 'tilbehor', 'img' => 'mm-tilbehor.jpg',
			'cols'  => [
				[ 'title' => 'Måtter', 'links' => [ [ 'Træningsmåtte', $s( 'måtte' ) ], [ 'Gulvmåtter', $s( 'gulv' ) ] ] ],
				[ 'title' => 'Småudstyr', 'links' => [ [ 'Elastikker', $s( 'elastik' ) ], [ 'Sjippetov', $s( 'sjippetov' ) ], [ 'Medicinbold', $s( 'medicinbold' ) ] ] ],
				[ 'title' => 'Boksning', 'links' => [ [ 'Boksehandsker', $s( 'boksehandske' ) ], [ 'Boksesæk', $s( 'sæk' ) ] ] ],
			],
		],
	];
}

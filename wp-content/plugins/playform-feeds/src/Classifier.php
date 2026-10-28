<?php
namespace Playform\Feeds;

/**
 * Placerer et produkt i Playforms kategoritræ ud fra navn og butikkens kategori.
 * Returnerer en kategori-slug, 'exclude' (skal ikke med) eller Config::uncategorized_slug().
 */
final class Classifier {

	/** Maskinkategorier, hvor tilbehør (silikone, måtter, reservedele …) skal sendes til Tilbehør. */
	private const MACHINE = [ 'lobebaand', 'motionscykler', 'romaskiner-crosstrainere' ];
	private const ACCESSORY = 'silikone|smøre|olie|beskyttelsesmåtte|måtte til|underlag|gulvbeskytt|reservedel|sikkerhedsnøgle|pulsbælte|\bbælte til|motorbælte|holder til|tabletholder|cover|overtræk|sadel|pedal(er)?\b|hjul til';

	public static function classify( array $offer ): string {
		$name = mb_strtolower( $offer['name'] );
		// Mærkenavnet må ikke styre kategorien (fx "American Barbell Squat Stand").
		$brand = mb_strtolower( trim( $offer['brand'] ) );
		$match_name = $brand !== '' && mb_strlen( $brand ) > 2 ? trim( str_replace( $brand, ' ', $name ) ) : $name;
		$cat  = mb_strtolower( $offer['shop_category'] );

		if ( $offer['price'] <= 0 ) {
			return 'exclude';
		}
		foreach ( Config::exclude() as $pattern ) {
			if ( self::m( $pattern, $name ) || self::m( $pattern, $cat ) ) {
				return 'exclude';
			}
		}

		$found = self::first_match( $match_name );
		if ( ! $found && ! in_array( $cat, Config::meaningless_shop_categories(), true ) && $cat !== mb_strtolower( $offer['brand'] ) ) {
			$found = self::first_match( $cat );
		}
		if ( $found && in_array( $found, self::MACHINE, true ) && self::m( self::ACCESSORY, $name ) ) {
			$found = 'tilbehor';
		}
		return $found ?: Config::uncategorized_slug();
	}

	private static function first_match( string $text ): ?string {
		if ( $text === '' ) {
			return null;
		}
		foreach ( Config::categories() as $slug => $def ) {
			if ( self::m( $def['match'], $text ) ) {
				return $slug;
			}
		}
		return null;
	}

	private static function m( string $pattern, string $text ): bool {
		return (bool) preg_match( '~' . $pattern . '~iu', $text );
	}
}

<?php
namespace Playform\Feeds;

/**
 * Gemmer produktbilleder på vores egen server.
 * Det er hurtigere, det holder, selvom en butik flytter sine billeder, og besøgendes
 * IP-adresser sendes ikke til butikkernes servere, bare fordi de ser et produktkort.
 */
final class ImageCache {

	private const MAX = 900; // px på den længste led

	public static function dir(): array {
		$u = wp_upload_dir();
		return [ trailingslashit( $u['basedir'] ) . 'playform-images', trailingslashit( $u['baseurl'] ) . 'playform-images' ];
	}

	private static function name( string $remote ): string {
		return substr( md5( $remote ), 0, 20 ) . '.jpg';
	}

	/** Lokal URL, hvis billedet er hentet – ellers tom streng. */
	public static function local_url( string $remote ): string {
		if ( $remote === '' ) {
			return '';
		}
		[ $dir, $url ] = self::dir();
		$file = self::name( $remote );
		return is_file( "$dir/$file" ) ? "$url/$file" : '';
	}

	/** Sti på serveren til det lokale billede, hvis det er hentet – ellers tom streng. */
	public static function local_path( string $remote ): string {
		if ( $remote === '' ) {
			return '';
		}
		[ $dir ] = self::dir();
		$file    = $dir . '/' . self::name( $remote );
		return is_file( $file ) ? $file : '';
	}

	/** Henter og nedskalerer ét billede. */
	public static function fetch( string $remote ): bool {
		[ $dir ] = self::dir();
		wp_mkdir_p( $dir );
		$dest = $dir . '/' . self::name( $remote );
		if ( is_file( $dest ) ) {
			return true;
		}
		$res = wp_remote_get( $remote, [ 'timeout' => 20, 'redirection' => 3, 'user-agent' => 'Playform/1.0 (+https://playform.dk)' ] );
		if ( is_wp_error( $res ) || wp_remote_retrieve_response_code( $res ) !== 200 ) {
			return false;
		}
		$type = (string) wp_remote_retrieve_header( $res, 'content-type' );
		if ( ! str_starts_with( $type, 'image/' ) ) {
			return false;
		}
		$tmp = wp_tempnam( 'pf-img' );
		file_put_contents( $tmp, wp_remote_retrieve_body( $res ) );
		$editor = wp_get_image_editor( $tmp );
		if ( is_wp_error( $editor ) ) {
			@unlink( $tmp );
			return false;
		}
		$editor->resize( self::MAX, self::MAX, false );
		$editor->set_quality( 82 );
		$saved = $editor->save( $dest, 'image/jpeg' );
		@unlink( $tmp );
		return ! is_wp_error( $saved );
	}

	/**
	 * Henter billeder for produkter, der mangler et lokalt billede.
	 * @return array{fetched:int,failed:int}
	 */
	public static function run( int $limit = 500, ?callable $log = null ): array {
		global $wpdb;
		$rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value <> '' ORDER BY post_id DESC", ProductSync::META_IMAGE ),
			ARRAY_A
		) ?: [];
		$stats = [ 'fetched' => 0, 'failed' => 0 ];
		foreach ( $rows as $r ) {
			if ( $stats['fetched'] + $stats['failed'] >= $limit ) {
				break;
			}
			if ( self::local_url( $r['meta_value'] ) ) {
				continue;
			}
			self::fetch( $r['meta_value'] ) ? $stats['fetched']++ : $stats['failed']++;
			if ( $log && ( $stats['fetched'] + $stats['failed'] ) % 100 === 0 ) {
				$log( sprintf( '  %d hentet, %d fejlede', $stats['fetched'], $stats['failed'] ) );
			}
		}
		return $stats;
	}
}

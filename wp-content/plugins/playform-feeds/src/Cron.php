<?php
namespace Playform\Feeds;

/**
 * Importen kører kl. 05:30 og 17:30 dansk tid.
 * På hostingen bør et rigtigt cronjob kalde: wp playform import (se README).
 */
final class Cron {

	public const HOOK = 'playform_feeds_import';

	public static function register(): void {
		add_action( self::HOOK, [ self::class, 'run' ] );
	}

	public static function schedule(): void {
		if ( wp_next_scheduled( self::HOOK ) ) {
			return;
		}
		$tz   = new \DateTimeZone( 'Europe/Copenhagen' );
		$next = new \DateTime( 'today 05:30', $tz );
		$now  = new \DateTime( 'now', $tz );
		while ( $next <= $now ) {
			$next->modify( '+12 hours' );
		}
		wp_schedule_event( $next->getTimestamp(), 'twicedaily', self::HOOK );
	}

	public static function unschedule(): void {
		wp_clear_scheduled_hook( self::HOOK );
	}

	public static function run(): void {
		if ( get_transient( 'playform_feeds_running' ) ) {
			return;
		}
		set_transient( 'playform_feeds_running', 1, 2 * HOUR_IN_SECONDS );
		try {
			( new Importer() )->run();
			ImageCache::run( 1500 );
			if ( Ai::enabled() ) {
				AiClassifier::run( 400 );
				$n = (int) get_option( 'playform_ai_texts_per_run', 0 );
				foreach ( $n > 0 ? AiWriter::queue( $n ) : [] as $id ) {
					try {
						AiWriter::generate( (int) $id );
					} catch ( \Throwable $e ) {
						break; // fx manglende kredit – prøv igen næste gang
					}
				}
			}
		} finally {
			delete_transient( 'playform_feeds_running' );
		}
	}
}

<?php
namespace Playform\Feeds;

/**
 * Skriver produkttekster med Claude. Teksterne gemmes som udkast og skal godkendes,
 * før de vises på siden (de lægges først i post_content ved godkendelse).
 */
final class AiWriter {

	public const META_DRAFT  = '_pf_ai_draft';
	public const META_STATUS = '_pf_ai_status';   // pending | approved | rejected
	public const META_DATE   = '_pf_ai_date';

	private const SYSTEM = <<<TXT
Du skriver produkttekster på dansk til Playform, en side der samler fitnessudstyr fra flere danske webshops. Læseren overvejer at købe produktet til brug derhjemme.

Regler – de er vigtigere end alt andet:
1. Brug KUN fakta, der står i produktdataene. Opfind aldrig mål, vægt, hastighed, motorkraft, materialer, garanti, programmer eller andre specifikationer.
2. Står en oplysning ikke i dataene, så nævn den ikke. Skriv hellere kortere.
3. Du må gerne give generel, sand købsrådgivning for produkttypen (fx hvad man bør overveje ved et løbebånd), men gør det tydeligt generelt og ikke som en påstand om netop dette produkt.
4. Ingen superlativer uden belæg ("markedets bedste", "perfekt"). Ingen priser, rabatter eller butiksnavne – dem viser siden selv.
5. Naturligt, venligt dansk. Korte afsnit. Du-form.

Svar KUN med JSON:
{"html": "<p>…</p><p>…</p>", "fordele": ["…"], "ulemper": ["…"]}
- html: 2–4 korte afsnit, kun <p>, <h3>, <ul>, <li>, <strong>.
- fordele/ulemper: 0–3 punkter hver, kun hvis dataene understøtter dem. Ellers tomme lister.
TXT;

	/** Loft over antal tekster pr. døgn, så et fejlbehæftet job aldrig kan løbe løbsk. */
	public const DAILY_CAP_DEFAULT = 200;

	public static function generate( int $product_id ): array {
		$cap   = (int) get_option( 'playform_ai_daily_cap', self::DAILY_CAP_DEFAULT );
		$today = current_time( 'Y-m-d' );
		$used  = (array) get_option( 'playform_ai_usage', [] );
		if ( ( $used[ $today ] ?? 0 ) >= $cap ) {
			throw new \RuntimeException( "Dagens loft på $cap tekster er nået." );
		}
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			throw new \RuntimeException( "Produkt $product_id findes ikke." );
		}
		$attrs = (array) get_post_meta( $product_id, ProductSync::META_ATTR, true );
		$cat   = (string) get_post_meta( $product_id, ProductSync::META_CATEGORY, true );
		$facts = [
			'navn'        => $product->get_name(),
			'mærke'       => (string) get_post_meta( $product_id, ProductSync::META_BRAND, true ),
			'kategori'    => Config::categories()[ $cat ]['name'] ?? $cat,
			'beskrivelse' => (string) get_post_meta( $product_id, ProductSync::META_FEED_DESC, true ),
			'egenskaber'  => $attrs,
		];
		$answer = Ai::json( Ai::ask( Ai::MODEL_WRITING, self::SYSTEM, "Produktdata:\n" . wp_json_encode( $facts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ), 1500 ) );
		if ( ! is_array( $answer ) || empty( $answer['html'] ) ) {
			throw new \RuntimeException( 'Uventet svar fra Claude.' );
		}
		$draft = [
			'html'    => wp_kses( $answer['html'], [ 'p' => [], 'h3' => [], 'ul' => [], 'li' => [], 'strong' => [] ] ),
			'pros'    => array_slice( array_map( 'sanitize_text_field', (array) ( $answer['fordele'] ?? [] ) ), 0, 3 ),
			'cons'    => array_slice( array_map( 'sanitize_text_field', (array) ( $answer['ulemper'] ?? [] ) ), 0, 3 ),
			'sources' => $facts,
		];
		update_post_meta( $product_id, self::META_DRAFT, $draft );
		update_post_meta( $product_id, self::META_STATUS, 'pending' );
		update_post_meta( $product_id, self::META_DATE, current_time( 'mysql' ) );
		$used = array_slice( [ $today => ( $used[ $today ] ?? 0 ) + 1 ] + $used, 0, 60, true );
		update_option( 'playform_ai_usage', $used, false );
		return $draft;
	}

	/** Godkender udkastet (evt. rettet af redaktøren) og viser det på siden. */
	public static function approve( int $product_id, ?string $html = null, ?array $pros = null, ?array $cons = null ): void {
		$draft = (array) get_post_meta( $product_id, self::META_DRAFT, true );
		$html  = $html ?? ( $draft['html'] ?? '' );
		wp_update_post( [ 'ID' => $product_id, 'post_content' => wp_kses_post( $html ) ] );
		update_post_meta( $product_id, '_pf_pros', array_values( array_filter( $pros ?? ( $draft['pros'] ?? [] ) ) ) );
		update_post_meta( $product_id, '_pf_cons', array_values( array_filter( $cons ?? ( $draft['cons'] ?? [] ) ) ) );
		update_post_meta( $product_id, self::META_STATUS, 'approved' );
	}

	public static function reject( int $product_id ): void {
		update_post_meta( $product_id, self::META_STATUS, 'rejected' );
	}

	/** Produkter uden tekst, de vigtigste først (på lager, højeste pris). */
	public static function queue( int $limit, string $category = '' ): array {
		$args = [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => $limit,
			'meta_key'       => '_price',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			'meta_query'     => [
				[ 'key' => self::META_STATUS, 'compare' => 'NOT EXISTS' ],
				[ 'key' => ProductSync::META_IN_STOCK, 'value' => 1, 'type' => 'NUMERIC' ],
			],
		];
		if ( $category ) {
			$args['tax_query'] = [ [ 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $category ] ];
		}
		return get_posts( $args );
	}
}

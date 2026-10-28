<?php
namespace Playform\Feeds;

/**
 * Al opsætning ét sted: shops, kategoritræ og sorteringsregler.
 * En ny niche (fx hvidevarer) er en ny udgave af denne fil.
 */
final class Config {

	/** Partner-ads feeds. Kan overstyres med option 'playform_shops'. */
	public static function shops(): array {
		$base     = 'https://www.partner-ads.com/dk/feed_udlaes.php?partnerid=14854';
		$defaults = [
			'apuls'                 => [ 'name' => 'Apuls.dk',              'url' => "$base&bannerid=46187&feedid=637" ],
			'fitnessshoppen'        => [ 'name' => 'Fitnessshoppen.dk',     'url' => "$base&bannerid=74933&feedid=1680" ],
			'traeningspartner'      => [ 'name' => 'Træningspartner',       'url' => "$base&bannerid=67325&feedid=1378" ],
			'den-intelligente-krop' => [ 'name' => 'Den Intelligente Krop', 'url' => "$base&bannerid=38097&feedid=400" ],
			'optimasport'           => [ 'name' => 'OptimaSport.dk',        'url' => "$base&bannerid=83091&feedid=2014" ],
		];
		$custom = get_option( 'playform_shops' );
		return is_array( $custom ) && $custom ? $custom : $defaults;
	}

	/**
	 * Playforms kategorier. 'match' testes først mod produktnavnet, derefter mod butikkens kategori.
	 * Rækkefølgen betyder noget: den første kategori, der matcher, vinder.
	 */
	public static function categories(): array {
		return [
			'lobebaand'          => [ 'name' => 'Løbebånd', 'match' => 'løbebånd|gåbånd|walking ?pad|curve runner|treadmill' ],
			'motionscykler'      => [ 'name' => 'Motionscykler', 'match' => 'siddecykel|spinningcykel|spinning ?bike|spin ?bike|motionscykel|kondicykel|liggecykel|air ?bike|assault ?bike|ergometercykel|indoor ?cycle|indendørscykel|pedaltræner' ],
			'romaskiner-crosstrainere' => [ 'name' => 'Romaskiner & crosstrainere', 'match' => 'romaskine|rower|rowing|roergometer|ski ?erg|crosstrainer|cross ?trainer|elliptical' ],
			'vaegte-kettlebells' => [ 'name' => 'Vægte & kettlebells', 'match' => 'bodypump|ez ?curl|curl ?bar|weight ?bar|vægtstænger|kettlebell|håndvægt|dumbbell|vægtskive|bumper|vægtstang|barbell|vægtstativ|stativ til håndvægt|curlstang|olympisk stang|vægtvest|ankelvægt' ],
			'styrke-baenke'      => [ 'name' => 'Styrke & bænke', 'match' => 'multi træningsmaskine|træningsmaskine|stack|lat[- ]?attachment|træningsbænk|vægtbænk|\bbænk\b|\bbench\b|\brack\b|racks|power ?cage|squat|smith|multigym|home ?gym|styrkemaskine|kabeltræk|lat ?pull|leg ?press|benpres|pulley|keiser|plate loaded|pull[- ]?up|dip ?station|chin[- ]?up|vægtstak' ],
			'yoga-pilates'       => [ 'name' => 'Yoga & pilates', 'match' => 'yoga|pilates|reformer|meditation' ],
			'restitution'        => [ 'name' => 'Restitution', 'match' => 'sauna|jacknobber|ekoroll|rygstrækker|backflex|massage|foam ?roller|foamroller|recovery|kuldeterapi|varmeterapi|isbad|ice ?bath|triggerpunkt|massagepistol' ],
			'tilbehor'           => [ 'name' => 'Tilbehør', 'match' => 'fitnessbold|træningsbold|\\bbold\\b|grips|løftekæde|hammer|wallbar|ribbe|slyngetræner|push-?up|parallettes|stepbænk|vægtvest|weight ?vest|power ?bags?|timer|pegboard|hand ?grip|flexbar|hand ?wraps|håndbind|\\bmma\\b|boksehjelm|benskinne|shin ?guard|skridtbeskytter|tandbeskytter|løftebælte|træningsbælte|fjederlås|\\bgreb\\b|attachment|hoppetræning|vibrationstræner|chalk|magnesia|kinesio|sportstape|\\btape\\b|boksehandske|boksesæk|boksebold|boksning|sandsæk|træningsmåtte|gulvmåtte|underlag|gulv\b|måtte|elastik|træningsbånd|loop ?band|modstandsbånd|sjippetov|springreb|ab ?wheel|mavehjul|medicinbold|slam ?ball|wall ?ball|gymball|balancebold|balance|stepbræt|træningshandske|løftestropper|vægtløfterbælte|greb og håndtag|håndtag|kalk|sandbag|battle ?rope|plyo|agility|træningsudstyr|core|boldtræning|suspension|trx|ringe|vægtløftning' ],
		];
	}

	/** Produkter, der aldrig skal med (navn eller butikkens kategori). */
	public static function exclude(): array {
		return [
			'gavekort',
			'\bguide\b|guides til|tips og tricks|de bedste|de \d bedste|vedligeholder du',
			'kosttilskud|protein|kreatin|\bbcaa\b|vitamin|mad og drikke|drikkeflask|termokop',
			'\bsko\b|sneaker|t-shirt|tights|shorts|bukser|jakke|hættetrøje|sportstøj|træningstøj|fritidstøj|tøj & sko|kompressionsbeklædning|strømpe|sokker|undertøj|nederdel|kjole',
			'løbesko|leggins|leggings|termokrus|camping|grill|flåt|næsestrip|basketstander|netgarniture|skudtræner|shine & protect|bandage|sleeve|\\bbrace\\b|albuebeskytter|knæbeskytter|smerteplaster|plaster\\b|træningssko|pulsmåling|pleje|hygiejne|bøger|\bbog\b|skærmbrille|computer-|søvn|udlejning|\bdemo\b|reservedel|trampolin|håndbold|fodbold|basketball|volleyball|svøm|padel|tennis|golf|kampsport|graviditet|sanser|børn|ergonomi|funktionelle møbler|\bnav\b|fokus|lyd &|måleapparat|åndedræt|bækkenbund|støttebind|hårtilbehør|\btens\b|hydrogen',
		];
	}

	/** Butikskategorier, hvor kun vores egne regler må afgøre placeringen (de siger intet om produktet). */
	public static function meaningless_shop_categories(): array {
		return [ 'brands', 'træningsudstyr a-z', 'tilbud', 'nyheder', 'sport', 'wellness', 'tilbehør', 'fødder', 'ryg', 'mave', 'hænder', 'træning' ];
	}

	public static function uncategorized_slug(): string {
		return 'ukategoriseret';
	}
}

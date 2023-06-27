<?php
/**
 *  GET RANDOM TERM FROM TEXT FILES
 */

function random_terms( $count = 10 )
{
	$files  = glob( storage_path('keyword') . '/' . get_one( config('agc.folder_keyword') ) . '/*.txt');
	if( empty($files) ) di("Please upload a folder contains list keyword into folder 'storage/keyword/{yourfolder}'");
	$file		= $files[array_rand($files)];
	$get    = explode("\n", file_get_contents_utf8($file));
	$terms	= array_filter( array_utf8($get) );
	$total 	= count( $terms ) - 1;


	if( $count > $total ) return $terms;

	$key 	= rand_numb(0, $total, $count);
 	foreach($key as $i)
	{
			if( isset($terms[$i]) ) {
				$output[] = 	( config('agc.filter_badwords_terms') ) ? filter_badstrings( $terms[$i] ) : $terms[$i]; //filter_badstrings( $terms[$i] );
			}
	}

	$output = array_filter($output);

	if($count == 1) {
		return $output[0];
	}
	return $output;
}


/**
 *  LIMIT WORDS COUNT
 */

function limit_the_words($text, $mark)
{
  	if (is_numeric($mark)) {
  		$text = explode(" ",$text);
  		$text = implode(" ",array_splice($text,0,$mark));
  	} else {
  		$text = explode($mark, $text)[0];
  	}
  	return $text;
}

/**
 *  GENERATE TERM FORM ARRAY
 */
function random_strings($array, $count = 5, $separator = ' ', $func = 'ucwords', $shuffle = false )
{
  	if( exist_key_multi('title', $array) ) {
      	if( $shuffle ) {
						shuffle($array);
				}
      	foreach($array as $item)
				{
      			$data[] = clean( $item['title'] );
      	}
  	} else {
  		$data = $array;
		if( $shuffle ) {
			shuffle($data);
		}
  	}
  	$the_title = array_slice($data, 0, $count);
  	return ( !function_exists( $func ) ) ? implode($separator, $the_title) : implode( $separator, array_map($func, $the_title) );
}

/**
 *  GENERATE CLEAN PERMALINK
 */

function clean($str, $delimiter = ' ', $options = array())
{
	// Make sure string is in UTF-8 and strip invalid UTF-8 characters
	$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

	$defaults = array(
		'delimiter' 	=> $delimiter,
		'limit' 		=> null,
		'lowercase' 	=> true,
		'replacements' 	=> array(),
		'transliterate' => false,
	);

	// Merge options
	$options = array_merge($defaults, $options);

	$char_map = array(
		// Latin
		'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
		'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
		'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
		'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
		'ß' => 'ss',
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
		'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
		'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
		'ÿ' => 'y',

		// Latin symbols
		'©' => '(c)',

		// Greek
		'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
		'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
		'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
		'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
		'Ϋ' => 'Y',
		'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
		'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
		'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
		'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
		'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

		// Turkish
		'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
		'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',

		// Russian
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
		'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
		'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
		'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
		'Я' => 'Ya',
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
		'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
		'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
		'я' => 'ya',

		// Ukrainian
		'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
		'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

		// Czech
		'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
		'Ž' => 'Z',
		'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
		'ž' => 'z',

		// Polish
		'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
		'Ż' => 'Z',
		'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
		'ż' => 'z',

		// Latvian
		'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
		'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
		'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
		'š' => 's', 'ū' => 'u', 'ž' => 'z',

		// German
		'Ä' => 'AE', 'Ö' => 'OE', 'Ü' => 'UE',
		'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue',

	);

	// Make custom replacements
	$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

	// Transliterate characters to ASCII
	if ($options['transliterate']) {
		$str = str_replace(array_keys($char_map), $char_map, $str);
	}

	// Replace non-alphanumeric characters with our delimiter
	$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

	// Truncate slug to max. characters
	$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

	// Remove delimiter from ends
	$str = trim($str, $options['delimiter']);

	$str = str_replace( 'amp', '', $str );

	$cleanStr = $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;

	//$config_badwords = config('agc.filter_badwords_text');
	//$config_commons  = config('agc.filter_common_words');

	// if( $config_badwords && $config_commons )
	// {
	// 		$cleanStr = filter_common_words( $cleanStr );
	// 		$cleanStr = filter_badstrings( $cleanStr );
	// } elseif( $config_badwords ) {
	// 		$cleanStr = filter_badstrings( $cleanStr );
	// } elseif( $config_commons ) {
	// 	  $cleanStr = filter_common_words( $cleanStr );
	// }

	// Remove duplicate delimiters
	$cleanStr = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $cleanStr);

	return $cleanStr;

}

/**
 *  GENERATE CLEAN PERMALINK WITHOUT NUMBER
 */
function clean_number($text) {
	$tt			= trim(preg_replace('/[0-9]+/', '', $text));
	return clean($tt);
}

/**
 *  GENERATE CLEAN PERMALINK UTF8
 */
function clean_utf8($str) {
    $output = preg_replace_callback("/\\\u([0-9a-f]{4})/i",
        create_function('$matches',
            'return html_entity_decode(\'&#x\'.$matches[1].\';\', ENT_QUOTES, \'UTF-8\');'
        ), $str);
		return $output;
}


/**
 *  GENERATE CLEAN PERMALINK WITHOUT ANY EXTENSION
 */
function clean_extension($string)
{
	$replace = preg_replace('/(www\.|\.com|\.org|\.net|\.int|\.edu|\.gov|\.mil|\.ac|\.ad|\.ae|\.af|\.ag|\.ai|\.al|\.am|\.an|\.ao|\.aq|\.ar|\.as|\.at|\.au|\.aw|\.ax|\.az|\.ba|\.bb|\.bd|\.be|\.bf|\.bg|\.bh|\.bi|\.bj|\.bm|\.bn|\.bo|\.bq|\.br|\.bs|\.bt|\.bv|\.bw|\.by|\.bz|\.bzh|\.ca|\.cc|\.cd|\.cf|\.cg|\.ch|\.ci|\.ck|\.cl|\.cm|\.cn|\.co|\.cr|\.cs|\.cu|\.cv|\.cw|\.cx|\.cy|\.cz|\.dd|\.de|\.dj|\.dk|\.dm|\.do|\.dz|\.ec|\.ee|\.eg|\.eh|\.er|\.es|\.et|\.eu|\.fi|\.fj|\.fk|\.fm|\.fo|\.fr|\.ga|\.gb|\.gd|\.ge|\.gf|\.gg|\.gh|\.gi|\.gl|\.gm|\.gn|\.gp|\.gq|\.gr|\.gs|\.gt|\.gu|\.gw|\.gy|\.hk|\.hm|\.hn|\.hr|\.ht|\.hu|\.id|\.ie|\.il|\.im|\.in|\.io|\.iq|\.ir|\.is|\.it|\.je|\.jm|\.jo|\.jp|\.ke|\.kg|\.kh|\.ki|\.km|\.kn|\.kp|\.kr|\.krd|\.kw|\.ky|\.kz|\.la|\.lb|\.lc|\.li|\.lk|\.lr|\.ls|\.lt|\.lu|\.lv|\.ly|\.ma|\.mc|\.md|\.me|\.mg|\.mh|\.mk|\.ml|\.mm|\.mn|\.mo|\.mp|\.mq|\.mr|\.ms|\.mt|\.mu|\.mv|\.mw|\.mx|\.my|\.mz|\.na|\.nc|\.ne|\.nf|\.ng|\.ni|\.nl|\.no|\.np|\.nr|\.nu|\.nz|\.om|\.pa|\.pe|\.pf|\.pg|\.ph|\.pk|\.pl|\.pm|\.pn|\.pr|\.ps|\.pt|\.pw|\.py|\.qa|\.re|\.ro|\.rs|\.ru|\.rw|\.sa|\.sb|\.sc|\.sd|\.se|\.sg|\.sh|\.si|\.sj|\.sk|\.sl|\.sm|\.sn|\.so|\.sr|\.ss|\.st|\.su|\.sv|\.sx|\.sy|\.sz|\.tc|\.td|\.tf|\.tg|\.th|\.tj|\.tk|\.tl|\.tm|\.tn|\.to|\.tp|\.tr|\.tt|\.tv|\.tw|\.tz|\.ua|\.ug|\.uk|\.us|\.uy|\.uz|\.va|\.vc|\.ve|\.vg|\.vi|\.vn|\.vu|\.wf|\.ws|\.ye|\.yt|\.yu|\.za|\.zm|\.zr|\.zw|\.academy|\.accountants|\.active|\.actor|\.adult|\.aero|\.agency|\.airforce|\.app|\.archi|\.army|\.associates|\.attorney|\.auction|\.audio|\.autos|\.band|\.bar|\.bargains|\.beer|\.best|\.bid|\.bike|\.bio|\.biz|\.black|\.blackfriday|\.blog|\.blue|\.boo|\.boutique|\.build|\.builders|\.business|\.buzz|\.cab|\.camera|\.camp|\.cancerresearch|\.capital|\.cards|\.care|\.career|\.careers|\.cash|\.catering|\.center|\.ceo|\.channel|\.cheap|\.christmas|\.church|\.city|\.claims|\.cleaning|\.click|\.clinic|\.clothing|\.club|\.coach|\.codes|\.coffee|\.college|\.community|\.company|\.computer|\.condos|\.construction|\.consulting|\.contractors|\.cooking|\.cool|\.country|\.credit|\.creditcard|\.cricket|\.cruises|\.dad|\.dance|\.dating|\.day|\.deals|\.degree|\.delivery|\.democrat|\.dental|\.dentist|\.diamonds|\.diet|\.digital|\.direct|\.directory|\.discount|\.domains|\.eat|\.education|\.email|\.energy|\.engineer|\.engineering|\.equipment|\.esq|\.estate|\.events|\.exchange|\.expert|\.exposed|\.fail|\.farm|\.fashion|\.feedback|\.finance|\.financial|\.fish|\.fishing|\.fit|\.fitness|\.flights|\.florist|\.flowers|\.fly|\.foo|\.forsale|\.foundation|\.fund|\.furniture|\.gallery|\.garden|\.gift|\.gifts|\.gives|\.glass|\.global|\.gop|\.graphics|\.green|\.gripe|\.guide|\.guitars|\.guru|\.healthcare|\.help|\.here|\.hiphop|\.hiv|\.holdings|\.holiday|\.homes|\.horse|\.host|\.hosting|\.house|\.how|\.info|\.ing|\.ink|\.institute|\.insure|\.international|\.investments|\.jobs|\.kim|\.kitchen|\.land|\.lawyer|\.legal|\.lease|\.lgbt|\.life|\.lighting|\.limited|\.limo|\.link|\.loans|\.lotto|\.luxe|\.luxury|\.management|\.market|\.marketing|\.media|\.meet|\.meme|\.memorial|\.menu|\.mobi|\.moe|\.money|\.mortgage|\.motorcycles|\.mov|\.museum|\.name|\.navy|\.network|\.new|\.ngo|\.ninja|\.one|\.ong|\.onl|\.ooo|\.organic|\.partners|\.parts|\.party|\.pharmacy|\.photo|\.photography|\.photos|\.physio|\.pics|\.pictures|\.pink|\.pizza|\.place|\.plumbing|\.poker|\.porn|\.post|\.press|\.pro|\.productions|\.prof|\.properties|\.property|\.qpon|\.recipes|\.red|\.rehab|\.ren|\.rentals|\.repair|\.report|\.republican|\.rest|\.reviews|\.rich|\.rip|\.rocks|\.rodeo|\.rsvp|\.sale|\.science|\.services|\.sexy|\.shoes|\.singles|\.social|\.software|\.solar|\.solutions|\.space|\.supplies|\.supply|\.support|\.surf|\.surgery|\.systems|\.tattoo|\.tax|\.technology|\.tel|\.tips|\.tires|\.today|\.tools|\.top|\.town|\.toys|\.trade|\.training|\.travel|\.university|\.vacations|\.vet|\.video|\.villas|\.vision|\.vodka|\.vote|\.voting|\.voyage|\.wang|\.watch|\.webcam|\.website|\.wed|\.wedding|\.whoswho|\.wiki|\.work|\.works|\.world|\.wtf|\.xxx|\.xyz|\.yoga|\.zone)/i', '', $string);
	$replace = trim($replace, ' ');
	return $replace;
}

/**
 *  unique word
 */
function unique_words($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 *  Get Gallery Data by Title / Without ID Images
 */
function getGallery($title, $results)
{
	  $id  = array_search(clean($title), array_column($results, 'title'));
		if( !$id ) {
				$id = rand( 0, count($results)-1 );
		}
		$beforeGallery = isset( $results[$id-1] ) ? $results[$id-1] : $results[$id];
		$afterGallery = isset( $results[$id+1] ) ? $results[$id+1] : $results[$id];

		return [
			'prev'	=> $beforeGallery,
			'current'	=> $results[$id],
			'next'		=> $afterGallery
		];
}

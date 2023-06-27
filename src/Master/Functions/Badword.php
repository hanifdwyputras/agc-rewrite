<?php
use Masterzain\Classes\Grabbing;

/*
 *	cek absolute niche
 */
function cek_niche( $query = null )
{
		if( !$query ) {
				$query = search_query();
		}

		if( config('niche.stritch') )
		{
				$stritchkeyword = explode( ',', config('niche.keyword') );
				preg_match_all( "/(" . implode("|", $stritchkeyword) . ")/", $query, $match );
				$count = count( array_filter( $match[1] ) );
				return ( $count > 0 ) ? true : false;
		}
		return true;
}

/*
 *	filter bad keyword on url
 */

function getBadwords( $type = "badwords" )
{
		if( config('remote.badwords.remote') )
		{
				$path					= BASE_PATH . '/' . config('cache.dir') . '/' . config('cache.path.views');

				if( !is_dir($path) ) {
						mkdir($path, 0777, true);
				}
				$filePath 		= "{$path}/badwords_{$type}.txt";

				if( !file_exists($filePath) || !$badwords = file_get_contents( $filePath ) )  {
						$badwords	= Grabbing::curl( [ 'url' => config( 'remote.badwords.source.'. $type ) ] );
						file_put_contents( $filePath, $badwords );
				}

				$badwords = ( isset($badwords) ) ? explode( ',', $badwords ) : null;

		} else {
				$words 			= include( storage_path('badwords.php') );
				$badwords 	= explode(',', $words[$type] );
		}

		return $badwords;

}

/*
 *	filter bad keyword on url
 */
function is_badwords( $string = null, $action = false )
{
	$uri		= ( !empty($string) ) ? $string : get_permalink();
	$uri 		= preg_replace("/[\/_|+-.:]+/", ' ', $uri);

	$badwords = getBadwords( "badwords" );

	$found			= preg_match_all(
                "/\b(" . implode("|", $badwords) . ")\b/i",
                $uri, $match
              );

	if( $found ) {
			if( $action ) {
					redirection();
			}
			return true;
	}
}

/*
 *	filter bad keyword on string
 */
function filter_badstrings( $text )
{
	$text 		= strtolower( $text );
	$badwords  = getBadwords( "badwords" );

	return preg_replace( "/\b(" . implode("|", $badwords) . ")\b/i", "", $text );

	$found		= preg_match_all(
                "/\b(" . implode("|", $badwords) . ")\b/i",
                $text, $match
              );

	if( $found ) {
		$bads 		= array_unique( $match[0] );
		$artext		= explode(' ', $text);
		foreach( $artext as $k => $txt )
		{
		  if(in_array($txt, $bads)) {
				unset($artext[$k]);
		  }
		}
		$text 		= implode(' ', $artext);
	}
	return $text;
}

function filter_common_words( $text )
{
		$text 		= strtolower( $text );
		$badwords  = getBadwords( "commonwords" );

		return preg_replace( "/\b(" . implode("|", $badwords) . ")\b/i", "", $text );

		$found		= preg_match_all(
									"/\b(" . implode("|", $badwords) . ")\b/i",
									$text, $match
								);

		if( $found ) {
			$bads 		= array_unique( $match[0] );
			$artext		= explode(' ', $text);
			foreach( $artext as $k => $txt )
			{
				if(in_array($txt, $bads)) {
					unset($artext[$k]);
				}
			}
			$text 		= implode(' ', $artext);
		}
		return $text;
}

<?php

use Masterzain\Classes\FastCache;

/* UPDATE CACHE DATASOURCE */
function updateSource( $function )
{
    $sourceCache = getSource();
    if( $sourceCache === $function ) {
      return;
    }
    file_put_contents( base_path( "/cache/datasource.txt" ), $function );
}

function getSource()
{
    $file = base_path( "/cache/datasource.txt" );
    if( !file_exists($file) ) {
        file_put_contents( $file, config('agc.default_search') );
    }
    return file_get_contents( $file );
}

function file_get_contents_utf8($fn) {
     $content = file_get_contents($fn);
     return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

function array_utf8( $array )
{
    $rterm = array();
    foreach( $array as $item ) {
        $rterm[] = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $item );
    }
    return $rterm;
}
/*
 *	Diedump Laravel
 */
if( !function_exists('di') ) {
	function di($array = array() )
  {

	if( ob_get_length() > 0) {
		ob_end_clean();
	}

		if( function_exists('dump') ) {
			   dump($array);
		} else {
			   highlight_string("<?php\n\$array =\n" . var_export($array, true) . ";\n?>");
		}
		exit();
	}
}

/*
 *	Pinger Domain Sitemap
 */
function pinger( $url = '' ){
	if( empty($url) ) {
		$url = home_url( $url );
	}
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_exec($ch);
	curl_close($ch);
	return true;
}

/**
 * Remove folder cache
 */
function delete_cache( $parent_folder = 'views', $path = null ) {

	if( $parent_folder ) {
		$cache_folder = BASE_PATH . '/'. config('cache.dir') .'/' . $parent_folder;
	} else {
		$cache_folder = $path;
	}

	if( is_dir( $cache_folder ) ) {
		$i = new DirectoryIterator($cache_folder);
		foreach($i as $f) {
			if($f->isFile())
			{
					unlink($f->getRealPath());
			} else if(!$f->isDot() && $f->isDir()) {
				delete_cache( null, $f->getRealPath());
			}
		}

		return rmdir($cache_folder) ? true : false;
	}

}

/**
 * Fake Sitemap date
 */
function sitemap_date()
{
	return date('Y-m-d') . 'T' . date('H:i:s') . 'Z';
}

/**
 * Check URL has extension
 */

function has_extension( $url )
{
	$path 	= parse_url($url, PHP_URL_PATH);
	$ext 	= pathinfo($path, PATHINFO_EXTENSION);
	return ( !empty($ext) ) ? $ext : false;
}

/**
 * Filter Keyword Before Grab
 */

function is_keyword( $keyword )
{
		if( str_word_count($keyword) < config('niche.min_keyword') ) {
				redirection();
		}
		if( !cek_niche($keyword) && config('niche.action') == 'redirect' ) {
				redirection();
		}
		return $keyword;
}
/**
 * Generate Title & Description
 */

function seo_meta( $meta_seo = null, $data = array() )
{
	$meta['title'] 	= (isset($data['title']) && !empty($data['title'])) ? $data['title'] : "";
	$meta['desc'] 	= (isset($data['description']) && !empty($data['description'])) ? $data['description'] : "";
	$meta['query'] 	= (isset($data['query']) && !empty($data['query'])) ? $data['query'] : "";
	$meta['subquery'] = (isset($data['subquery']) && !empty($data['subquery'])) ? $data['subquery'] : "";
	$meta['results'] = (isset($data['results']) && !empty($data['results'])) ? $data['results'] : array();
	$meta['page_title'] = (isset($data['page_title']) && !empty($data['page_title'])) ? $data['page_title'] : "";
	$meta['list_title'] = (isset($data['list_title']) && !empty($data['list_title'])) ? $data['list_title'] :"";
	$meta['random_terms'] 	= (isset($data['random_terms']) && !empty($data['random_terms'])) ? $data['random_terms'] : array();

	// check if meta_seo contain {random...
	preg_match_all('~{random\K.*(?=})~Uis', $meta_seo, $randome );
	preg_match_all('~{query\K.*(?=})~Uis', $meta_seo, $querye );
	preg_match_all('~{subquery\K.*(?=})~Uis', $meta_seo, $sube );


	$random 	= ( !empty($randome[0]) ) ? '{random'.$randome[0][0].'}' : '{random}';
	$query 		= ( !empty($querye[0]) ) ? '{query'.$querye[0][0].'}' : '{query}';
	$subquery 	= ( !empty($sube[0]) ) ? '{subquery'.$sube[0][0].'}' : '{subquery}';

	$replacement = array();

	// get random value
	if( !empty($randome[0]) ) {
		$rd 		= seo_meta_random( $random );
		$randomdata = random_strings( $meta[$rd['data']], $rd['count'], $rd['separator'], $rd['char'], $rd['shuffle'] );
		$replacement[$random] = $randomdata;
	}

	if( !empty($querye[0]) || !empty($sube[0]) ) {
		$querydata	= seo_char( $query, $meta['query'] );
		$subdata	= seo_char( $subquery, $meta['subquery'] );
		$replacement[$query] 	= $querydata;
		$replacement[$subquery] = $subdata;
	}

	// toReplace
	$replacement['{title}']		 = $meta['title'];
	$replacement['{description}'] = $meta['desc'];
	$replacement['{count}']		 = ( isset($meta['results']) ) ? count($meta['results']) : "";
	$replacement['{sitename}']	 = sitename();
	$replacement['{page_title}'] = $meta['page_title'];
	$replacement['{list_title}'] = $meta['list_title'];

	$output = strtr($meta_seo, $replacement);

	return $output;
}

function seo_meta_random( $random = null )
{
	$opt = array();
	if(strpos($random, "|"))
	{
		$option = explode('|', $random);
		$opt['data'] 		= isset($option[1]) ? $option[1] : "results";
		$opt['count'] 		= isset($option[2]) ? $option[2] : 5;
		$opt['separator'] 	= isset($option[3]) ? $option[3] : ", ";
		$opt['char'] 		= isset($option[4]) ? $option[4] : "strtolower";
		$opt['shuffle'] 	= ( isset($option[5]) && $option[5] == 'false' ) ? false : true;
	} else {
		$opt['data'] 		= "results";
		$opt['count'] 		= 5;
		$opt['separator'] 	= ", ";
		$opt['char'] 		= "strtolower";
		$opt['shuffle'] 	= false;
	}

	return $opt;
}

function seo_char( $pattern, $query )
{
	if(strpos($pattern, "|"))
	{
		$option 		= explode('|', $pattern);
		$function		= isset($option[1]) ? trim($option[1], "}") : "strtolower";
		if(function_exists($function))
		{
			return $function($query);
		}
		return strtolower( $query );
	}
	return strtolower( $query );

}

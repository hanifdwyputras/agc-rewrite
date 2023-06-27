<?php

/**
 *  SANITIZE HTML
 */
function sanitize_output($buffer) {
    $search = array(
        '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
        '/[^\S ]+\</s',  // strip whitespaces before tags, except space
        '/(\s)+/s',       // shorten multiple whitespace sequences
		'/\>([\s])\</s',
		'/\>\s([a-zA-Z0-9-_\s]+)\s\</s'
    );
    $replace = array(
        '>',
        '<',
        '\\1',
		'><',
		'>\\1<'
    );
	
	$buffer = preg_replace($search, $replace, $buffer);
    
	$minify_html = config('themes.minify_html');
		
	if( count($minify_html) == 1 && $minify_html[0] == 'html' )
	{
		return $buffer;
	}

    //replaces
    $buffer = str_replace(" type=\"text/javascript\"", "", $buffer);
    $buffer = str_replace(" type=\"text/css\"", "", $buffer);

    //minify the css
    $buffer=preg_replace_callback("/<style>([\s\S]*?)<\/style>/",function($matches){
        $css=$matches[1];
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        $css = str_replace(array("\r\n","\r","\n","\t",'  ','    ','     '), '', $css);
        $css = preg_replace(array('(( )+{)','({( )+)'), '{', $css);
        $css = preg_replace(array('(( )+})','(}( )+)','(;( )*})'), '}', $css);
        $css = preg_replace(array('(;( )+)','(( )+;)'), ';', $css);
        return '<style>'.$css.'</style>';
    },$buffer);

    
    //replace script elements
    $buffer=preg_replace_callback("/<script>([\s\S]*?)<\/script>/",function($matches){
        $minifiedCode = minify_js($matches[1]);
        return '<script>'.$minifiedCode.'</script>';
    },$buffer);

    return $buffer;	
}

// JavaScript Minifier
function minify_js($input) {
    if(trim($input) === "") return $input;
    return preg_replace(
        array(
            // Remove comment(s)
            '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
            // Remove white-space(s) outside the string and regex
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
            // Remove the last semicolon
            '#;+\}#',
            // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
            '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
            // --ibid. From `foo['bar']` to `foo.bar`
            '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
        ),
        array(
            '$1',
            '$1$2',
            '}',
            '$1$3',
            '$1.$3'
        ),
    $input);
}

/**
 *  STRIP ALL TAGS IN HTML
 */
function strip_all_tags($string, $remove_breaks = true)
{

	$re = '/([\w.]+@[\w.]+)
	|
	(?:(?:\b|[,\/]\s*)(?:wa|whatsapp|pin|viber|wechat))+\b\s*[:：]?\s*(\+?\d+)
	|
	\bline(?:\sid)?\s*(?:[:：]\s*)?(@\w+)
	|
	([\d -]{6,}\d)
	|
	\bBBM?\s*:\s*(\w+)/ix';
	$string = preg_replace($re, "", $string);
	$string = strip_tags($string, '<b><img><p><noscript><li><br><ul><span><table><tr><th><td>');
	$string = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $string);
	$string = preg_replace( '@<(script|style|noscript)[^>]*?>.*?</\\1>@si', '', $string );
	$string = preg_replace('/class=".*?"/', '', $string);
	$string = preg_replace('/style=".*?"/', '', $string);
	$string = preg_replace('/id=".*?"/', '', $string);
	$string = str_replace('data-original', 'src', $string);
	$string = preg_replace('#(" src.*?).*?(alt)#', '" $2', $string);
	$string = str_replace('">','',$string);
	$string = str_replace('<img','<img rel="nofollow"', $string);
    if ( $remove_breaks )
        $string = preg_replace('/[\r\n\t]+/', '<br>', $string);

    return trim( $string );
}

<?php

/**
 *  cek user agent (true or false)
 */

function user_agent($device = 'bot') {
	if( empty( $_SERVER['HTTP_USER_AGENT'] ) && !isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
		return;
	}
  	$user_agent 	= strtolower ( $_SERVER['HTTP_USER_AGENT'] );
  	$bot 			= preg_match ( "/addthis|adsbot|adsbot-google|ask|bingbot|duckduckbot|facebook|facebookexternalhit|facebot|Facebot|feedfetcher-google|googlebot|Googlebot-Image|Googlebot-Mobile|Googlebot-News|Googlebot-Video|Mediapartners-Google|msnbot|pingdom\.com|watchmouse|yahoo|yahoobot|yahooseeker/", $user_agent);
  	$browser 		= preg_match ( "/mozilla\/|opera\//", $user_agent);
  	$mobile 		= preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo|mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent );

  	$types 			= array (
  		'bot'		=> $bot,
  		'browser'	=> $browser,
  		'mobile'	=> $mobile,
  	);
  	return $types[$device];
}

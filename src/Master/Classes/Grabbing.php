<?php

namespace Masterzain\Classes;

class Grabbing
{

  public function __construct()
  {

  }

  public static function parse( $_start, $_end, $HTML ) {
    $_start = str_replace("'", "\'", $_start);
    $_start = str_replace(" ", "[^>]+", $_start);
    $_end   = str_replace("'", "\'", $_end);
    $_end   = str_replace(" ", "[^>]+", $_end);
    $parser = preg_match_all('~'.$_start.'\K.*(?='.$_end.')~Uis', $HTML, $output );
    return $output[0];
  }

  public static function curl($DATA=array())
  {
  	if(!isset($DATA['url']))
    {
  	     return false;
  	}
    $data = curl_init();
  	$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
  	$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
  	$header[] = "Cache-Control: max-age=0";
  	$header[] = "Connection: keep-alive";
  	$header[] = "Keep-Alive: 300";
  	$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
  	$header[] = "Accept-Language: en-us,en;q=0.5";
  	$header[] = "Pragma: ";
    curl_setopt($data, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($data, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($data, CURLOPT_URL, $DATA['url']);
    if(isset($DATA['useragent']) && !empty($DATA['useragent']))
    {
    	 curl_setopt($data, CURLOPT_USERAGENT, $DATA['useragent']);
    }
    curl_setopt($data, CURLOPT_HTTPHEADER, $header);
    if(isset($DATA['referer']) && !empty($DATA['referer']))
    {
    	 curl_setopt($data, CURLOPT_REFERER, $DATA['referer']);
    }
    if(isset($DATA['proxy']) && !empty($DATA['proxy']))
    {
    	 curl_setopt($data, CURLOPT_PROXY, $DATA['proxy']);
    }
    if(isset($DATA['account_proxy']) && !empty($DATA['account_proxy']))
    {
        curl_setopt($data, CURLOPT_PROXYUSERPWD, $DATA['account_proxy']);
    }
    if(isset($DATA['post']) && !empty($DATA['post']))
    {
    		curl_setopt($data, CURLOPT_POSTFIELDS, $DATA['post_data']);
    		curl_setopt($data, CURLOPT_POST,1);
	  }
    curl_setopt($data, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($data, CURLOPT_AUTOREFERER, true);
    curl_setopt($data, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($data, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($data, CURLOPT_TIMEOUT, 60);
    curl_setopt($data, CURLOPT_MAXREDIRS, 7);
    curl_setopt($data, CURLOPT_FOLLOWLOCATION, true);
    if(isset($DATA['source']) && !empty($DATA['source']))
    {
		curl_setopt($data, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'] . '/cache/cookies_'.$DATA['source'].'.txt');
		curl_setopt($data, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'] . '/cache/cookies_'.$DATA['source'].'.txt');
	}

    $render_output = curl_exec($data);

    if(isset($DATA['minify']) && $DATA['minify'] === true ){
    	$render_output = sanitize_output($render_output);
    }

    if(isset($DATA['header']))
    {
      $status_header_output = curl_getinfo($data, CURLINFO_HTTP_CODE);
    }
    curl_close($data);
    if(isset($DATA['header'])) {
       return array(
            'HTTP_OUTPUT' => $render_output,
            'HTTP_HEADER' => $status_header_output,
        );
    } else {
        return $render_output;
    }
  }

}

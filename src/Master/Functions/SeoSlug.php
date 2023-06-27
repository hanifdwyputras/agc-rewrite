<?php

/**
 *  HOME URL
 */
function home_url( $arg = null, $www = false )
{
		if (
	    isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ||
	    ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ||
	    ! empty( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on'
	  ) {
			$ssl = true;
		} else {
	    $ssl = false;
	  }

		$protocol = $ssl ? 'https' : 'http';
		$www = ( $www ) ? 'www.' : '';
		$host = '://' . $www . $_SERVER['HTTP_HOST'];
		$url = $protocol . $host;
		if( $arg ) {
			return $url . '/' . ltrim($arg, '/');
		} 
		return $url;
}

/**
 *  CANONICAL URL
 */
function get_permalink( $HttpQueryString = false )
{
	if( $HttpQueryString )
	{
			return home_url( $_SERVER['REQUEST_URI'] );
	}
	return home_url( parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) );
}

/**
 *  Sitename
 */
function sitename()
{
	return ucwords( $_SERVER['HTTP_HOST'] );
}

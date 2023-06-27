<?php

use Masterzain\Classes\Input;
use Masterzain\Classes\Routing;

/*
 *	Input as non static function
 */
function input()
{
	return new Input;
}

/*
 *	Calling pagination class
 */
function pagination()
{
	return new Masterzain\Classes\Pagination;
}

/*
 *	REDIRECTION
 */
function redirection( $url = "" ) {
	if( empty($url) || $url == "/" ) {
		$location = home_url();
	} else {
		$location = $url;
	}
	header("Location: " . $location );
	exit();
}

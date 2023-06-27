<?php

use Masterzain\Classes\Blade;

/*
 *	Render permalink, combining with routing parameter
 */
function permalink( $params = null, $name = 'search', $url = null )
{
	if( !$params ) {
		return url( $name );
	}

	$param 	= is_array( $params ) ? $params : [ $params ];
	$config = get_parameter( $name );
	$key 	= array_keys($config);


	if (in_array("random", $key))
	{
			$param = array_merge( [ unique_words( rand(5,8) ) ], $param );
	}

	if (in_array("firstword", $key))
	{
			$param = array_merge( [ explode(' ',trim($param[0]))[0] ], $param );
	}

	if (in_array("firstchar", $key))
	{
			$param = array_merge( [ $param[0][0] ], $param );
	}

	if( $config )
	{
			foreach( $key as $k => $v )
			{
					if( !isset($param[$k]) ) di( "Permalink {$name} requires the #{$k} parameter, null given");
					$out[$v] = $param[$k];

					if( !is_numeric( $out[$v] ) )
					{
							$out[$v] = ( $url ) ? trim($param[$k], '/') : clean($param[$k], "-");
					}
			}
			$output = url( $name, $out );

			return ( !has_extension($output) ) ? $output : rtrim($output, "/");
	}

}

/**
 * GALLERY PERMALINK WITHOUT ID
 */

function attachment_url( $query, $subquery, $link = false )
{
		if( $link || config('themes.attachment') ){
			return permalink( [ $query, $subquery ], 'attachment' );
		}

		if( !$link || !config('themes.attachment') )
		{
				return '#';
		}
}

/**
 * FAKE UPLOAD IMAGE
 */

function image_url( $query, $subquery )
{
	return permalink( [ $query, $subquery ], 'image' );
}

/**
 * THEME URL, ASSETS URL
 */

function base_path( $path = null )
{
		if( !defined("BASE_PATH") ) {
				define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] );
		}
		return BASE_PATH . $path;
}

function theme_url( $request = '' )
{
		$themePath = strtr( config('app.blade.themes'), [ '{%active_theme%}' => config('themes.active_theme') ] ) . "/" . trim($request, '/');
		return home_url( $themePath );
}

function assets_url( $request = '' )
{
		return home_url( "assets/". $request );
}

function assets_path ( $request = '' )
{
	return base_path( "/assets/". $request );
}

function storage_path( $dir = '' )
{
		return base_path( '/storage/' . $dir );
}

function theme_path( $dir = "" )
{
		$theme_path = config('app.blade.themes');
		$theme_path = explode( '/', $theme_path )[0];
		if( $dir ) {
			$theme_path = $theme_path . '/' . $dir;
		}
		return base_path( '/' . $theme_path );
}

/**
 * SITEMAP XSL
 */
function sitemap_xsl( $type = 'index' )
{
	$xsl = config('agc.sitemap_xsl');
	return assets_url('xsl/'.$type.'-'.$xsl.'.xsl');
}

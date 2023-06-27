<?php

namespace Masterzain\Classes;

// use Pecee\Handlers\IExceptionHandler;

use Pecee\Http\Request;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;

use Pecee\SimpleRouter\SimpleRouter;

class Routing extends SimpleRouter
{

    private static $patterns = [
          '(a)'   	 => '([a-zA-Z]+)',
          '(n)' 	 => '([0-9]+)',
          '(uri)'    => '([a-zA-Z0-9\-\_]+)',
          '(l)'  	 => '([a-z]+)',
          '(u)'  	 => '([A-Z]+)',
          '(any)'    => '([\W\w]+)'
    ];
	
	public static function Errors($callback)
	{
		self::error(function(Request $request, \Exception $exception) use ($callback)
		{
			if($exception instanceof NotFoundHttpException && $exception->getCode() == 404) {
				if( !$callback ){
					redirection();
				}
				$request->setRewriteCallback( $callback );
				return $request;				
			}
		});
	}

    public static function run($request, $url, $callback)
    {
		// return parent::$request(Routing::takeUrl($url), function() use ($callback, $url) {
		// 	var_dump(home_url
		// 	return 'oi';
		// })
		// 	->where(static::takeWhere($url))
		// 	->name(static::takeName($callback));
		return Routing::$request( static::takeUrl($url), $callback)
					 ->where( static::takeWhere($url) )
					 ->name( static::takeName($callback) );
    }
	
	protected static function takeUrl( $url )
	{
		preg_match_all('~:\K.*(?=})~Uis', $url, $params );	
	
		if( empty($params[0]) ) return $url;
		
		foreach( $params[0] as $key => $param)
		{
			$get[] = ":$param";
		}
		$url = preg_replace("/\b(" . implode("|", $get) . ")\b/i", "", $url);
		return $url;	
	}
	
	protected static function takeWhere( $url )
	{
		preg_match_all('~{\K.*(?=})~Uis', $url, $params );

		if( empty($params[0]) ) return $url = [];
		
		foreach( $params[0] as $key => $param)
		{
			$par = explode(":", $param);
			$get[$par[0]]	= strtr( "({$par[1]})", static::getPatterns() );
		}
		return $get;
	}
	
	protected static function takeName( $callback )
	{
		$get = explode( "@", $callback );
		return $get[1];
	}
	
	protected static function getPatterns()
	{
		$configs = config('routes');
		return ( $configs ) ? array_merge( static::$patterns, $configs ) : static::$patterns;
	}
}

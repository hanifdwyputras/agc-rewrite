<?php

namespace Masterzain\Classes;

class Session
{

    private static $inited = false;

    public static function init()
	{
        if (!static::$inited) {
            session_start();
            static::$inited = true;
        }
    }

    public static function set($key, $value)
    {
		self::init();
		if( !isset($_SESSION[$key]) )
		{
			$_SESSION[$key] = $value;
		}
		return true;
    }
    
	public static function get($key, $item = null)
	{
		self::init();	
		if (isset($_SESSION[$key])) {
			if(isset($item) && isset($_SESSION[$key][$item])) {
				return $_SESSION[$key][$item];
			}

			return $_SESSION[$key];
		} 

		return null;
	}
    
    public static function delete($key, $type = '' )
    {
		self::init();
        unset($_SESSION[$key]);
		if( $type ){
			session_destroy();
		}
		return true;
    }
	
	public static function has($key)
	{
		self::init();
		if (isset($_SESSION[$key])){
			return true;
		}
		return;
	}
    
}

<?php

class Uri
{
	
	public static function string()
	{
		return $_SERVER['REQUEST_URI'];
	}
	
	public static function all()
	{
        $uris = self::string();
		
        if(isset($_GET))
        {
            $uris = explode('?',$uris);
            $uris = $uris[0];
        }
        $uris = explode('/', trim($uris, '/'));   
		return $uris;
	}
	
	public static function count()
	{
		return count( self::all() );
	}
	
    public static function segment($arg=null)
    {
		$uris = self::all();
        if($arg==null)
        {
            return end($uris);
        }
        else
        {
            array_unshift($uris, null);
            unset($uris[0]);
            if(isset($uris[$arg]))
            {
                return $uris[$arg];
            }
            else
            {
                return null;
            }
        }
    }
}
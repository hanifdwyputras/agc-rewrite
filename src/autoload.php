<?php

if( !defined("BASE_PATH") || !defined("APP_PATH") )
{
	define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] );
	define('APP_PATH',  BASE_PATH . '/app' );	
}

foreach ( glob( __DIR__ . '/Master/Functions/*.php') as $file)
{
    require_once $file;
}
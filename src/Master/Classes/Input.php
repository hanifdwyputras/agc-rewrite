<?php

namespace Masterzain\Classes;

class Input {

	public static function all() {
		if( !empty($_POST) && !empty($_GET) ) {
			return [
				'post'	=> $_POST,
				'get'	=> $_GET
			];
		} elseif( !empty($_POST) || !empty($_GET) ) {
			if( !empty($_POST) ) {
				return [
					'post'	=> $_POST,
				];
			} else {
				return [
					'get'	=> $_GET,
				];
			}
		} else {
			return;
		}
	}

	public static function exist($type = 'post') {
		switch ($type) {
			case "post":
				return (!empty($_POST)) ? true : null;
			break;
			case "get":
				return (!empty($_GET)) ? true : null;
			break;
			default:
				return;
			break;
		}

	}

	public static function get($data = null) {
		$all = self::all();
		if(is_null($data)) {
			return isset($all['get']) ? $all['get'] : null;
		}
		if (isset($all['get'][$data])){
			return $all['get'][$data];
		}
		return;
	}

	public static function post($data = null) {
		$all = self::all();
		if(is_null($data)) {
			return isset($all['post']) ? $all['post'] : null;
		}
		if (isset($all['post'][$data])){
			return $all['post'][$data];
		}
		return;
	}
}

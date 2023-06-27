<?php

namespace Masterzain\Classes;

class Sitemap {
	public $folder_keyword;
	public function __construct() {
		$this->folder_keyword	= config('agc.folder_keyword');
	}

	public function count_sitemap() {
		$all_file		= glob( storage_path('keyword') . '/' . get_one( $this->folder_keyword ) . '/*.txt');
		return count($all_file);
	}

	public function single_sitemap( $loop = 0, $count = 100 ) {
		$loop = $loop - 1;
		$all_file		= glob( storage_path('keyword') . '/' . get_one( $this->folder_keyword ) . '/*.txt');
		if(array_key_exists($loop, $all_file) ) {
			$get_file		= explode("\n", file_get_contents_utf8($all_file[$loop]));
			shuffle($get_file);
			$in_file = array_filter( array_utf8($get_file) );
			$output = array_slice($in_file, 0, $count);
			return $output;
		}
	}

}

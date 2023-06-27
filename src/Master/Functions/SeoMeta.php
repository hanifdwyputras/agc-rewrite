<?php
use Masterzain\Classes\Grabbing;
/*
 *	Render permalink, combining with routing parameter
 */
function suggestion( $keyword = null )
{
		$keyword = ( $keyword ) ? $keyword : search_query();
		$data_source = [
				'url'				=> 'http://suggestqueries.google.com/complete/search?output=toolbar&q=' . urlencode($keyword),
				'proxy'			=> '',
				'referer'		=> 'http://google.com/',
				'useragent'	=> '',
		];

		return $geturl = file_get_contents ( utf8_encode ( $data_source['url'] ) );

		$html         = Grabbing::curl($data_source);
		preg_match_all('~<suggestion data="\K.*(?=")~Uis', $html, $terms );
		return ( $terms[0] ) ? $terms[0] : null;
}

/*
 *	Render permalink, combining with routing parameter
 */
function meta_data( $meta = 'google' )
{
		$config = config('meta.verification');
		return isset( $config[$_SERVER['HTTP_HOST']] ) ? $config[$_SERVER['HTTP_HOST']][$meta] : null;
}

/*
 *	Render meta robot index follow
 */
function meta_index( $path = 'index', $default = false )
{
		$meta_index = "";

		if( !cek_niche() ) {
				if( search_query() )
				{
						if( config('niche.action') == 'noindex' ) {
								$meta_index .= '<meta name="robots" content="noindex,nofollow,noarchive,nosnippet"/>';
						} elseif( config('niche.action') == 'redirect' ) {
								redirection();
						}
				}
		} else {
				if( in_array($path, config('agc.meta_index') ) && empty( input()->get('page') ) )
				{
						$meta_index .= '<meta name="googlebot" content="index,follow,imageindex"/>';
						$meta_index .= '<meta name="robots" content="all,index,follow"/>';
						$meta_index .= '<meta name="googlebot-Image" content="index,follow"/>';
				} else {
						$meta_index .= '<meta name="robots" content="noindex,follow"/>';
				}
		}

		// if( in_array($path, config('agc.meta_index') ) && empty( input()->get() ) )
		// {
		// 		if( is_search() || is_attachment() ) {
		// 				if( !cek_niche() && config('niche.action') == 'noindex' )
		// 				{
		// 						$meta_index .= '<meta name="robots" content="noindex,nofollow,noarchive,nosnippet"/>';
		// 				} elseif( !cek_niche() && config('niche.action') == 'redirect' ) {
		// 						redirection();
		// 				} else {
		// 						$meta_index .= '<meta name="googlebot" content="index,follow,imageindex"/>';
		// 						$meta_index .= '<meta name="robots" content="all,index,follow"/>';
		// 						$meta_index .= '<meta name="googlebot-Image" content="index,follow"/>';
		// 				}
		// 		} else  {
		// 				$meta_index .= '<meta name="googlebot" content="index,follow,imageindex"/>';
		// 				$meta_index .= '<meta name="robots" content="all,index,follow"/>';
		// 				$meta_index .= '<meta name="googlebot-Image" content="index,follow"/>';
		// 		}
		// } else {
		// 		$meta_index .= '<meta name="robots" content="noindex,follow"/>';
		// }
		return $meta_index;
}

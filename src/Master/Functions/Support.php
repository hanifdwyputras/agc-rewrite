<?php

use Illuminate\Config\Repository;
use Masterzain\Classes\Grabbing;

/*
 *	Remote Ads Function
 */
function remoteAds( $adsType )
{
	$adsPath		= BASE_PATH . '/' . config('cache.dir') . '/' . config('cache.path.views');
	$adsFilePath 	= "{$adsPath}/remoteAds_{$adsType}.txt";

	if( !file_exists($adsFilePath) || !$adsCode = file_get_contents( $adsFilePath ) )  {
		$adsCode	= Grabbing::curl( [ 'url' => config( 'remote.ads.source.'. $adsType ) ] );
		file_put_contents( $adsFilePath, $adsCode );
	}

	return $adsCode;
}

/*
 *	Render Ads on Blade
 */
function ads( $ads = "responsive" )
{
	if( !user_agent('bot') )
	{
		if( config('agc.filter_badwords_ads') && filter_badwords() ) {
			return;
		} else {
			if( config('remote.ads.remote') ) {
				return remoteAds( $ads );
			} else {
        if( file_exists( BASE_PATH . '/ads/' . $ads . '.php' ) ) {
      				include ( BASE_PATH . '/ads/' . $ads . '.php' );
        }
			}
		}
	}
}

/*
 *	Config like Laravel
 */
function config( $configFiles )
{
	if (strpos($configFiles, '.') !== false) {
		$getData		= explode('.', $configFiles);
		$configFile = $getData[0];
		$params			= implode('.', array_slice( $getData, 1 ) );
	} else {
		$configFile	= $configFiles;
	}

  if( !file_exists( APP_PATH . '/config/'.$configFile.'.php' ) ) return;

	$config = new Repository(require APP_PATH . '/config/'.$configFile.'.php');

	if (strpos($configFiles, '.') !== false) {
		return $config->get($params);
	}

	return $config->all();
}

<?php

namespace Masterzain\Classes;

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;

// use phpFastCache\Core\phpFastCache;

class FastCache
{

	protected $_disabled 	= false;
	private $instance		= null;

	public function __construct()
	{
		$config = [
		  'path' 				   => BASE_PATH . '/' . config('cache.dir'),
		//   'securityKey'		 	   => config('cache.path.sub'),
		];

		if( !$this->instance ) {
		  if( config('cache.disabled') ) {
			  $this->instance = CacheManager::getInstance('Devnull', new ConfigurationOption($config));
		  } else {
			  $this->instance = CacheManager::getInstance( config('cache.driver') , new ConfigurationOption($config));
		  }
		}
	}

	public function disabled( $value = "" )
	{
	  $this->_disabled = $value;
	  return $this;
	}

	public function getDisabled()
	{
	  return $this->_disabled;
	}

	public function get($cachekey = null)
  {
		  return $this->instance->getItem($cachekey)->get();
	}

	public function save($cachekey, $data, $expires = '', $tags = null )
	{
		if(!isset($data) && empty($data) ) return;
		
		$saving		= $this->instance->getItem($cachekey);
		if( !$saving->get() )
		{
			if( $expires ) {
					$saving->set($data)->expiresAfter( $expires );
			} else {
					$saving->set($data)->expiresAfter( 0 );
			}

			if( $tags ) {
          $saving->setTags( $tags );
			}
			return $this->instance->save($saving);
		}
	}

	public function getRandByTag( $tag )
	{
		$data = $this->instance->getItemsByTag( $tag );
		if( $data) {
			foreach($data as $k => $v) {
				$key[]	= $k;
			}
			shuffle($key);
			return self::get($key[0]);
		}
	}

	public function remove( $cache_key = array() )
  {
		  return $this->instance->deleteItems($cache_key);
	}

  public function update( $cache_key, $data_cache, $expires )
  {
      $this->remove( [$cache_key] );
      $this->save( $cache_key, $data_cache, $expires );
  }

	public function clear()
	{
		return $this->instance->clear();
	}

}

<?php

namespace Masterzain\Classes;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class Blader {

    /**
		* @var include blade path
     */
    private $bladePath = array();

    /**
     * @var render the blade
     */
    private $render = array();

    /**
     * @var setup content type for xml, rss, and robots.txt
     */
    protected $ContentType = "text/xml";

    /**
     * @var setup status response code for sitemap
     */
    protected $Response    = "200";

	protected $setTheme       = "";

    public function __construct()
    {
		//di($this->setTheme);
		//$this->init();
	}

	protected function init()
	{
		$default_theme = BASE_PATH . '/' . config('app.blade.themes');

		if( ! $this->setTheme ) {
			$theme_name = config('themes.active_theme');
		} else {
			$theme_name = $this->setTheme;
		}

		$this->setPath([
			'Cache' 	  => BASE_PATH . '/' . config('cache.dir') . '/' . config('cache.path.views'),
			'Themes' 	  => strtr( $default_theme, [ '{%active_theme%}' => $theme_name ] ),
			'Admin' 	  => BASE_PATH . '/' . config('app.blade.admin'),
			'Library'  	=> BASE_PATH . '/' . config('app.blade.library'),
			'Default'	  => BASE_PATH . '/' . config('app.blade.default'),
		]);

    // Dependencies
    $filesystem       = new Filesystem;
    $eventDispatcher  = new Dispatcher(new Container);

    // Create View Factory capable of rendering PHP and Blade templates
    $viewResolver     = new EngineResolver;

    if (!is_dir( $this->bladePath['Cache'] )) {
        mkdir( $this->bladePath['Cache'], 0777, true);
    }

    $bladeCompiler    = new BladeCompiler($filesystem, $this->bladePath['Cache'] );


    $viewResolver->register('blade', function () use ($bladeCompiler, $filesystem) {
        return new CompilerEngine($bladeCompiler, $filesystem);
    });

    $viewResolver->register('php', function () {
        return new PhpEngine;
    });



    // $viewFinder       = new FileViewFinder($filesystem, $pathsToTemplates);
    // $viewFactory      = new Factory($viewResolver, $viewFinder, $eventDispatcher);

    $this->setFinder([
			'theme'     => new FileViewFinder( $filesystem, [ $this->bladePath['Themes'] ] ),
			'admin'   	=> new FileViewFinder( $filesystem, [ $this->bladePath['Admin'] ] ),
			'library' 	=> new FileViewFinder( $filesystem, [ $this->bladePath['Library'] ] ),
			'default'   => new FileViewFinder( $filesystem, [ $this->bladePath['Default'] ] ),
		]);

		$this->setRender([
			'theme'     => new Factory( $viewResolver, $this->finder['theme'], $eventDispatcher ),
			'admin'   	=> new Factory( $viewResolver, $this->finder['admin'], $eventDispatcher ),
			'library' 	=> new Factory( $viewResolver, $this->finder['library'], $eventDispatcher ),
			'default'   => new Factory( $viewResolver, $this->finder['default'], $eventDispatcher ),
		]);

		$this->addPath( theme_path('static') );

		return $this;

	}

  public function addPath( $path )
  {
         $this->finder['theme']->addLocation( $path );
         return $this;

  }

	public function setTheme( $theme )
	{
		$this->setTheme = $theme;
		return $this;
	}

	public function getTheme()
	{
		return $this->init();
		if( ! $this->setTheme ) {
			return config('themes.active_theme');
		}
		return $this->init();
	}

	protected function setPath( $path = array() )
	{
		$this->bladePath = $path;
		return $this;
	}

	protected function setRender( $render = array() )
	{
		$this->render = $render;
		return $this;
	}

  protected function setFinder( $finder = array() )
	{
		$this->finder = $finder;
		return $this;
	}

  	public function theme( $file, $data = array() )
    {
    		ob_start("ob_gzhandler");
    		ob_start("sanitize_output");
    		return $this->init()->render['theme']->make($file, $data)->render();
  	}

  	public function admin( $file, $data = array() )
    {
  		  return $this->init()->render['admin']->make($file, $data)->render();
  	}

  	public function view( $file, $data = array() )
    {
  		  return $this->init()->render['default']->make($file, $data)->render();
  	}


  	public function library( $file, $data = array() )
    {
    		ob_start();
        if( config('options.library.disabled') ) {
          redirection();
        }
    		header("HTTP/1.1 {$this->Response}");
    		header("Status: {$this->Response}");
			  header("Content-type: {$this->ContentType}");
    		return $this->init()->render['library']->make($file, $data)->render();
  	}

  	public function image( $imageURI )
    {
			ob_start();
			header("Content-type: {$this->ContentType}");
			echo file_get_contents( $imageURI );
			exit();
  	}

    public function json(array $value)
    {
        $this->header('Content-Type: application/json');
        echo json_encode($value);
        die();
    }

    public function header( $value = "text/xml", $code = 200 )
    {
        $this->ContentType  = $value;
		switch ($code) {
			case 100: $text = 'Continue'; break;
			case 101: $text = 'Switching Protocols'; break;
			case 200: $text = 'OK'; break;
			case 201: $text = 'Created'; break;
			case 202: $text = 'Accepted'; break;
			case 203: $text = 'Non-Authoritative Information'; break;
			case 204: $text = 'No Content'; break;
			case 205: $text = 'Reset Content'; break;
			case 206: $text = 'Partial Content'; break;
			case 300: $text = 'Multiple Choices'; break;
			case 301: $text = 'Moved Permanently'; break;
			case 302: $text = 'Moved Temporarily'; break;
			case 303: $text = 'See Other'; break;
			case 304: $text = 'Not Modified'; break;
			case 305: $text = 'Use Proxy'; break;
			case 400: $text = 'Bad Request'; break;
			case 401: $text = 'Unauthorized'; break;
			case 402: $text = 'Payment Required'; break;
			case 403: $text = 'Forbidden'; break;
			case 404: $text = 'Not Found'; break;
			case 405: $text = 'Method Not Allowed'; break;
			case 406: $text = 'Not Acceptable'; break;
			case 407: $text = 'Proxy Authentication Required'; break;
			case 408: $text = 'Request Time-out'; break;
			case 409: $text = 'Conflict'; break;
			case 410: $text = 'Gone'; break;
			case 411: $text = 'Length Required'; break;
			case 412: $text = 'Precondition Failed'; break;
			case 413: $text = 'Request Entity Too Large'; break;
			case 414: $text = 'Request-URI Too Large'; break;
			case 415: $text = 'Unsupported Media Type'; break;
			case 500: $text = 'Internal Server Error'; break;
			case 501: $text = 'Not Implemented'; break;
			case 502: $text = 'Bad Gateway'; break;
			case 503: $text = 'Service Unavailable'; break;
			case 504: $text = 'Gateway Time-out'; break;
			case 505: $text = 'HTTP Version not supported'; break;
			default:
				di('Unknown http status code "' . htmlentities($code) . '"');
			break;
		}
        $this->Response     = $code . " " . $text;
        return $this;
    }
}

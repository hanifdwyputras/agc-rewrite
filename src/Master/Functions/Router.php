<?php

use Pecee\SimpleRouter\SimpleRouter;

function url($name = null, $parameters = null, $getParams = null)
{
    $url = SimpleRouter::getUrl($name, $parameters, $getParams);
    return home_url( $url );
}

/**
 * @return \Pecee\Http\Response
 */
function response()
{
    return SimpleRouter::response();
}

/**
 * @return \Pecee\Http\Request
 */
function request()
{
    return SimpleRouter::request();
}

/**
 * Get input class
 * @return \Pecee\Http\Input\Input
 */
function inputs()
{
    return request()->getInput();
}

function redirect($url, $code = null)
{
    if ($code !== null) {
        response()->httpCode($code);
    }

    response()->redirect($url);
}

function search_query()
{
    $query = request()->getLoadedRoute()->getParameters();
    return ( isset( $query['query'] ) ) ? clean( $query['query'] ) : null;
}

function attachment_query()
{
    $query = request()->getLoadedRoute()->getParameters();
    return ( isset( $query['subquery'] ) ) ? clean( $query['subquery'] ) : null;
}

function page_title()
{
    $query = request()->getLoadedRoute()->getParameters();
    return ( isset( $query['page'] ) ) ? $query['page'] : null;
}

function is_search()
{
  $router = request()->getLoadedRoute()->getName();
  return ( $router == 'search' ) ? true : false;
}

function is_home()
{
  $router = request()->getLoadedRoute()->getName();
  return ( $router == 'index' ) ? true : false;
}

function is_attachment()
{
  $router = request()->getLoadedRoute()->getName();
  return ( $router == 'attachment' ) ? true : false;
}

function is_page()
{
  $router = request()->getLoadedRoute()->getName();
  return ( $router == 'page' ) ? true : false;
}

function is_list()
{
  $router = request()->getLoadedRoute()->getName();
  return ( $router == 'list_term' ) ? true : false;
}


function get_parameter( $name = "" )
{
	$params = SimpleRouter::router()->findRoute( $name );

	if (isset($params) && !empty($params) ) {
		return $params->getWhere();
	}

	return;

}

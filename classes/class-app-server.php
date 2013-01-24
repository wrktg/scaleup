<?php
class ScaleUp_App_Server {

  protected $_routes;

  function __construct() {
    if ( !is_admin() ) {
      add_filter( 'do_parse_request', array( $this, 'do_parse_request' ) );
    }
  }

  function do_parse_request( $continue ) {

    list( $method, $uri, $args ) = $this->prepare_request();

    /**
     * @todo: add light code to ignore static content
     */
    $this->initialize_routes();
    if ( $this->has_route( $uri, $method ) ) {
      do_action( 'scaleup_initialize' );
      $this->serve( $method, $uri, $args );
      $continue = false;
    }

    return $continue;
  }

  function initialize_routes() {
    $routes = apply_filters( 'register_route', array() );
    foreach ( $routes as $route ) {
      $url = $route->get_url();
      $this->_routes[ $url ] = $route;
      unset( $route );
    }
  }

  function match_route( $uri ) {
    /**
     * @todo: route matching needs to be improved because this is very primitive
     */
    if ( isset( $this->_routes[ $uri ] ) ) {
      $route = $this->_routes[ $uri ];
      return $route;
    }
    return false;
  }

  /**
   * Return context for a uri.
   * Context is a view associated to specific uri.
   *
   * @param $uri
   * @return bool|ScaleUp_View
   */
  function get_context( $uri ) {
    if ( $context = $this->match_route( $uri ) )
      if ( is_object( $context ) )
        return $context;
    return false;
  }

  /**
   * Return callback to follow
   *
   * @param $uri
   * @param $method
   * @return bool|callable
   */
  function get_route( $uri, $method ) {
    if ( $route = $this->match_route( $uri ) ) {
      if ( is_object( $route ) && method_exists( $route, 'get_callback' ) ) {
        $callback = $route->get_callback( $method );
        if ( is_callable( $callback ) )
          return $callback;
      }
    }
    return false;
  }

  /**
   * @param $uri
   * @param $method
   * @return bool
   */
  function has_route( $uri, $method ) {
    return false !== $this->get_route( $uri, $method );
  }

  function prepare_request() {

    $method = $_SERVER['REQUEST_METHOD'];
    $uri    = $_SERVER['REQUEST_URI'];

    /**
     * @todo: think about this more thoroughly.
     */
    $uri = $this->normalize_uri( $uri );

    $args = array();
    if ( 'GET'  == $method ) $args = $_GET;
    if ( 'POST' == $method ) $args = $_POST;

    return array( $method, $uri, $args );
  }

  function serve( $method, $uri, $args ) {

    $callback = $this->get_route( $uri, $method );
    $context  = $this->get_context( $uri );

    if ( is_callable( $callback ) )
      if ( $context )
        call_user_func( $callback, $args, $context );
      else
        call_user_func( $callback, $args );
    exit;

  }

  /**
   * Make the uri consisent. For example: /hello is converted to /hello/.
   * @param $uri
   * @return mixed
   */
  function normalize_uri( $uri ) {

    if ( "" == pathinfo( $uri, PATHINFO_EXTENSION ) && "/" != substr( $uri, -1 ) )
      $uri .= "/";

    return $uri;
  }

}
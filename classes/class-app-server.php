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

  function get_route( $uri, $method ) {
    /**
     * @todo: route matching needs to be improved because this is very primitive
     */
    if ( isset( $this->_routes[ $uri ] ) ) {
      $route = $this->_routes[ $uri ];
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
    if ( is_callable( $callback ) )
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

  /**
   * Set http status
   *
   * @todo: Need to rethink the whole server thing and how its handled
   * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
   * @param $code
   * @param $message
   */
  static function http_status( $code, $message ) {

    switch( $code ):
      case 200:
        $description = "OK";
        break;
      case 404:
        $description = "Not Found";
    endswitch;

    header( "HTTP/1.1 {$code} {$description}" );
    header( "Content-Type: text/html; charset=UTF-8" );

    if ( 404 == $code ) {
      get_template_part( '404' );
      exit;
    }
  }

}
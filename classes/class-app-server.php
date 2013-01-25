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
      $url                   = $route->get_url();
      $this->_routes[ $url ] = $route;
      unset( $route );
    }
  }

  function match_route( $uri ) {

    $result = false;
    foreach ( $this->_routes as $template => $route ) {
      list( $matched, $args ) = $this->_match( $template, $uri );
      if ( $matched ) {
        $result = array( $route, $args );
        break;
      }
    }

    return $result;
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
   * @param $uri
   * @param $method
   * @return bool
   */
  function has_route( $uri, $method ) {
    return false !== $this->match_route( $uri, $method );
  }

  function prepare_request() {

    $method = $_SERVER[ 'REQUEST_METHOD' ];
    $uri    = $_SERVER[ 'REQUEST_URI' ];

    $args = array();
    if ( 'GET' == $method )
      $args = $_GET;
    if ( 'POST' == $method )
      $args = $_POST;

    return array( $method, $uri, $args );
  }

  function serve( $method, $uri, $args ) {

    $route = $this->match_route( $uri, $method );

    if ( false !== $route ) {

      list( $context, $variables ) = $route;

      /**
       * merge POST/GET arguments with variables from url matching
       * @todo: Is this bad?
       */
      $args = wp_parse_args( $variables, $args );
      $callback = false;

      if ( is_object( $context ) && method_exists( $context, 'get_callback' ) )
        $callback = $context->get_callback( $method );

      if ( is_callable( $callback ) ) {
        if ( $context ) {
          $this->set_context( $context );
          call_user_func( $callback, $args, $context );
        }
        else
          call_user_func( $callback, $args );
      }
      exit;
    }
  }

  /**
   * Set context into global scope to templating
   *
   * @param $context
   */
  function set_context( $context ) {
    if ( 'ScaleUp_View' == get_class( $context ) ) {
      global $in_scaleup_view, $scaleup_view;
      $in_scaleup_view = true;
      $scaleup_view    = $context;
    }
  }

  /**
   * Match template against uri and returns an array with match result and matched variables
   *
   * @param $template
   * @param $uri
   * @return array with 2 elements: true or false of match and array or matched args
   */
  function _match( $template, $uri ) {

    $regex = $this->_build_regexp( $template );
    $output = preg_match( $regex, $uri, $matches );

    $matched  = false;
    $args     = array();

    if ( false == $output ) {
      /**
       * @todo: Should return debug error that there was a problem with regex parsing
       */
    }

    if ( 1 === $output ) {
      // uri was matches, let's get the arguments out of it
      foreach ( $matches as $key => $value ) {
        if ( !is_numeric( $key ) )
          $args[ $key ] = $value;
      }
      $matched = true;
    }

    return array( $matched, $args );
  }

  function _build_regexp( $template ) {
    $pattern   = $template;
    $len       = strlen( $pattern );
    $tokens    = array();
    $variables = array();
    $pos       = 0;
    preg_match_all( '#.\{(\w+)\}#', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER );
    foreach ( $matches as $match ) {
      if ( $text = substr( $pattern, $pos, $match[ 0 ][ 1 ] - $pos ) ) {
        $tokens[ ] = array( 'text', $text );
      }

      $pos = $match[ 0 ][ 1 ] + strlen( $match[ 0 ][ 0 ] );
      $var = $match[ 1 ][ 0 ];

      // Use the character preceding the variable as a separator
      $separators = array( $match[ 0 ][ 0 ][ 0 ] );

      if ( $pos !== $len ) {
        // Use the character following the variable as the separator when available
        $separators[ ] = $pattern[ $pos ];
      }
      $regexp = sprintf( '[^%s]+', preg_quote( implode( '', array_unique( $separators ) ), "#" ) );

      $tokens[ ] = array( 'variable', $match[ 0 ][ 0 ][ 0 ], $regexp, $var );

      if ( in_array( $var, $variables ) ) {
        /**
         * @todo: Add error that variable can't be used twice
         */
      }

      $variables[ ] = $var;
    }

    if ( $pos < $len ) {
      $tokens[ ] = array( 'text', substr( $pattern, $pos ) );
    }

    // compute the matching regexp
    $regexp = '';
    foreach ( $tokens as $token ) {
      if ( 'text' === $token[ 0 ] ) {
        // Text tokens
        $regexp .= preg_quote( $token[ 1 ], "#" );
      } else {
        // Variable tokens
        $regexp .= sprintf( '%s(?P<%s>%s)', preg_quote( $token[ 1 ], "#" ), $token[ 3 ], $token[ 2 ] );
      }
    }

    /**
     * escape the string and
     */
    $regexp = "'$regexp/?$'";
    return $regexp;
  }

}
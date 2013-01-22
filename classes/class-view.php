<?php
class ScaleUp_View {

  protected $_callbacks;

  protected $_url;

  protected $_args;

  function __construct( $url, $callbacks, $args ) {

    $this->_url       = $url;
    $this->_callbacks = wp_parse_args( $callbacks, array( 'GET' => null, 'POST'=> null ) );
    $this->_args      = $args;

    add_filter( 'register_route', array( $this, 'register_route' ) );
  }

  /**
   * Callback function for register_route filter to add this view to routes
   *
   * @param $routes
   * @return array
   */
  function register_route( $routes ) {
    $routes[] = $this;
    return $routes;
  }

  /**
   * Return this view's url
   *
   * @return mixed
   */
  function get_url() {
    return $this->_url;
  }

  /**
   * @param $method
   * @return bool
   */
  function has_callback( $method ) {
    return isset( $this->_callbacks[ $method ] );
  }

  /**
   * @param $method
   * @return bool
   */
  function get_callback( $method ) {
    if ( isset( $this->_callbacks[ $method ] ) )
      return $this->_callbacks[ $method ];
    return false;
  }

}
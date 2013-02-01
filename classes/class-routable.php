<?php
class ScaleUp_Routable extends ScaleUp_Context {

  var $_context;

  var $_name;

  var $_url;

  var $_callbacks;

  function __construct( $args ) {

    parent::__construct( $args );

    add_filter( 'register_route', array( $this, 'register_route' ) );

  }

  /**
   * Callback function for register_route filter to add this view to routes
   *
   * @param $routes
   * @return array
   */
  function register_route( $routes ) {
    if ( ! ( $this->has( 'exclude_route' ) && true == $this->get( 'exclude_route' ) ) ) {
      $routes[] = $this;
    }
    return $routes;
  }

  function get_defaults() {
    return wp_parse_args(
      array(
          'name'      => '',
          'url'       => '',
          'callbacks' => array(),
     ), parent::get_defaults() );
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

  /**
   * Return this view's url. Without $args, this function will return url template for this view.
   *
   * @param null $args
   * @return mixed
   */
  function get_url( $args = null ) {

    $url = '';
    if ( is_null( $this->_context ) ) {
      $url = $this->_url;
    } elseif ( is_object( $this->_context ) && method_exists( $this->_context, 'get_url' ) ) {
      $url = $this->_context->get_url() . $this->_url;
    }

    if ( is_null( $args ) )
      return $url;
    else
      return string_template( $url, $args );

  }

}
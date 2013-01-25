<?php
class ScaleUp_Addon {

  protected $_views;

  protected $_base;

  function __construct( $args, $context = null ) {

    $args = wp_parse_args( $args, $this->get_defaults() );

    $this->_base  = $context;

    foreach ( $args as $property => $value ){
      $this->set( $property, $value );
      unset( $value );
    }

    $this->_views = new ScaleUp_Views( $this );

    $this->initialize();
  }

  function initialize() {

  }

  function get_defaults() {
    return array(
      'url' => '',
    );
  }

  function get_views() {
    return $this->_views;
  }

  function set_views( $views ) {
    $this->_views = $views;
  }

  function get_url() {
    if ( is_object( $this->_base ) && method_exists( $this->_base, 'get_url' ) ) {
      return $this->_base->get_url() . $this->_url;
    } else {
      return $this->_base . $this->get( 'url' );
    }
  }

  /**
   * Return a field attribute
   *
   * @param $name
   * @return mixed|null
   */
  function get( $name ) {

    $method_name = "get_$name";
    if ( method_exists( $this, $method_name ) )
      return $this->$method_name( $name );

    $property_name = "_$name";
    if ( property_exists( $this, $property_name ) ) {
      return $this->$property_name;
    }

    return null;
  }

  /**
   * Set a field attribute
   *
   * @param $name
   * @param $value
   */
  function set( $name, $value ) {

    $method_name = "set_$name";
    if ( method_exists( $this, $method_name ) )
      $this->$method_name( $name, $value );

    $property_name = "_$name";
    $this->$property_name = $value;
  }

}
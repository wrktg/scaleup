<?php
class ScaleUp_Base {

  protected static $_initialized = false;

  function __construct( $args = array() ) {

    $args = wp_parse_args( $args, $this->get_defaults() );

    $this->load( $args );
    $this->set( 'args', $args );

    $this->initialize();

  }

  function get_defaults() {
    return array();
  }

  /**
   * Overload this function in child class to execute code once during instantiation of this object.
   * This is a good place to execute register_* functions and hook to filters and actions.
   */
  function initialize(){
    // overload this function in child class
  }

  function load( $args ) {
    foreach ( $args as $property => $value ) {
      $this->set( $property, $value );
      unset( $value );
    }
  }

  /**
   * Return a property value
   *
   * @param $name
   * @return mixed|null
   */
  function get( $name ) {

    $method_name = "get_$name";
    if ( method_exists( $this, $method_name ) )
      return $this->$method_name();

    $property_name = "_$name";
    if ( property_exists( $this, $property_name ) ) {
      return $this->$property_name;
    }

    return null;
  }

  /**
   * Set a property value
   *
   * @param $name
   * @param $value
   */
  function set( $name, $value ) {

    $method_name = "set_$name";
    if ( method_exists( $this, $method_name ) ) {
      $this->$method_name( $value );
      return;
    }

    $property_name = "_$name";
    $this->$property_name = $value;
  }

  function has( $name ) {
    $property_name = "_$name";
    return isset( $this->$property_name );
  }

}
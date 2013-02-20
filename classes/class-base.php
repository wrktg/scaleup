<?php
class ScaleUp_Base extends stdClass {

  function __construct( $args = array() ) {

    $args = wp_parse_args( $args, $this->get_defaults() );

    foreach ( $args as $key => $value ) {
      $this->set( $key, $value );
      unset( $value );
    }
    $this->set( '_args', $args );

  }

  function get_defaults() {
    return array();
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
    $property_name = "_$name";

    $this->$property_name = $value;
  }

  function has( $name ) {
    $property_name = "_$name";

    return isset( $this->$property_name );
  }

  /**
   * Return an array of publicly accessible properties for this object
   */
  function get_properties() {
    $return = array();

    $properties = get_object_vars( $this );
    foreach ( $properties as $property => $value ) {
      if ( preg_match( '/^[_]([^_][a-zA-Z0-9_\x7f-\xff]*)$/', $property, $matches ) ) {
        $return[] = $matches[1];
      }
      unset( $value );
    }

    return $return;
  }

}
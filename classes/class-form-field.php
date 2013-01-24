<?php
class ScaleUp_Form_Field {

  function __construct( $args ) {

    $default = array(
      'type'          => 'text',
      'value'         => '',
      'help'          => null,
      'label'         => null,
      'before_field'  => '',
      'after_field'   => '',
      'placeholder'   => '',
      'class'         => '',
    );

    $args = wp_parse_args( $args, $default );

    /**
     * In case user provides only id but not name or the other way around
     */
    if ( !isset( $args[ 'name' ] ) && isset( $args[ 'id' ] ) )
      $args[ 'name' ] = $args[ 'id' ];

    if ( !isset( $args[ 'id' ] ) && isset( $args[ 'name' ] ) )
      $args[ 'id' ] = $args[ 'name' ];

    foreach ( $args as $property => $value ) {
      $this->set( $property, $value );
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
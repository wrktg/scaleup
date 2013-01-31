<?php
class ScaleUp_Schema_Property extends ScaleUp_Base {

  function __construct( $property_name, $args = array() ) {

    $default = array();

    $schema_property = get_property_reference( $property_name );
    if ( is_array( $schema_property ) )
      $default = $schema_property;
    else
      $this->_error = new WP_Error( 'instantiation', sprintf( __( '%s schema property does not exist.' ), $property_name ) );

    $default[ 'name' ]      = $property_name;
    $default[ 'meta_type' ] = 'post';

    $args = wp_parse_args( $args, $default );

    parent::__construct( $args );
  }

  /**
   * Return value in this property
   *
   * @return bool|float|int|null
   */
  function get_value() {

    if ( !isset( $this->_value ) )
      return null;
    else
      $value = $this->_value;

    $ranges = $this->get( 'ranges' );
    if ( in_array( "Integer", $ranges ) )
      return (int) $value;

    if ( in_array( "Boolean", $ranges ) )
      return (bool) $value;

    if ( in_array( "Float", $ranges ) )
    return (float) $value;

    return $value;
  }

  /**
   * Update schema property of a specific object
   *
   * @param $object_id
   * @return bool
   */
  function update( $object_id ) {
    /**
     * @todo: validate input before saving
     */
    return update_metadata( $this->get( 'meta_type' ), $object_id, $this->get( 'name' ), $this->get( 'value' ) );
  }

  /**
   * Read value into this property
   *
   * @param $object_id
   */
  function read( $object_id ) {
    $meta_type  = $this->get( 'meta_type' );
    $meta_key   = $this->get( 'name' );
    $value = get_metadata( $meta_type, $object_id, $meta_key, true );
    $this->set( 'value', $value );
  }

}
<?php
class ScaleUp_Schema_Property extends ScaleUp_Base {

  function __construct( $property_name, $args = array() ) {

    $default = array(
      'name'      => $property_name,
      'meta_type' => 'post',
      'custom'    => false,
    );

    $args = wp_parse_args( $args, $default );

    if ( !$args[ 'custom' ] ) {
      $property_defintion = get_property_reference( $property_name );
      if ( !is_array( $property_defintion ) ) {
        $this->_error = new WP_Error( 'instantiation', sprintf( __( '%s schema property does not exist.' ), $property_name ) );
      }
      $args =  wp_parse_args( $args, $property_defintion );
    }

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
   * Return value of $this->_meta_key if it was set via $args, otherwise return $this->_name;
   * @return mixed|null
   */
  function get_meta_key() {
    if ( $this->has( 'meta_key' ) ) {
      return $this->_meta_key;
    } else {
      return $this->get( 'name' );
    }
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
    return update_metadata( $this->get( 'meta_type' ), $object_id, $this->get_meta_key(), $this->get( 'value' ) );
  }

  /**
   * Read value into this property
   *
   * @param $object_id
   */
  function read( $object_id ) {
    $meta_type  = $this->get( 'meta_type' );
    $meta_key   = $this->get_meta_key();
    $value = get_metadata( $meta_type, $object_id, $meta_key, true );
    $this->set( 'value', $value );
  }

}
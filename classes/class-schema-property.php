<?php
class ScaleUp_Schema_Property extends ScaleUp_Base {

  function __construct( $property_name, $args = null ) {

    $default = array(
      'name'        => $property_name,
      'meta_type'   => 'post',
      'data_type'   => 'Text',
    );

    $args = wp_parse_args( $args, $default );

    parent::__construct( $args );
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

}
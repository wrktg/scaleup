<?php
class ScaleUp_Item extends ScaleUp_Feature {

  function init() {
    $this->add_action( 'activation', array( $this, 'activation' ) );
  }

  function activation() {
    $this->add( 'property', array(
      'name'  => 'schemas',
      'value' => $this->get_schemas(),
    ));
  }

  /**
   * Create item with values from $args array
   *
   * @param   array $args
   * @return  array $args
   */
  function create( $args = array() ) {
    $args = $this->apply_filters( 'create', $args );
    if ( isset( $args[ 'ID' ][ 'value' ] ) ) {
      $this->set( 'ID', $args[ 'ID' ][ 'value' ] );
    }
    return $args;
  }

  /**
   * Read item
   *
   * @param   array $args
   * @return  array
   */
  function read( $args ) {
    $args = $this->apply_filters( 'read', $args );
    return $args;
  }

  /**
   * Update item with values from $args array
   *
   * @param array $args
   * @return array $args
   */
  function update( $args ) {
    $args = $this->apply_filters( 'update', $args );
    return $args;
  }

  /**
   * Delete item with $id.
   * Set $force_delete to true if you want the item be deleted without going into the Trash.
   *
   * @param   array $args
   * @return  array
   */
  function delete( $args ) {
    $args = $this->apply_filters( 'delete', $args );
    return $args;
  }

  /**
   * Callback function to be used when hooking to forms
   *
   * @param array $args
   * @return array $args
   */
  function store( $args ) {

    if ( isset( $args[ 'ID' ][ 'value' ] ) && $args[ 'ID' ][ 'value' ] ) {
      $args = $this->update( $args );
    } else {
      $args = $this->create( $args );
    }

    return $args;
  }

  /**
   * Return schemas for this item
   *
   * @return array
   */
  function get_schemas() {
    $schemas = $this->get_features( 'schemas' );
    return array_keys( $schemas );
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'item',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'item', array(
  '__CLASS__'     => 'ScaleUp_Item',
  '_plural'       => 'items',
  '_supports'     => array( 'schemas', 'properties' ),
  '_duck_types'   => array( 'global' ),
) );
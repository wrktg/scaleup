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
   * @param $args array
   */
  function create( $args = array() ) {
    $this->do_action( 'create', $args );
    /**
     * @todo: check if errors occured and return
     */
  }

  /**
   * Read item from id
   *
   * @param $id int
   */
  function read( $id ) {
    $this->set( 'id', $id );
    $this->do_action( 'read', $id );
  }

  /**
   * Update item with values from $args array
   *
   * @param $args
   */
  function update( $args ) {
    if ( !isset( $args[ 'ID' ] ) && !is_null( $this->get( 'id' ) ) ) {
      $args[ 'ID' ] = $this->get( 'id' );
    }
    $this->do_action( 'update', $args );
    /**
     * @todo: check if errors occured and return
     */
  }

  /**
   * Delete item with $id.
   * Set $force_delete to true if you want the item be deleted without going into the Trash.
   *
   * @param $id int
   * @param bool $force_delete
   */
  function delete( $id, $force_delete = false ) {
    $this->set( 'id', $id );
    $this->do_action( 'delete', array( 'id' => $id, 'force_delete' => $force_delete ) );
  }

  /**
   * Callback function to be used when hooking to forms
   *
   * @param $caller
   * @param $args
   */
  function store( $caller, $args ) {

    $id = null;

    $this->set( 'caller', $caller );
    if ( isset( $args[ 'id' ] ) ) {
      $id = $args[ 'id' ];
    }
    if ( isset( $args[ 'ID' ] ) ) {
      $id = $args[ 'ID' ];
    }

    $id = (int) $id;

    if ( 0 == $id ) {
      $this->create( $args );
    } elseif ( $id > 0 ) {
      $args[ 'ID' ] = $id;
      $this->update( $args );
    }

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
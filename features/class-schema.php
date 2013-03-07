<?php
class ScaleUp_Schema extends ScaleUp_Feature {

  var $_error = false;

  function activation() {
    /** @var $context ScaleUp_Item */
    $context = $this->get( 'context' );
    $context->add_action( 'create', array( $this, 'on_item_create' ) );
    $context->add_action( 'read',   array( $this, 'on_item_read' ) );
    $context->add_action( 'update', array( $this, 'on_item_update' ) );
    $context->add_action( 'delete', array( $this, 'on_item_delete' ) );
  }

  /**
   * Execute create action which causes all properties, taxonomies and relationships to execute create
   *
   * @param $item ScaleUp_Item
   * @param array $args
   * @return bool
   */
  function on_item_create( $item, $args = array() ) {
    $id = (int) $item->get( 'id' );
    if ( 0 < $id ) {
      $this->set( 'error', false );   // reset error flag
      $args[ 'item' ] = $item;
      $args[ 'id' ]   = $id;
      $this->do_action( 'create', $args );
    }
    return $this->get( 'error' );
  }

  /**
   * Execute read action which causes all properties, taxonomies and relationships to load their values
   *
   * @param $item ScaleUp_Item
   * @param $args array
   * @return bool
   */
  function on_item_read( $item, $args = array() ) {
    $id = (int) $item->get( 'id' );
    if ( 0 < $id ) {
      $this->set( 'error', false );   // reset error flag
      $args[ 'item' ] = $item;
      $args[ 'id' ]   = $id;
      $this->do_action( 'read', $id );
    }
    return $this->get( 'error' );
  }

  /**
   * Execute update action which causes all properties, taxonomies and relationships to check if they have a value to update
   *
   * @param $item ScaleUp_Item
   * @param $args
   * @return mixed|null
   */
  function on_item_update( $item, $args ) {
    $id = (int) $item->get( 'id' );
    if ( 0 < $id ) {
      $this->set( 'error', false );   // reset error flag
      $args[ 'item' ] = $item;
      $args[ 'id' ]   = $id;
      $this->do_action( 'update', $args );
    }
    return $this->get( 'error' );
  }

  /**
   * Execute delete action
   * @param $item ScaleUp_Item
   * @param $args
   * @return mixed|null
   */
  function on_item_delete( $item, $args ) {
    $id = (int) $item->get( 'id' );
    if ( 0 < $id ) {
      $this->set( 'error', false );   // reset error flag
      $args[ 'item' ] = $item;
      $args[ 'id' ]   = $id;
      $this->do_action( 'delete', $args );
    }
    return $this->get( 'error' );
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        'error' => false,
        '_feature_type' => 'schema',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'schema', array(
  '__CLASS__'     => 'ScaleUp_Schema',
  '_plural'       => 'schemas',
  '_supports'     => array( 'properties', 'taxonomies', 'relationships' ),
  '_duck_types'   => array( 'global', 'contextual' ),
) );
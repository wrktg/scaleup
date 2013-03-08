<?php
class ScaleUp_Schema extends ScaleUp_Feature {

  var $_error = false;

  function activation() {
    /** @var $context ScaleUp_Item */
    $item = $this->get( 'context' );
    $item->add_action( 'create', array( $this, 'on_item_create' ) );
    $item->add_action( 'read',   array( $this, 'on_item_read' ) );
    $item->add_action( 'update', array( $this, 'on_item_update' ) );
    $item->add_action( 'delete', array( $this, 'on_item_delete' ) );
  }

  /**
   * Extract id from args and setup this schema
   *
   * @param $args
   * @return bool
   */
  function setup( $args ) {
    $successful = true;

    $id = null;

    if ( !$this->has( 'item_id' ) || is_null( $this->has( 'item_id' ) ) ) {
      if ( isset( $args[ 'id' ] ) ) {
        $id = (int)$args[ 'id' ];
      } elseif ( isset( $args[ 'ID' ] ) ) {
        $id = (int)$args[ 'ID' ];
      } else {
        $successful = false;
      }
    }

    if ( !is_null( $id ) && $id > 0 ) {
      $this->set( 'item_id', $id );
    } else {
      $successful = false;
    }

    return $successful;
  }

  /**
   * Execute create action which causes all properties, taxonomies and relationships to execute create
   *
   * @param $item ScaleUp_Item
   * @param array $args
   */
  function on_item_create( $item, $args = array() ) {
    $post_type  = $this->get( 'post_type' );
    if ( $this->setup( $args ) ) {
      $id = $item->get( 'item_id' );
      $this->set( 'error', false );   // reset error flag
      $args[ 'item' ] = $item;
      if ( $post_type ) {
        set_post_type( $id, $post_type );
      }
      $this->do_action( 'on_item_create', $args );
    }
  }

  /**
   * Execute read action which causes all properties, taxonomies and relationships to load their values
   *
   * @param $item ScaleUp_Item
   * @param $args array
   * @return bool
   */
  function on_item_read( $item, $args = array() ) {
    if ( $this->setup( $args ) ) {
      $this->set( 'error', false );   // reset error flag
      $args[ 'item' ] = $item;
      $this->do_action( 'on_item_read', $args );
    }
  }

  /**
   * Execute update action which causes all properties, taxonomies and relationships to check if they have a value to update
   *
   * @param $item ScaleUp_Item
   * @param $args
   * @return mixed|null
   */
  function on_item_update( $item, $args ) {
    if ( $this->setup( $args ) ) {
      $this->set( 'error', false );   // reset error flag
      $args[ 'item' ] = $item;
      $this->do_action( 'on_item_update', $args );
    }
  }

  /**
   * Execute delete action
   * @param $item ScaleUp_Item
   * @param $args
   * @return mixed|null
   */
  function on_item_delete( $item, $args ) {
    if ( $this->setup( $args ) ) {
      $this->set( 'error', false );   // reset error flag
      $args[ 'item' ] = $item;
      $this->do_action( 'on_item_delete', $args );
    }
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
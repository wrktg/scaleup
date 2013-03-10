<?php
class ScaleUp_Schema extends ScaleUp_Feature {

  var $_error = false;

  function activation() {
    /** @var $context ScaleUp_Item */
    $item = $this->get( 'context' );
    $item->add_filter( 'create', array( $this, 'on_item_create' ) );
    $item->add_filter( 'read',   array( $this, 'on_item_read' ) );
    $item->add_filter( 'update', array( $this, 'on_item_update' ) );
    $item->add_filter( 'delete', array( $this, 'on_item_delete' ) );
  }

  /**
   * Extract ID from args and setup this schema
   *
   * @param $args
   * @return bool
   */
  function setup( $args ) {
    $successful = false;

    if ( isset( $args[ 'ID' ][ 'value' ] ) ) {
      $this->set( 'ID', $args[ 'ID' ][ 'value' ] );
      $this->set( 'error', false );   // reset error flag
      $successful = true;
    }

    return $successful;
  }

  /**
   * Execute create action which causes all properties, taxonomies and relationships to execute create
   *
   * @param   array $args
   * @return  array
   */
  function on_item_create( $args ) {
    if ( $this->setup( $args ) ) {
      $ID         = $this->get( 'ID' );
      $post_type  = $this->get( 'post_type' );
      if ( $post_type ) {
        set_post_type( $ID, $post_type );
      }
      $args = $this->apply_filters( 'on_item_create', $args );
    }
    return $args;
  }

  /**
   * Execute read action which causes all properties, taxonomies and relationships to load their values
   *
   * @param array $args
   * @return array
   */
  function on_item_read( $args ) {
    if ( $this->setup( $args ) ) {
      $args = $this->apply_filters( 'on_item_read', $args );
    }
    return $args;
  }

  /**
   * Execute update action which causes all properties, taxonomies and relationships to check if they have a value to update
   *
   * @param array $args
   * @return array
   */
  function on_item_update( $args ) {
    if ( $this->setup( $args ) ) {
      $args = $this->apply_filters( 'on_item_update', $args );
    }
    return $args;
  }

  /**
   * Execute delete action
   *
   * @param array $args
   * @return array
   */
  function on_item_delete( $args ) {
    if ( $this->setup( $args ) ) {
      $args = $this->apply_filters( 'on_item_delete', $args );
    }
    return $args;
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
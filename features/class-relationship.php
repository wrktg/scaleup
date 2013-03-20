<?php
class ScaleUp_Relationship extends ScaleUp_Feature {

  var $_error = false;

  function activation() {
    $context = $this->get( 'context' );
    $context->add_action( 'create', array( $this, 'create' ) );
    $context->add_action( 'read',   array( $this, 'read' ) );
    $context->add_action( 'update', array( $this, 'update' ) );
    $context->add_action( 'delete', array( $this, 'delete' ) );
  }

  function setup( $args ) {

    $name = $this->get( 'name' );
    if ( isset( $args[ 'ID' ][ 'value' ] ) ) {
      $this->set( 'ID', $args[ 'ID' ][ 'value' ] );
    }
    if ( isset( $args[ $name ][ 'value' ] ) ) {
      $this->set( 'value', $args[ $name ][ 'value' ] );
    }

    $setup = !is_null( $this->get( 'ID' ) ) && !is_null( $this->get( 'value' ) );

    return $setup;
  }

  function create( $args ) {
    if ( $this->setup( $args ) ) {
      $name       = $this->get( 'name' );
      $type       = $this->get( 'type' );
      $value      = $this->get( 'value' );
      $ID         = $this->get( 'ID' );
      $meta_type  = $this->get( 'meta_type' );
      $meta_key   = $this->get( 'meta_key' );
      if ( 'meta' == $type ) {
        $id = add_metadata( $meta_type, $ID, $meta_key, $value );
      }
      $this->set( 'id', $id );
      $args[ $name ][ 'id' ] = $id;
    }
    return $args;
  }

  function read( $args ) {
    /**
     * @todo: implement relationship read
     */
  }

  function update( $args ) {
    if ( $this->setup( $args ) ) {
      $name       = $this->get( 'name' );
      $type       = $this->get( 'type' );
      $value      = $this->get( 'value' );
      $ID         = $this->get( 'ID' );
      $meta_type  = $this->get( 'meta_type' );
      $meta_key   = $this->get( 'meta_key' );
      $many       = $this->get( 'many' );
      if ( 'meta' == $type ) {
        $id = add_metadata( $meta_type, $ID, $meta_key, $value, !$many );
        if ( false === $id ) {
          ScaleUp::add_alert( array(
            'type'  => 'warning',
            'msg'   => "Could not update metadata $name for item $ID",
            'debug' => true
          ));
        }
      }
      $this->set( 'id', $id );
      $args[ $name ][ 'id' ] = $id;
    }
    return $args;
  }

  function delete( $args ) {
    /**
     * @todo: implement relationship delete
     */
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        'type'          => 'meta',
        'meta_type'     => 'post',
        'many'          => true,
        '_feature_type' => 'relationship',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'relationship', array(
  '__CLASS__' => 'ScaleUp_Relationship',
  '_plural'   => 'relationships',
  '_duck_types'   => array( 'global', 'contextual' ),
) );
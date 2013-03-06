<?php
class ScaleUp_Property extends ScaleUp_Feature {

  var $_error = false;

  function activation() {
    $context = $this->get( 'context' );
    $context->add_action( 'create', array( $this, 'setup' ) );
    $context->add_action( 'create', array( $this, 'create' ) );
    $context->add_action( 'read',   array( $this, 'setup' ) );
    $context->add_action( 'read',   array( $this, 'read' ) );
    $context->add_action( 'update', array( $this, 'setup' ) );
    $context->add_action( 'update', array( $this, 'update' ) );
  }

  function setup( $schema, $args = array() ) {
    if ( !$this->has( 'id' ) || is_null( $this->has( 'id' ) ) ) {
      $id = (int) $args[ 'id' ];
      $this->set( 'id', $id );
    }
  }

  /**
   * Create the value of this property when the value for this property is passed in args array.
   *
   * @param $schema ScaleUp_Schema
   * @param $args array
   * @return bool
   */
  function create( $schema, $args = array() ) {
    $id = $this->get( 'id' );
    $name = $this->get( 'name' );
    if ( isset( $args[ $name ] ) ) {
      $value = $args[ $name ];
      add_metadata( $this->get( 'meta_type' ), $id, $this->get_meta_key(), $value );
    }
  }

  /**
   * Read the value from the database and store it in this object
   *
   * @param $schema ScaleUp_Schema
   * @param array $args
   * @return array|string
   */
  function read( $schema, $args = array() ) {
    $id = $this->get( 'id' );
    $value = get_metadata( $this->get( 'meta_type' ), $id, $this->get_meta_key() );
    $this->get( 'value', $value );
    return $value;
  }

  /**
   * Update the value of this property. The new value is taken from the $args array.
   * If value is null then this method will remove the current value of the metadata.
   *
   * @param $schema ScaleUp_Schema
   * @param array $args
   */
  function update( $schema, $args = array() ) {
    $id = $this->get( 'id' );
    if ( isset( $args[ $this->get( 'name' ) ] ) ) {
      $value = $args[ $this->get( 'name' ) ];
      if ( is_null( $value ) ) {
        delete_metadata( $this->get( 'meta_type' ), $id, $this->get_meta_key() );
      } else {
        update_metadata( $this->get( 'meta_type' ), $id, $this->get_meta_key(), $value );
      }
      $this->set( 'value', $value );
    }
  }

  /**
   * Return meta_key if it was set, otherwise return name
   *
   * @return mixed
   */
  function get_meta_key() {
    $meta_key = $this->get( 'name' );
    if ( $this->has( 'meta_key' ) ) {
      $meta_key = $this->get( 'meta_key' );
    }
    return $meta_key;
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        'meta_type'     => 'post',
        '_feature_type' => 'template',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'property', array(
  '__CLASS__' => 'ScaleUp_Property',
  '_plural'   => 'properties',
  '_duck_types'   => array( 'global', 'contextual' ),
) );
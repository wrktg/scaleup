<?php
class ScaleUp_Property extends ScaleUp_Feature {

  var $_error = false;

  function activation() {
    $schema = $this->get( 'context' );
    $schema->add_action( 'create', array( $this, 'create' ) );
    $schema->add_action( 'read',   array( $this, 'read' ) );
    $schema->add_action( 'update', array( $this, 'update' ) );
  }

  /**
   *
   * @param array $args
   * @return bool
   */
  function setup( $args = array() ) {
    $successful = false;

    if ( isset( $args[ 'id' ] ) && $args[ 'id' ] > 0 ) {
      $this->set( 'item_id', $args[ 'id' ] );
      $successful = true;
    }

    return $successful;
  }

  /**
   * Create the value of this property when the value for this property is passed in args array.
   *
   * @param $schema ScaleUp_Schema
   * @param $args array
   */
  function create( $schema, $args = array() ) {
    if ( $this->setup( $args ) ) {
      $item_id    = $this->get( 'item_id' );
      $name       = $this->get( 'name' );
      $meta_key   = $this->get_meta_key();
      $meta_type  = $this->get( 'meta_type' );
      if ( isset( $args[ $name ] ) ) {
        $value = $args[ $name ];
        $successful = add_metadata( $meta_type, $item_id, $meta_key, $value );
        if ( !$successful ) {
          $this->add( 'alert',
            array(
              'type'  => 'warning',
              'msg'   => "Failed to update $meta_type meta $meta_key with $value",
              'debug' => true,
            )
          );
        }
      }
    }
  }

  /**
   * Read the value from the database and store it in this object
   *
   * @param $schema ScaleUp_Schema
   * @param array $args
   */
  function read( $schema, $args = array() ) {
    $item_id    = $this->get( 'item_id' );
    $meta_type  = $this->get( 'meta_type' );
    $meta_key   = $this->get_meta_key();
    $value = get_metadata( $meta_type, $item_id, $meta_key );
    $this->get( 'value', $value );
  }

  /**
   * Update the value of this property. The new value is taken from the $args array.
   * If value is null then this method will remove the current value of the metadata.
   *
   * @param $schema ScaleUp_Schema
   * @param array $args
   */
  function update( $schema, $args = array() ) {
    $item_id    = $this->get( 'item_id' );
    $meta_type  = $this->get( 'meta_type' );
    $meta_key   = $this->get_meta_key();
    $name       = $this->get( 'name' );
    if ( $this->setup( $args ) ) {
      if ( isset( $args[ $name ] ) ) {
        $value = $args[ $this->get( 'name' ) ];
        if ( is_null( $value ) ) {
          delete_metadata( $meta_type, $item_id, $meta_key );
        } else {
          update_metadata( $meta_type, $item_id, $meta_key, $value );
        }
        $this->set( 'value', $value );
      }
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
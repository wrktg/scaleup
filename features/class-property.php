<?php
class ScaleUp_Property extends ScaleUp_Feature {

  var $_error = false;

  function activation() {
    $context = $this->get( 'context' );
    /**
     * @todo: this kind of thing should not happen. refactor how events happen so there aren't these kinds of exceptions.
     */
    switch ( $context->get( '_feature_type' ) ) :
      case 'schema':
        $context->add_action( 'on_item_create', array( $this, 'create' ) );
        $context->add_action( 'on_item_read', array( $this, 'read' ) );
        $context->add_action( 'on_item_update', array( $this, 'update' ) );
        break;
      case 'item':
        $context->add_action( 'create', array( $this, 'create' ) );
        break;
    endswitch;
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
   * @param $feature ScaleUp_Feature
   * @param $args array
   */
  function create( $feature, $args = array() ) {
    $item_id   = null;
    $name      = $this->get( 'name' );
    $meta_key  = $this->get_meta_key();
    $meta_type = $this->get( 'meta_type' );
    if ( $this->setup( $args ) ) {
      /**
       * This property is a schema property
       */
      $item_id = $this->get( 'item_id' );
      if ( isset( $args[ $name ] ) ) {
        $value = $args[ $name ];
      }
    } else {
      /**
       * This property is a item property
       */
      if ( $feature->get( 'id' ) && $this->get( 'value' ) ) {
        $item_id = $feature->get( 'id' );
      }
      $value = $this->get( 'value' );
    }
    if ( !is_null( $item_id ) ) {
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

  /**
   * Read the value from the database and store it in this object
   *
   * @param $schema ScaleUp_Schema
   * @param array $args
   */
  function read( $schema, $args = array() ) {
    $item_id   = $this->get( 'item_id' );
    $meta_type = $this->get( 'meta_type' );
    $meta_key  = $this->get_meta_key();
    $value     = get_metadata( $meta_type, $item_id, $meta_key );
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
    $item_id   = $this->get( 'item_id' );
    $meta_type = $this->get( 'meta_type' );
    $meta_key  = $this->get_meta_key();
    $name      = $this->get( 'name' );
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
  '__CLASS__'   => 'ScaleUp_Property',
  '_plural'     => 'properties',
  '_duck_types' => array( 'global', 'contextual' ),
) );
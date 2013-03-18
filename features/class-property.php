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
        $context->add_action( 'on_item_read',   array( $this, 'read' ) );
        $context->add_action( 'on_item_update', array( $this, 'update' ) );
        break;
      case 'item':
        $context->add_action( 'create', array( $this, 'create' ) );
        $context->add_action( 'update', array( $this, 'update' ) );
        break;
    endswitch;
  }

  /**
   * Set post ID from args
   *
   * @param array $args
   * @return bool
   */
  function setup( $args ) {
    $successful = false;

    if ( isset( $args[ 'ID' ][ 'value' ] ) ) {
      $this->set( 'ID', $args[ 'ID' ][ 'value' ] );
      $name = $this->get( 'name' );
      if ( isset( $args[ $name ][ 'value' ] ) ) {
        $this->set( 'value', $args[ $name ][ 'value' ] );
      }
      $successful = true;
    }


    return $successful;
  }

  /**
   * Create the value of this property when the value for this property is passed in args array.
   *
   * @param   array $args
   * @return  array
   */
  function create( $args ) {

    $meta_key  = $this->get_meta_key();
    $meta_type = $this->get( 'meta_type' );
    if ( $this->setup( $args ) ) {
      $ID     = $this->get( 'ID' );
      $value  = $this->get( 'value' );
      $unique = $this->get( 'unique' );
      $id = add_metadata( $meta_type, $ID, $meta_key, $value, $unique );
      if ( $id ) {
        $name = $this->get( 'name' );
        $args[ $name ][ 'id' ] = $id;
      } else {
        $this->add( 'alert',
          array(
            'type'  => 'warning',
            'msg'   => "Failed to update $meta_type meta $meta_key with $value",
            'debug' => true,
          )
        );
      }
    }

    return $args;
  }

  /**
   * Read the value from the database and store it in this object
   *
   * @param   array $args
   * @return  array
   */
  function read( $args ) {
    if ( $this->setup( $args ) ) {
      $ID        = $this->get( 'ID' );
      $meta_type = $this->get( 'meta_type' );
      $meta_key  = $this->get_meta_key();
      $single    = $this->get( 'single' );
      $value     = get_metadata( $meta_type, $ID, $meta_key, $single );
      $this->get( 'value', $value );

      $name      = $this->get( 'name' );
      $args[ $name ][ 'value' ] = $value;
    }
    return $args;
  }

  /**
   * Update the value of this property. The new value is taken from the $args array.
   * If value is null then this method will remove the current value of the metadata.
   *
   * @param   array $args
   * @return  array
   */
  function update( $args ) {

    if ( $this->setup( $args ) ) {
      $ID        = $this->get( 'ID' );
      $meta_type = $this->get( 'meta_type' );
      $meta_key  = $this->get_meta_key();
      $value     = $this->get( 'value' );
      if ( !is_null( $value ) ) {
        $updated   = update_metadata( $meta_type, $ID, $meta_key, $value );

        $name      = $this->get( 'name' );
        $args[ $name ][ 'updated' ] = $updated;
      }
    }

    return $args;
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
        'unique'        => false,
        'single'        => false,
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
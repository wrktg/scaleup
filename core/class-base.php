<?php
class ScaleUp_Base extends stdClass {

  function __construct( $args = array() ) {

    $args = wp_parse_args( $args, $this->get_defaults() );

    foreach ( $args as $key => $value ) {
      $this->set( $key, $value );
      unset( $value );
    }
    $this->set( '_args', $args );

  }

  function get_defaults() {
    return array();
  }

  function load( $args ) {
    foreach ( $args as $property => $value ) {
      $this->set( $property, $value );
      unset( $value );
    }
  }

  /**
   * Return a property value
   *
   * @param $name
   * @return mixed|null
   */
  function get( $name ) {
    $property_name = "_$name";
    if ( property_exists( $this, $property_name ) ) {
      return $this->$property_name;
    }

    return null;
  }

  /**
   * Set a property value
   *
   * @param $name
   * @param $value
   */
  function set( $name, $value ) {
    $property_name = "_$name";

    if ( property_exists( $this, $property_name ) ) {
      $old = $this->$property_name;
    } else {
      $old = null;
    }

    $this->$property_name = $value;
    $this->do_action( "set_{$name}", array( 'old' => $old, 'new' => $value ) );
  }

  function has( $name ) {
    $property_name = "_$name";

    return isset( $this->$property_name );
  }

  function __unset( $name ) {
    $property_name = "_$name";
    if ( isset( $this->$name ) ) {
      unset( $this->$property_name );
    }
  }

  function __isset( $name ) {
    $property_name = "_$name";
    return property_exists( $this, $property_name );
  }

  /**
   * Return an array of publicly accessible properties for this object
   */
  function get_properties() {
    $return = array();

    $properties = get_object_vars( $this );
    foreach ( $properties as $property => $value ) {
      if ( preg_match( '/^[_]([^_][a-zA-Z0-9_\x7f-\xff]*)$/', $property, $matches ) ) {
        $return[] = $matches[1];
      }
      unset( $value );
    }

    return $return;
  }

  /**
   * Add action to this object
   *
   * @param $handle
   * @param null $callback
   * @param int $priority
   */
  function add_action( $handle, $callback, $priority = 10 ) {
    $object_hash = spl_object_hash( $this );
    add_action( "{$object_hash}->{$handle}", $callback, $priority, 2 );
  }

  /**
   * Remove callback from this object
   *
   * @param $handle
   * @param $callback
   * @param int $priority
   */
  function remove_action( $handle, $callback, $priority = 10 ) {
    $object_hash = spl_object_hash( $this );
    remove_action( "{$object_hash}->{$handle}", $callback, $priority, 2 );
  }

  /**
   * Do action associated with this object
   *
   * @param $handle
   * @param null $args
   */
  function do_action( $handle, $args = null ) {
    $object_hash = spl_object_hash( $this );
    do_action( "{$object_hash}->{$handle}", $this, $args );
  }

  /**
   * Add filter to this object
   *
   * @param string $handle
   * @param null $callback
   * @param int $priority
   * @param int $accepted_args
   */
  function add_filter( $handle, $callback = null, $priority = 10, $accepted_args = 1 ) {
    /**
     * @todo: remove this
     */
    if ( is_null( $callback ) ) {
      $callback = array( $this, $handle );
    }
    $object_hash = spl_object_hash( $this );
    add_filter( "{$object_hash}->{$handle}", $callback, $priority, $accepted_args );
  }

  /**
   * Remove filter for this object
   *
   * @param $handle
   * @param $callback
   * @param int $priority
   */
  function remove_filter( $handle, $callback, $priority = 10 ) {
    $object_hash = spl_object_hash( $this );
    remove_filter( "{$object_hash}->{$handle}", $callback, $priority );
  }

  function apply_filters( $handle, $value ) {
    $object_hash = spl_object_hash( $this );
    return apply_filters( "{$object_hash}->{$handle}", $value );
  }

}
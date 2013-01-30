<?php
/**
 * Provides access and acts as repository for available schemas
 */
class ScaleUp_Schemas {

  protected static $_this;

  protected static $_schemas;

  protected static $_registered_schemas;

  const STORAGE_TRANSIENT = 'scaleup_schemas_storage';

  function __construct( $args = null ) {

    if ( isset( self::$_this ) )
      wp_die( sprintf( __( '%s is a singleton class and you cannot create a second instance.',
        'scaleup' ), get_class( $this ) ) );

    self::$_this = $this;
    $this->initialize();

    self::$_registered_schemas = new ScaleUp_Base();

  }

  /**
   * Initialize ScaleUp_Schemas should only be called once during a request cycle
   */
  function initialize() {
    if ( false === ( $schemas = get_transient( self::STORAGE_TRANSIENT ) ) ) {
      $json = file_get_contents( SCALEUP_DIR . '/schemas.json' );
      $schemas = json_decode( $json, true );
      set_transient( self::STORAGE_TRANSIENT, $schemas );
    }
    self::$_schemas = $schemas;
  }

  /**
   * Return singleton instance of this class
   *
   * @return ScaleUp_Schemas
   */
  static function this() {
    return self::$_this;
  }

  /**
   * Register schema type against a specific post type.
   * Returns an array of 2 elements: instance of ScaleUp_Schema_Type & instance of post type object
   * you can use list( $schema_type_obj, $post_type_object ) = ScaleUp_Schemas::register( .. ) to get the 2 values
   *
   * @param $schema_type
   * @param $post_type
   * @param null $args
   * @param null $properties
   * @return WP_Error|array
   */
  static function register( $schema_type, $post_type, $args = null, $properties = null ) {

    if ( self::is_registered( $schema_type ) )
      return new WP_Error( 'registration-error', sprintf( __( '%s Schema already registered.', $schema_type ) ) );

    $post_type_obj = get_post_type_object( $post_type );

    if ( is_null( $post_type_obj ) ) {
      // register post type
      $post_type_obj = register_post_type( $post_type, $args );

    } else {
      // post type already defined, so let's override its properties
      /**
       * @todo: implement post type override
       */
    }

    $schema_type_args = array(
      'properties' => $properties
    );
    $default = self::$_schemas[ 'types' ][ $schema_type ];
    $schema_type_args = wp_parse_args( $schema_type_args, $default );
    $schema_type_obj = new ScaleUp_Schema_Type( $schema_type_args );

    $registered = array( $schema_type_obj, $post_type_obj );
    self::$_registered_schemas->set( $schema_type, $registered );

    return $registered;
  }

  /**
   * Return post type registered for specific schema type
   *
   * @todo: implement get_post_type
   * @param $schema_type
   */
  static function get_post_type( $schema_type ) {

  }

  /**
   * Return true if schema type is registed, otherwise return false.
   *
   * @param $schema_type
   * @return bool
   */
  static function is_registered( $schema_type ) {
    return self::$_registered_schemas->has( $schema_type );
  }

  /**
   * Return schema type for specific post type
   *
   * @todo: Implement get_schema_type function
   * @param $post_type
   * @return bool|string
   */
  static function get_schema_type( $post_type ) {
    $schema_type = false;

    return $schema_type;
  }

  /**
   * Check if specified property is a schema property
   *
   * @param $name
   * @return bool
   */
  static function is_property( $name ) {
    return isset( self::$_schemas[ 'properties' ][ $name ] );
  }

  /**
   * Return property definition from schema type reference
   *
   * @param $name
   * @return bool|array
   */
  static function get_property( $name ) {
    if ( self::is_property( $name ) )
      return self::$_schemas[ 'properties' ][ $name ];
    return false;
  }


  static function get_properties( $schema ) {
    $properties = null;
    if ( isset( self::$_schemas[ 'types' ][ $schema ] ) ) {
      $properties = self::$_schemas[ 'types' ][ $schema ][ 'properties' ];
    }
    return $properties;
  }

}
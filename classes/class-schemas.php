<?php
/**
 * Provides access and acts as repository for available schemas
 */
class ScaleUp_Schemas {

  protected static $_this;

  protected static $_schemas;

  protected static $_registered_schemas;

  protected static $_post_types;

  protected static $_custom_properties;

  const STORAGE_TRANSIENT = 'scaleup_schemas_storage';

  function __construct( $args = null ) {

    if ( isset( self::$_this ) )
      wp_die( sprintf( __( '%s is a singleton class and you cannot create a second instance.',
        'scaleup' ), get_class( $this ) ) );

    self::$_this = $this;
    $this->initialize();

    self::$_registered_schemas  = new ScaleUp_Base();
    self::$_post_types          = new ScaleUp_Base();

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
  static function register_schema( $schema_type, $post_type, $args = null, $properties = null ) {

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
    $default = get_schema( $schema_type, true );
    $schema_type_args = wp_parse_args( $schema_type_args, $default );
    $schema_type_obj = new ScaleUp_Schema_Type( $schema_type_args );

    $registered = new ScaleUp_Base( array( 'schema_type' => $schema_type_obj, 'post_type' => $post_type_obj ) );
    self::$_registered_schemas->set( $schema_type, $registered );
    self::$_post_types->set( $post_type, $schema_type_obj );

    return $registered;
  }

  /**
   * Register custom property against several schema types or
   *
   * @param $property_name
   * @param array $schema_types
   * @param array $args
   * @return ScaleUp_Schema_Property|bool|WP_Error
   */
  static function register_property( $property_name, $schema_types = array(), $args = array() ) {

    if ( empty( $property_name ) ) {
      return new WP_Error( 'empty', __( 'Property name can not be empty' ) );
    }

    if ( empty( $schema_types ) ) {
      if ( self::$_custom_properties->has( $property_name ) ) {
        return new WP_Error( 'exists', sprintf( __( '%s custom property already exists.' ), $property_name ) );
      } else {
        $property_obj = new ScaleUp_Schema_Property( $property_name, $args );
        self::$_custom_properties->set( $property_name, $property_obj );
        return $property_obj;
      }
    }

    $property_obj = new ScaleUp_Schema_Property( $property_name, $args );

    foreach ( $schema_types as $schema_type ) {
      if ( self::is_registered( $schema_type ) ) {
        $schema_type_obj = self::$_registered_schemas->get( $schema_type )->get( 'schema_type' );
        $schema_type_obj->set( $property_name, $property_obj );
      }
    }

    return $property_obj;
  }

  /**
   * Return a new schema object populated with schema properties.
   * If $reference is true, return schema args from schema definition.
   * If nothing is found, return false.
   *
   * @param $schema_type
   * @param bool $reference
   * @return bool|ScaleUp_Schema|array
   */
  static function get_schema( $schema_type, $reference = false ) {

    if ( $reference ) {
      if ( isset( self::$_schemas[ 'types' ][ $schema_type ] ) ) {
        return self::$_schemas[ 'types' ][ $schema_type ];
      }
    }

    if ( ScaleUp_Schemas::is_registered( $schema_type ) ) {
      $schema = new ScaleUp_Schema( $schema_type );
      return $schema;
    }
    return false;
  }
  
  /**
   * Return post type registered for specific schema type
   *
   * @param $schema_type
   * @return bool|string
   */
  static function get_post_type( $schema_type ) {
    $post_type = false;
    if ( self::is_registered( $schema_type ) ) {
      $post_type_object = self::$_registered_schemas->get( $schema_type )->get( 'post_type' );
      $post_type = $post_type_object->name;
    }
    return $post_type;
  }

  /**
   * Return schema type for specific post type
   *
   * @param $post_type
   * @return bool|string
   */
  static function get_schema_type( $post_type ) {
    $schema_type = false;
    if ( self::$_post_types->has( $post_type ) ) {
      $schema_type_obj = self::$_post_types->get( $post_type );
      $schema_type = $schema_type_obj->get( 'schema_type' );
    }
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
   * Return true if schema type is registed, otherwise return false.
   *
   * @param $schema_type
   * @return bool
   */
  static function is_registered( $schema_type ) {
    return self::$_registered_schemas->has( $schema_type );
  }


  /**
   * Return property definition from reference
   *
   * @param $name
   * @return bool|array
   */
  static function get_property_reference( $name ) {
    if ( self::is_property( $name ) )
      return self::$_schemas[ 'properties' ][ $name ];
    return false;
  }

  /**
   * Return array of properties for a schema.
   * Set $reference to true if you want to get properties from reference instead of registered schema.
   *
   * @param $schema_type
   * @param bool $reference
   * @return array|null
   */
  static function get_properties( $schema_type, $reference = false ) {
    $properties = null;
    if ( $reference ) {
      if ( isset( self::$_schemas[ 'types' ][ $schema_type ] ) ) {
        $properties = self::$_schemas[ 'types' ][ $schema_type ][ 'properties' ];
      }
    } else {
      if ( self::is_registered( $schema_type ) ) {
        $schema = self::$_registered_schemas->get( $schema_type )->get( 'schema_type' );
        $properties = $schema->get( 'properties' );
      }
    }
    return $properties;
  }

}
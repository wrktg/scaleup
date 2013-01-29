<?php
/**
 * Provides access and acts as repository for available schemas
 */
class ScaleUp_Schemas extends ScaleUp_Base{

  private static $_initialized = false;

  private static $_this;

  private static $_schemas;

  private static $_registered_schemas = array();

  const STORAGE_TRANSIENT = 'scaleup_schemas_storage';

  /**
   * Initialize ScaleUp_Schemas should only be called once during a request cycle
   */
  function initialize() {

    self::$_this = $this;

    if ( false === ( $schemas = get_transient( self::STORAGE_TRANSIENT ) ) ) {
      $json = file_get_contents( SCALEUP_DIR . '/schemas.json' );
      $schemas = json_decode( $json, true );
      set_transient( self::STORAGE_TRANSIENT, $schemas );
    }
    self::$_schemas = $schemas;

    self::$_initialized = true;
  }

  function register( $schema_type, $post_type, $args = null ) {

    if ( isset( self::$_registered_schemas[ $schema_type ] ) ) {
      $schema = self::get_schema( $schema_type );
      return new WP_Error( 'registration-error', sprintf( __( '%s Schema already registered.', $schema_type ) ) );
    }

  }

  /**
   * Return definition for specific schema.
   *
   * @param $schema_type
   * @param null $args
   * @return bool|ScaleUp_Schema
   */
  static function get_schema( $schema_type, $args = null ) {

    $schema = false;

    $default = array(
      'parents'     => false,
      'children'    => false,
      'reference'   => false,   // when true schema definition will be taken from schema reference instead of registered schemas
    );

    $args = wp_parse_args( $args, $default );

    if ( $args[ 'reference' ] )
      return new ScaleUp_Schema( $schema_type );

    return $schema;
  }

  /**
   * Return true if schema type is registed, otherwise return false.
   *
   * @param $schema_type
   * @return bool
   */
  static function is_registered( $schema_type ) {
    return isset( self::$_registered_schemas[ $schema_type ] );
  }

}
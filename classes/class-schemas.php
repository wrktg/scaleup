<?php
/**
 * Provides access and acts as repository for available schemas
 */
class ScaleUp_Schemas {

  private static $_initialized = false;

  private static $_this;

  private static $_schemas;

  const STORAGE_TRANSIENT = 'scaleup_schemas_storage';

  function __construct(){

    if ( !self::$_initialized ) {
      $this->initialize();
    }

  }

  /**
   * Initialize ScaleUp_Schemas should only be called once during a request cycle
   */
  function initialize() {
    self::$_schemas = $this->_load_schemas();
  }

  /**
   * Return array of schemas generated from schemas.json
   *
   * @return array|mixed
   */
  private static function _load_schemas() {

    if ( false === ( $schemas = get_transient( self::STORAGE_TRANSIENT ) ) ) {
      $json = file_get_contents( SCALEUP_DIR . '/schemas.json' );
      $schemas = json_decode( $json, true );
      set_transient( self::STORAGE_TRANSIENT, $schemas );
    }

    return $schemas;
  }

}
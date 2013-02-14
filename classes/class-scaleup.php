<?php
class ScaleUp {

  private static $_this;

  /**
   * Registered features
   * @var array
   */
  private static $_feature_types = array();

  static $duck_types = array( 'routable', 'contextual' );

  var $site;

  function __construct() {

    if ( isset( self::$_this ) ) {
      return new WP_Error( 'instantiation-error', 'ScaleUp class is a singleton and can only be instantiated once.' );
    } else {
      self::$_this = $this;
      $this->site  = new ScaleUp_Site( array( 'name' => 'WordPress' ) );
    }

  }

  static function this() {
    return self::$_this;
  }

  /**
   * Returns the global scope object for this class
   *
   * @return ScaleUp_Site
   */
  static function get_site() {
    return self::$_this->site;
  }

  /**
   * Make a feature type available.
   *
   * Example:
   *  $args = array(
   *    '__CLASS__' => 'ScaleUp_Form',
   *    '_plural'   => 'forms',
   *  )
   * @param $feature_type
   * @param $args
   */
  static function register_feature_type( $feature_type, $args ) {
    $args[ '_feature_type' ]              = $feature_type;
    self::$_feature_types[ $feature_type ] = $args;

    return $args;
  }

  /**
   * Return true if feature type has is of a specific duck type, otherwise return false
   *
   * @param $feature_type
   * @param $duck_type
   * @return bool
   */
  static function is_duck_type( $feature_type, $duck_type ) {

    $feature_type_args = ScaleUp::get_feature_type( $feature_type );

    return !is_null( $feature_type_args )
      && isset( $feature_type_args[ '_duck_types' ] )
      && is_array( $feature_type_args[ '_duck_types' ] )
      && in_array( $duck_type, $feature_type_args[ '_duck_types' ] );
  }

  /**
   * Check if a feature type is available
   *
   * @param $feature_type
   * @return bool
   */
  static function is_registered_feature_type( $feature_type ) {
    return isset( self::$_feature_types[ $feature_type ] );
  }

  /**
   * Return feature type by matching first args $key and $value.
   * $key can be __CLASS__, _plural or _feature_type
   *
   * @param $key
   * @param $value
   * @return string|null
   */
  static function find_feature_type( $key, $value ) {

    $found_feature_type = null;

    foreach ( self::$_feature_types as $feature_type => $args ) {
      if ( isset( $args[ $key ] ) && $args[ $key ] == $value ) {
        $found_feature_type = $feature_type;
        break;
      }
    }

    return $found_feature_type;
  }

  /**
   * Return args for a feature type
   *
   * @param $feature_type
   * @return array|null
   */
  static function get_feature_type( $feature_type ) {

    if ( !isset( self::$_feature_types[ $feature_type ] ) ) {
      return null;
    }

    return self::$_feature_types[ $feature_type ];
  }

  static function register( $feature_type, $args ) {
    self::$_this->site->register( $feature_type, $args );
  }

  static function activate( $feature_type, $name, $args = array() ) {
    return self::$_this->site->activate( $feature_type, $name, $args );
  }

}
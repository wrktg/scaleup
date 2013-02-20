<?php
class ScaleUp {

  private static $_this;

  /**
   * Registered features
   * @var array
   */
  private static $_feature_types = array();

  private static $_duck_types = array();

  var $site;

  function __construct() {

    if ( isset( self::$_this ) ) {
      return new WP_Error( 'instantiation-error', 'ScaleUp class is a singleton and can only be instantiated once.' );
    } else {
      self::$_this = $this;
      $this->site  = new ScaleUp_Site( array( 'name' => 'WordPress' ) );
    }

    do_action( 'scaleup_init' );

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
   * @param $feature_type string
   * @param $args array
   * @return array
   */
  static function register_feature_type( $feature_type, $args = array() ) {

    $default = array(
      '__CLASS__'     => 'ScaleUp_Feature',
      '_feature_type' => $feature_type,
      '_plural'       => "{$feature_type}s",
      '_supports'     => array(),
      '_duck_types'   => array(),
    );

    $args = wp_parse_args( $args, $default );
    self::$_feature_types[ $feature_type ] = $args;

    return $args;
  }

  /**
   * Register duck type to make it available to ScaleUp
   *
   * @param $duck_type
   * @param array $args
   * @return array
   */
  static function register_duck_type( $duck_type, $args = array() ) {

    $default = array(
      '__CLASS__'     => 'ScaleUp_Duck_Type',
      'duck_type'     => $duck_type,
      'methods'       => array(),
    );

    if ( self::is_registered_duck_type( $duck_type ) ) {
      $args = self::get_duck_type( $duck_type );
    } else {
      $args = wp_parse_args( $args, $default );
      self::$_duck_types[ $duck_type ] = $args;
    }

    return $args;
  }

  /**
   * Return activated duck types
   *
   * @return array
   */
  static function get_duck_types() {
    return self::$_duck_types;
  }

  /**
   * Return true if duck type is registered, otherwise return false.
   *
   * *Note*: this function will also return true when the duck type is activated.
   *
   * @param $duck_type
   * @return bool
   */
  static function is_registered_duck_type( $duck_type ) {
    return isset( self::$_duck_types[ $duck_type ] );
  }

  /**
   * Return true if duck type was activated.
   *
   * @param $duck_type
   * @return bool
   */
  static function is_activated_duck_type( $duck_type ) {
    return self::is_registered_duck_type( $duck_type ) && is_object( self::get_duck_type( $duck_type ) );
  }

  /**
   * Return args array if duck_type is registered, object if its activated or null if its not registered.
   *
   * @param $duck_type
   * @return array|object|null
   */
  static function get_duck_type( $duck_type ) {

    $args = null;

    if ( self::is_registered_duck_type( $duck_type ) ) {
      $args = self::$_duck_types[ $duck_type ];
    }

    return $args;
  }

  /**
   * Return duck type object or null if duck type is not registered
   *
   * @param $duck_type string
   * @return object|null
   */
  static function activate_duck_type( $duck_type ) {

    $object = null;

    if ( self::is_activated_duck_type( $duck_type ) ) {
      // if already activated then return the activated object
      $object = ScaleUp::get_duck_type( $duck_type );
    } else {

      if ( self::is_registered_duck_type( $duck_type ) ) {
        // then activate the registered object
        $args = ScaleUp::get_duck_type( $duck_type );
        if ( class_exists( $args[ '__CLASS__' ] ) ) {
          $class = $args[ '__CLASS__' ];
          $object = new $class( $args );
          self::$_duck_types[ $duck_type ] = $object;
        }
      }

    }

    return $object;
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
    return self::$_this->site->register( $feature_type, $args );
  }

  static function activate( $feature_type, $args ) {
    return self::$_this->site->activate( $feature_type, $args );
  }

}
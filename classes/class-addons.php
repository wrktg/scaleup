<?php
class ScaleUp_Addons {

  private static $_this;

  private static $_available_addons;

  function __construct( ) {
    self::$_this = $this;
  }

  /**
   * Register an addon, return true if successful or return WP_Error on error.
   *
   * @param $name
   * @param $class
   * @return bool|WP_Error
   */
  static function register_addon( $name, $class ) {

    if ( isset( self::$_available_addons[ $name ] ) )
      return new WP_Error( 'addon-exists', sprintf( __( '%s already registered' ), $name ) );

    if ( !class_exists( $class ) )
      return new WP_Error( 'addon-not-available', sprintf( __( '%s could not be registered because %s is not available.', 'scaleup') , $name, $class ) );

      self::$_available_addons[ $name ] = $class;

    return true;
  }

  /**
   * Return true if an addon is availble.
   *
   * @param $name
   * @return bool
   */
  public static function is_available( $name ) {
    return isset( self::$_available_addons[ $name ] );
  }

  /**
   * Return an instance of a requested addon
   *
   * @param $name
   * @param $args
   * @return mixed
   */
  public static function get_addon( $name, $args ) {
    $class = self::$_available_addons[ $name ];
    return new $class( $args );
  }

}
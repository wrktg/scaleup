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
   * @param $slug
   * @param $class
   * @return bool|WP_Error
   */
  static function register_addon( $slug, $class ) {

    if ( isset( self::$_available_addons[ $slug ] ) )
      return new WP_Error( 'addon-exists', sprintf( __( '%s already registered' ), $slug ) );

    if ( !class_exists( $class ) )
      return new WP_Error( 'addon-not-available', sprintf( __( '%s could not be registered because %s is not available.', 'scaleup') , $slug, $class ) );

      self::$_available_addons[ $slug ] = $class;

    return true;
  }

  /**
   * Return true if an addon is availble.
   *
   * @param $slug
   * @return bool
   */
  public static function is_available( $slug ) {
    return isset( self::$_available_addons[ $slug ] );
  }

  /**
   * Return an instance of a requested addon
   *
   * @param $slug
   * @param $args
   * @param $context
   * @return mixed
   */
  public static function get_addon( $slug, $args, $context = null ) {
    /**
     * @todo: Implement get_addon from $context
     */
    $class = self::$_available_addons[ $slug ];
    return new $class( $args, $context );
  }

}
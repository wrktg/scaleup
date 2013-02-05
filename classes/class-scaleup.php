<?php
class ScaleUp {

  static $_methods = array();

  /**
   * Magic function to call registered methods
   *
   * @param $method
   * @param $args
   * @return mixed
   */
  static function __callStatic( $method, $args ) {
    if ( isset( self::$_methods[ $method ] ) ) {
      $callback = self::$_methods[ $method ];
      if ( is_callable( $callback ) ) {
        return call_user_func( $callback, $args );
      }
    }
  }

  /**
   * Register ScaleUp API method to specific callback methods
   *
   * @param $method
   * @param $callback
   */
  static function register( $method, $callback ) {
    if ( !isset( self::$_methods[ $method ] ) ) {
      self::$_methods[ $method ] = $callback;
    }
  }

}
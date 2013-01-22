<?php

if ( !function_exists( 'register_view' ) ) {
  /**
   * Register view with WordPress, an Addon or an App
   * To register with WordPress, the base must be a string
   * To register with an Addon or an App, the base must be an instance of the Addon or App.
   * The class for the Addon or App must implement: get_views & set_views methods.
   *
   * @param $base string|ScaleUp_App|ScaleUp_Addon
   * @param $url string relative to base
   * @param $callbacks array with method as key and callback as value
   * @param $args array
   * @return mixed
   */
  function register_view( $base, $url, $callbacks, $args = array() ) {
    return ScaleUp_Views::register_view( $base, $url, $callbacks, $args );
  }
}

if ( !function_exists( 'register_addon' ) ) {

  /**
   * Make an addon available to be consumed by applications
   *
   * @param $name
   * @param $class
   * @return bool|WP_Error
   */
  function register_addon( $name, $class ) {
    return ScaleUp_Addons::register_addon( $name, $class );
  }

}

if ( !function_exists( 'http_status' ) ) {

  /**
   * Set http status for current request and apply description
   *
   * @param $code
   * @param $message
   */
  function http_status( $code, $message ) {
    ScaleUp_App_Server::http_status( $code, $message );
  }
}
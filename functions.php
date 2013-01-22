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

if ( !function_exists( 'register_template' ) ) {
  /**
   * Register a template located at $path + $template_name
   *
   * $template_name must start with forward slash / and may contain one or more directories.
   *
   * For example: /simple.php, /form/simple.php or /gravityforms/form/simple.php
   *
   * @param $path
   * @param $template_name
   */
  function register_template( $path, $template_name ) {
    $scaleup_templates = ScaleUp_Templates::this();
    $scaleup_templates->register( $path, $template_name );
  }
}


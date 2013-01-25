<?php

if ( !function_exists( 'register_view' ) ) {
  /**
   * Register view with WordPress, an Addon or an App
   * To register with WordPress, the base must be a string
   * To register with an Addon or an App, the base must be an instance of the Addon or App.
   * The class for the Addon or App must implement: get_views & set_views methods.
   *
   * @todo: Consider refactoring to register_view( $url, $callbacks, $args, $context )
   *
   * @param $slug string representing new view
   * @param $url string relative to base
   * @param $callbacks array with method as key and callback as value
   * @param $context string|ScaleUp_App|ScaleUp_Addon
   * @param $args array
   * @return mixed
   */
  function register_view( $slug, $url, $callbacks, $context = null, $args = array() ) {
    return ScaleUp_Views::register_view( $slug, $url, $callbacks, $context, $args );
  }
}

if ( !function_exists( 'register_addon' ) ) {
  /**
   * Make an addon available to be consumed by applications
   *
   * @param $slug
   * @param $class
   * @return bool|WP_Error
   */
  function register_addon( $slug, $class ) {
    return ScaleUp_Addons::register_addon( $slug, $class );
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

if ( !function_exists( 'get_view' ) ) {

  /**
   * Returns specific view either from global scope or the context
   *
   * @param $slug
   * @param null $context
   * @return bool
   */
  function get_view( $slug, $context = null ) {
    return ScaleUp_Views::get_view( $slug, $context );
  }
}

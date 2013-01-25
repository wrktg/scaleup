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

if ( !function_exists( 'scaleup_string_template' ) ) {
  /**
   * Replaces variables in string template that uses {variable_name} syntax.
   * For example, /profile/{username} with array( 'username' => 'taras') produces /profile/taras/
   *
   * @param $template
   * @param $args
   * @return string
   */
  function scaleup_string_template( $template, $args ) {

    $pattern   = $template;
    $len       = strlen( $pattern );
    $tokens    = array();
    $variables = array();
    $pos       = 0;
    preg_match_all( '#.\{(\w+)\}#', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER );
    foreach ( $matches as $match ) {
      if ( $text = substr( $pattern, $pos, $match[ 0 ][ 1 ] - $pos ) ) {
        $tokens[ ] = array( 'text', $text );
      }

      $pos = $match[ 0 ][ 1 ] + strlen( $match[ 0 ][ 0 ] );
      $var = $match[ 1 ][ 0 ];

      // Use the character preceding the variable as a separator
      $separators = array( $match[ 0 ][ 0 ][ 0 ] );

      if ( $pos !== $len ) {
        // Use the character following the variable as the separator when available
        $separators[ ] = $pattern[ $pos ];
      }
      $regexp = sprintf( '[^%s]+', preg_quote( implode( '', array_unique( $separators ) ), '#' ) );

      $tokens[ ] = array( 'variable', $match[ 0 ][ 0 ][ 0 ], $regexp, $var );

      if ( in_array( $var, $variables ) ) {
        /**
         * @todo: Add error that variable can't be used twice
         */
      }

      $variables[ ] = $var;
    }

    if ( $pos < $len ) {
      $tokens[ ] = array( 'text', substr( $pattern, $pos ) );
    }

    $result = '';
    foreach ( $tokens as $token ) {
      if ( 'text' === $token[ 0 ] ) {
        // Text tokens
        $result .= $token[ 1 ];
      }
      if ( 'variable' === $token[ 0 ] ) {
        // Variable tokens
        $prefix = $token[ 1 ];
        if ( isset( $args[ $token[ 3 ] ] ) ) {
          $value = $args[ $token[ 3 ] ];
        } else {
          /**
           * @todo: return an error if args doesn't provide value for variable.
           */
          $value = '';
        }
        $result .= "$prefix$value";
      }
    }

    return $result;
  }
}
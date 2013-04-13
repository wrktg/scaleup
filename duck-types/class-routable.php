<?php
class ScaleUp_Routable extends ScaleUp_Duck_Type {

  private static $_routes = array();

  /**
   * Callback for duck_type filter that applies duck types to a feature.
   *
   * @param ScaleUp_Feature $feature
   * @param array $args
   * @return ScaleUp_Feature
   */
  function duck_types( $feature, $args = array() ) {
    parent::duck_types( $feature, $args );

    if ( true !== $feature->get( 'exclude_route' ) ) {
      $url = $feature->get_url();
      self::$_routes[ $url ] = $feature;
    }

    return $feature;
  }

  /**
   * Return array of active routes
   *
   * @return array
   */
  static function get_routes() {
    return self::$_routes;
  }

  /**
   * Callback function for get_url
   *
   * @param ScaleUp_Feature $feature
   * @param null $args
   * @return string
   */
  static function get_url( $feature, $args = null ) {

    $url = '';

    if ( is_object( $feature ) ) {
      /**
       * @todo: In the future, move this to the filter that's included in the Contextual feature
       */
      if ( $feature->is( 'contextual' ) && $feature->has( 'context' ) && !is_null( $feature->get( 'context' ) ) ) {
        $context = $feature->get( 'context' );
        if ( $context->is( 'routable' ) ) {
          $url = $context->get_url() . $feature->_url;
        }
      } else {
        if ( $feature->has( 'url' ) ) {
          $url = $feature->get( 'url' );
        }
      }
    }

    if ( is_array( $args ) && !empty( $args ) ) {
      $url = apply_filters( 'scaleup_string_template', $url, $args );
    }

    return $url;
  }

  /**
   * Return feature that matches $uri
   *
   * @param $uri
   * @return array
   */
  static function match( $uri ) {
    $result = array( null, array() );
    foreach ( self::$_routes as $template => $feature ) {
      list( $matched, $args ) = self::_match( $template, $uri );
      if ( $matched ) {
        $result = array( $feature, $args );
        break;
      }
    }
    return $result;
  }

  /**
   * Match template against uri and returns an array with match result and matched variables
   *
   * @param $template
   * @param $uri
   * @return array with 2 elements: true or false of match and array or matched args
   */
  private static function _match( $template, $uri ) {

    $regex = self::_build_regexp( $template );
    $output = preg_match( $regex, $uri, $matches );

    $matched  = false;
    $args     = array();

    if ( false == $output ) {
      /**
       * @todo: Should return debug error that there was a problem with regex parsing
       */
    }

    if ( 1 === $output ) {
      // uri was matches, let's get the arguments out of it
      foreach ( $matches as $key => $value ) {
        if ( !is_numeric( $key ) )
          $args[ $key ] = $value;
      }
      $matched = true;
    }

    return array( $matched, $args );
  }


  private static function _build_regexp( $template ) {
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
      $regexp = sprintf( '[^%s]+', preg_quote( implode( '', array_unique( $separators ) ), "#" ) );

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

    // compute the matching regexp
    $regexp = '';
    foreach ( $tokens as $token ) {
      if ( 'text' === $token[ 0 ] ) {
        // Text tokens
        $regexp .= preg_quote( $token[ 1 ], "#" );
      } else {
        // Variable tokens
        $regexp .= sprintf( '%s(?P<%s>%s)', preg_quote( $token[ 1 ], "#" ), $token[ 3 ], $token[ 2 ] );
      }
    }

    /**
     * escape the string and
     */
    $regexp = "'^$regexp/?$'";
    return $regexp;
  }

}

ScaleUp::register_duck_type( 'routable', array(
  '__CLASS__'     => 'ScaleUp_Routable',
  'methods'       => array( 'get_url' ),
) );
ScaleUp::activate_duck_type( 'routable' );
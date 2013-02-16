<?php
class ScaleUp_Site extends ScaleUp_Feature {

  private static $_this;

  function this() {
    return self::$_this;
  }

  function __construct( $args ) {

    if ( isset( self::$_this ) ) {
      return new WP_Error( 'instantiation-error', sprintf( __( '%s class is a singleton and can not be initialized twice.' ), __CLASS__ ) );
    }
    parent::__construct( $args );

  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'site',
      ), parent::get_defaults() );
  }

}

ScaleUp::register_feature_type( 'site', array(
  '__CLASS__'     => 'ScaleUp_Site',
  '_feature_type' => 'site',
  '_plural'       => 'sites',
  '_supports'     => array( 'apps', 'addons', 'views', 'forms', 'schemas' ),
  '_duck_types'   => array( 'routable' ),
  'exclude_route' => true,  // do not include this url when routing
) );
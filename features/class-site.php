<?php
class ScaleUp_Site extends ScaleUp_Feature {

  private static $_this;

  /**
   * Points to current active app
   * @var ScaleUp_App $app
   */
  var $app = null;

  /**
   * Points to current active addon
   * @var ScaleUp_Addon $addon
   */
  var $addon = null;

  function this() {
    return self::$_this;
  }

  function __construct( $args ) {

    if ( isset( self::$_this ) ) {
      return new WP_Error( 'instantiation-error', sprintf( __( '%s class is a singleton and can not be initialized twice.' ), __CLASS__ ) );
    }
    parent::__construct( $args );

  }

  /**
   * Set $this->app & $this->addon from provided feature.
   *
   * @param ScaleUp_View $feature
   */
  function set_state( $feature ) {
    $context = $feature->get( 'context' );
    switch ( $context->get( '_feature_type' ) ) :
      case 'addon':
        $this->addon  = $context;
        $context      = $context->get( 'context' );
      case 'app':
        $this->app    = $context;
      break;
    endswitch;
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
  '_supports'     => array( 'apps', 'addons', 'views', 'forms', 'schemas', 'alerts' ),
  '_duck_types'   => array( 'routable' ),
  'exclude_route' => true,  // do not include this url when routing
) );
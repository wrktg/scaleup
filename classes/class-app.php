<?php
class ScaleUp_App {

  private $_base;

  private $_url;

  private $_views;

  private $_schemas;

  private $_args;

  private $_active_addons = array();

  function __construct( $args = array() ) {

    $default = array(
      'base'    => home_url(),
      'url'     => '/',
      'addons'  => array(),
    );

    $args = wp_parse_args( $args, $default );

    $this->_args  = $args;
    $this->_base  = $args[ 'base' ];
    $this->_url   = $args[ 'url' ];
    $this->_views = new ScaleUp_Views( array( 'base' => $this ) );

    add_action( 'init', array( $this, 'init') );
  }

  function init() {
    $this->activate_addons();
    // $this->adopt_views();
  }

  /**
   * Activate App's addons
   */
  function activate_addons(){
    // make sure that the addon is available
    if ( isset( $this->_args[ 'addons' ] ) ) {
      foreach ( $this->_args[ 'addons' ] as $key => $value ) {
        // value is array is addon arguments were specified
        // otherwise args are empty
        if ( is_array( $value ) ) {
          $name = $key;
          $args = $value;
        } else {
          $name = $value;
          $args = array();
        }

        $default = array(
          'base' => $this,
        );
        $args = wp_parse_args( $args, $default );

        if ( ScaleUp_Addons::is_available( $name ) ) {
          $this->_active_addons[] = ScaleUp_Addons::get_addon( $name, $args );
        }
      }
    }
  }

  /**
   * Adopt views from addons into this app
   */
  function adopt_views() {
    foreach ( $this->_active_addons as $addon ) {
      if ( method_exists( $addon, 'get_views' ) ) {
        $views = $addon->get_views();
        foreach ( $views as $view )
          $this->_views->adopt_view( $view );
      }
    }
  }

  function get_views() {
    return $this->_views;
  }

  function set_views( $views ) {
    $this->_views = $views;
  }

}
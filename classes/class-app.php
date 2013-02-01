<?php
class ScaleUp_App extends ScaleUp_Routable {

  var $_url;

  var $_views;

  var $_active_addons;

  function __construct( $args = array() ) {

    parent::__construct( $args );

    add_action( 'scaleup_init', array( $this, 'scaleup_init' ) );
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        'url'             => '/',
        'active_addons'   => array(),
        'views'           => new ScaleUp_Views( $this ),
        'exclude_route'   => true,
    ), parent::get_defaults() );
  }

  function scaleup_init() {
    $this->activate_addons();
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

        if ( ScaleUp_Addons::is_available( $name ) ) {
          $this->_active_addons[] = ScaleUp_Addons::get_addon( $name, $args, $this );
        }
      }
    }
  }

  function get_views() {
    return $this->_views;
  }

  function set_views( $views ) {
    $this->_views = $views;
  }

  function get_url() {
      return $this->_url;
  }

}
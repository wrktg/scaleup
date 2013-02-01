<?php
class ScaleUp_Frontpage_Addon extends ScaleUp_Addon {

  function get_defaults() {
    return wp_parse_args(
      array(
           'name'       => 'frontpage',
           'url'        => '',
      ), parent::get_defaults() );
  }

  function initialize() {
    register_view( 'frontpage', '', array( 'GET' => array( $this, 'show_frontpage' ) ), $this );
    register_template( dirname( __FILE__ ) . '/templates', '/frontpage.php' );
  }

  function show_frontpage() {
    get_template_part( '/frontpage.php' );
  }

}
if ( function_exists( 'register_addon' ) ) {
  register_addon( 'frontpage', 'ScaleUp_Frontpage_Addon' );
}
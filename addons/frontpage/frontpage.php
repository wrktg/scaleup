<?php
class ScaleUp_Frontpage_Addon extends ScaleUp_Addon {

  function get_defaults() {
    return wp_parse_args(
      array(
        'name'      => 'frontpage',
        'url'       => '',
        'views'     => array(
          'frontpage' => array(
            'name' => 'frontpage',
            'url'  => ''
          )
        ),
        'templates' => array(
          'frontpage' => array(
            'path'     => dirname( __FILE__ ) . '/templates',
            'template' => '/frontpage.php'
          )
        ),
      ), parent::get_defaults() );
  }

  function get_frontpage( $args = array() ) {
    get_template_part( '/frontpage.php' );
  }

}

ScaleUp::register( 'addon', array( 'name' => 'frontpage', '__CLASS__' => 'ScaleUp_Frontpage_Addon' ) );
<?php
class ScaleUp_Login_Addon extends ScaleUp_Addon {

  function __construct( $args ) {

    $default = array(
      'base'  => null,
      'url'   => 'login',
    );

    $args = wp_parse_args( $args, $default );

    $this->_base  = $args['base'];
    $this->_url   = $args['url'];
    $this->_views = new ScaleUp_Views( array( 'base' => $this ) );


    // register view on /$prefix/login/
    register_view( $this, '/', array(
                                    'GET'=> array( $this, 'GET' ),
                                    'POST'=> array( $this, 'POST')
                               ));

  }

  function GET( $args ) {
    echo "This callback shows the forms";
  }

  function POST( $args ) {
    echo "This callback handles authentication or user creation.";
  }

}
<?php
class ScaleUp_Login_Addon extends ScaleUp_Addon {

  private $_views;

  private $_base;

  function __construct( $args ) {

    $default = array(
      'base' => 'login'
    );

    $args = wp_parse_args( $args, $default );

    $this->_base  = $args['base'];
    /**
     * @todo: there has to be a better way to do this. We can not expect developers to remember this logic
     *
     */
    if ( is_object( $this->_base ) && method_exists( $this->_base, 'get_views' ) ) {
      $this->_views = $this->_base->get_views();
    } else {
      $this->_views = new ScaleUp_Views( $args[ 'base' ] );
    }


    // register view on /$prefix/login/
    register_view( $this, '/', array(
                                    'get'=> array( $this, 'get' ),
                                    'post'=> array( $this, 'post')
                               ));

  }

  function get() {
    echo "This callback shows the forms";
  }

  function post() {
    echo "This callback handles authentication or user creation.";
  }

}
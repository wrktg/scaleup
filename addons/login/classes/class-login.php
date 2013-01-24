<?php
class ScaleUp_Login_Addon extends ScaleUp_Addon {

  function __construct( $args ) {

    $default = array(
      'base'  => null,
      'url'   => 'login',
      'forms' => array(
        'login' => array(
          'fields' => array(
            array(
              'id'        => 'username',
              'type'      => 'text',
              'required'  => true,
            ),
            array(
              'id'        => 'password',
              'type'      => 'password',
              'required'  => true,
            ),
            array(
              'id'        => 'submit',
              'type'      => 'button',
              'text'      => __( 'Login' ),
            ),
            array(
              'id'        => 'forgot',
              'type'      => 'custom',
              'callback'  => array( $this, 'forgot_field' ),
            ),
          ),
        ),
        'register' => array(
          'fields' => array(
            array(
              'id'        => 'givenName',
              'type'      => 'text',
              'required'  => true
            ),
            array(
              'id'        => 'familyName',
              'type'      => 'text',
              'required'  => true,
            ),
            array(
              'id'        => 'userName',
              'type'      => 'text',
              'unique'    => true,
              'required'  => true,
            ),
            array(
              'id'        => 'email',
              'type'      => 'text',
              'validation'=> array('email'),
            ),
            array(
              'id'        => 'submit',
              'type'      => 'button',
              'text'      => __( 'Sign up' ),
            ),
          ),
        ),
      ),
    );

    $args = wp_parse_args( $args, $default );

    $this->_base  = $args['base'];
    $this->_url   = $args['url'];
    $this->_views = new ScaleUp_Views( array( 'base' => $this ) );

    // register view on /$prefix/login/
    register_view( $this, '/',
      array(
           'GET'=> array( $this, 'GET' ),
           'POST'=> array( $this, 'POST')
      ), array( 'forms' => array( $args['forms'] ) ) );

    register_template( dirname( dirname( __FILE__ ) ) . '/templates', '/login.php' );

  }

  function GET( $view, $args ) {
    get_template_part( '/login.php' );
  }

  function POST( $view, $args ) {
    get_template_part( '/login.php' );
  }

  function forgot_field() {
    $lost_password_url = wp_lostpassword_url();
    $html = <<<HTML
<a href="{$lost_password_url}" title="Lost Password">Lost Password</a>
HTML;
    echo $html;
  }

}
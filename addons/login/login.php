<?php
class ScaleUp_Login_Or_Register_Addon extends ScaleUp_Addon {

  function init() {

    ScaleUp::add_form( array(
        'name'         => 'login',
        'title'        => __( 'Login' ),
        'form_fields'       => array(
          array(
            'name'        => 'username',
            'type'        => 'text',
            'validation'  => array( 'required' ),
            'placeholder' => __( 'Username' ),
            'class'       => 'input-block-level',
          ),
          array(
            'name'        => 'password',
            'type'        => 'password',
            'validation'  => array( 'required' ),
            'placeholder' => __( 'Password' ),
            'class'       => 'input-block-level'
          ),
          array(
            'name'  => 'submit',
            'type'  => 'button',
            'text'  => __( 'Login' ),
            'value' => 'login',
            'class' => 'btn-large'
          ),
          array(
            'name'     => 'forgot',
            'type'     => 'custom',
          ),
        ),
        'confirmation' => __( 'Welcome back!' ),
      )
    );

    ScaleUp::add_form( array(
      'name'         => 'register',
      'title'        => __( 'Register' ),
      'form_fields'       => array(
        array(
          'name'          => 'givenName',
          'type'          => 'text',
          'validation'    => array( 'required' ),
          'label'         => __( 'First name' ),
          'class'         => 'input-block-level',
          'control-group' => true,
        ),
        array(
          'name'       => 'familyName',
          'label'      => __( 'Last name' ),
          'type'       => 'text',
          'validation' => array( 'required' ),
          'class'      => 'input-block-level'
        ),
        array(
          'name'       => 'email',
          'label'      => __( 'Email' ),
          'type'       => 'text',
          'validation' => array( 'required', 'email', array( 'ScaleUp_Login_Or_Register_Addon', 'validate_unique_user_email' ) ),
          'class'      => 'input-block-level'
        ),
        array(
          'name'       => 'userName',
          'label'      => __( 'Username' ),
          'type'       => 'text',
          'validation' => array( 'required', array( 'ScaleUp_Login_Or_Register_Addon', 'validate_unique_username' ) ),
          'class'      => 'input-block-level',
        ),
        array(
          'name'   => 'submit',
          'type'   => 'button',
          'text'   => __( 'Register' ),
          'value'  => 'register',
          'class'  => 'btn-large btn-primary',
          'submit' => true,
        ),
      ),
      'confirmation' => __( 'Welcome! You might want to checkout your profile.' ),
    ) );

    $this->add( 'view', array(
      'name' => 'login_or_register',
      'url'  => ''
    ) );

    ScaleUp::add_template( array(
      'name'     => 'login_or_register',
      'path'     => dirname( __FILE__ ) . '/templates',
      'template' => '/login-or-register.php',
    ) );

  }

  function get_defaults() {
    return wp_parse_args(
      array(
        'url'  => '/login',
      ), parent::get_defaults() );
  }

  /**
   * Displays forms to the user.
   *
   * @param $args
   */
  function get_login_or_register( $args ) {
    get_template_part( '/login-or-register.php' );
  }

  /**
   * Processes submitted forms. Callback function for POST request.
   *
   * @param $args
   * @return bool
   */
  function post_login_or_register( $args ) {

    if ( isset( $args[ 'form_name' ] ) ) {
      $form = ScaleUp::get_form( $args[ 'form_name' ] );
      $form->process( $args );
      get_template_part( '/login-or-register.php' );
    }

  }

  static function validate_unique_username( $pass ) {

    return $pass;
  }

  static function validate_unique_user_email( $pass ) {

    return $pass;
  }

}

ScaleUp::register( 'addon', array(
  'name' => 'login_or_register',
  '__CLASS__' => 'ScaleUp_Login_Or_Register_Addon'
));
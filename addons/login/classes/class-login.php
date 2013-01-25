<?php
class ScaleUp_Login_Addon extends ScaleUp_Addon {

  function get_defaults() {
    return array(
      'url' => 'login',
      'forms' => array(
        'login' => array(
          'title' => __( 'Login' ),
          'fields' => array(
            array(
              'id' => 'username',
              'type' => 'text',
              'required' => true,
              'placeholder' => __( 'Username' ),
              'class' => 'input-block-level',
            ),
            array(
              'id' => 'password',
              'type' => 'password',
              'required' => true,
              'placeholder' => __( 'Password' ),
              'class' => 'input-block-level'
            ),
            array(
              'id' => 'submit',
              'type' => 'button',
              'text' => __( 'Login' ),
              'value' => 'login',
              'class' => 'btn-large'
            ),
            array(
              'id' => 'forgot',
              'type' => 'custom',
              'callback' => array( $this, 'forgot_field' ),
            ),
          ),
          'confirmation' => __( 'Welcome back!' ),
        ),
        'register' => array(
          'title' => __( 'Register' ),
          'fields' => array(
            array(
              'id' => 'givenName',
              'type' => 'text',
              'required' => true,
              'label' => __( 'First name' ),
              'class' => 'input-block-level',
            ),
            array(
              'label' => __( 'Last name' ),
              'id' => 'familyName',
              'type' => 'text',
              'required' => true,
              'class' => 'input-block-level'
            ),
            array(
              'label' => __( 'Username' ),
              'id' => 'userName',
              'type' => 'text',
              'unique' => true,
              'required' => true,
              'class' => 'input-block-level',
            ),
            array(
              'label' => __( 'Email' ),
              'id' => 'email',
              'type' => 'text',
              'validation' => array( 'email' ),
              'class' => 'input-block-level'
            ),
            array(
              'id' => 'submit',
              'type' => 'button',
              'text' => __( 'Sign up' ),
              'value' => 'register',
              'class' => 'btn-large btn-primary',
              'submit' => true,
            ),
          ),
          'confirmation' => __( 'Welcome to community! You might want to checkout your profile.' ),
        ),
      ),
    );
  }

  function initialize() {
    // register view on /$prefix/login/
    register_view( 'login', '/', array( 'GET'  => array( $this, 'display_login_forms' ), 'POST' => array( $this, 'process_login_request') ), $this, array( 'forms' => $this->get( 'forms' ) ) );
    register_template( dirname( dirname( __FILE__ ) ) . '/templates', '/login.php' );
  }

  /**
   * Displays forms to the user. Callback function for GET request to $base/ url.
   *
   * @param $args
   * @param $context
   */
  function display_login_forms( $args, $context ) {
    get_template_part( '/login.php' );
  }

  /**
   * Processes submitted forms. Callback function for POST request.
   *
   * @param $args
   * @param $context
   */
  function process_login_request( $args, $context ) {

    if ( $form = $context->get_form( $args[ 'submit' ] ) ) {

      $form->load( $args );
      if ( $form->validates() ) {
        /**
         * Need some kind of mechanism to direct users to different pages.
         * it would be safe to assume that they would want to be redirected to something like profile page
         * but I don't yet know how that will be handled. Once or 2 lines, I'm sure, but just dont' know how.
         * it would probably have to do be something like:
         *
         * @todo: figure out what to do after validation
         */
        get_template_part( '/forms/confirmation.php' );
      } else {
        get_template_part( '/login.php' );
      }
    }
  }

  function forgot_field() {
    $lost_password_url = wp_lostpassword_url();
    $html = <<<HTML
<a href="{$lost_password_url}" title="Lost Password">Lost Password</a>
HTML;
    echo $html;
  }

}
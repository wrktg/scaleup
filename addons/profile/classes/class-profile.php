<?php
class ScaleUp_Profile_Addon extends ScaleUp_Addon {

  function initialize() {
    $template_path = dirname( dirname( __FILE__ ) ) . '/templates';
    register_template( $template_path, '/profile.php' );
    register_template( $template_path, '/profile-edit.php' );

    /**
     * GET /profile/edit/
     *  - edit authenticated user's profile
     * POST /profile/edit/
     *  - update authenticated user's profile
     */
    register_view( 'profile-edit', '/edit/', array( 'GET' => array( $this, 'edit_profile' ), 'POST' => array( $this, 'update_profile' ) ), $this, array( 'forms' => $this->get( 'forms' ) ) );

    register_view( 'profile', '/', array( 'GET' => array( $this, 'view_profile' ) ), $this );

    /**
     * GET /profile/{username}/
     *  - view username with username that matches url query
     */
    register_view( 'profile-by-username', '/{username}/', array( 'GET' => array( $this, 'view_profile' ) ), $this );
  }

  function get_defaults() {
    return array(
      'url'   => 'profile',
      'forms' => array(
        'profile' => array(
          'fields'=> array(
            array(
              'id'        => 'userName',
              'type'      => 'text',
              'unique'    => true,
              'required'  => true,
              'label'     => __( 'Username' ),
              'class'     => 'input-block-level',
            ),
            array(
              'id'        => 'givenName',
              'type'      => 'text',
              'required'  => true,
              'label'     => __( 'First Name' ),
              'class'     => 'input-block-level',
            ),
            array(
              'id'        => 'familyName',
              'type'      => 'text',
              'required'  => true,
              'label'     => __( 'Last Name' ),
              'class'     => 'input-block-level',
            ),
            array(
              'id'        => 'email',
              'type'      => 'text',
              'unique'    => true,
              'required'  => true,
              'label'     => __( 'Email' ),
              'class'     => 'input-block-level',
            ),
            array(
              'id'        => 'address',
              'type'      => 'textarea',
              'required'  => false,
              'label'     => __( 'Address' ),
              'class'     => 'input-block-level',
            ),
            array(
              'id' => 'submit',
              'type' => 'button',
              'text' => __( 'Update' ),
              'value' => 'profile',
              'class' => 'btn-large'
            ),
          ),
        ),
        'password' => array(
          'fields' => array(
            array(
              'id'        => 'password',
              'type'      => 'password',
              'required'  => true,
              'label'     => __( 'Password' ),
              'class'     => 'input-block-level',
            ),
            array(
              'id'        => 'confirm',
              'type'      => 'password',
              'required'  => true,
              'label'     => __( 'Confirm password' ),
              'class'     => 'input-block-level',
            ),
            array(
              'id' => 'submit',
              'type' => 'button',
              'text' => __( 'Update' ),
              'value' => 'password',
              'class' => 'btn-large'
            ),
          ),
        ),
      ),
    );
  }

  function edit_profile( $args, $view ) {
    get_template_part( '/profile-edit.php' );
  }

  function update_profile( $args, $view ) {
    get_template_part( '/profile-edit.php' );
  }

  function view_profile( $args, $view ) {

    if ( isset( $args[ 'username' ] ) && !empty( $args[ 'username' ] ) ) {
      if ( $user = get_user_by( 'login', $args[ 'username' ] ) ) {
        // user found
        $view->set( 'user', $user );
      } else {
        $view->set( 'message', array( 'error', __( '%s does not exist.', $args[ 'username' ] ) ) );
      }
    }

    if ( is_user_logged_in() ) {

      global $current_user;
      get_currentuserinfo();
      $view->set( 'user', $current_user );

    } else {
      /**
       * @todo: change this to take path dynamically instead of statically because it might change depending on the configuration
       */
      if ( $login = get_view( 'login' ) )
        $login->redirect();
      else
        wp_redirect( '/login' );
    }

    get_template_part( '/profile.php' );
  }



}
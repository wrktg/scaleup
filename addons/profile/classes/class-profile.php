<?php
class ScaleUp_Profile_Addon extends ScaleUp_Addon {

  function get_defaults() {
    return array(
      'url'     => '/profile',
      'forms'   => array(
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
              'id'        => 'birthDate',
              'type'      => 'text',
              'label'     => 'Birth Date',
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
              'id'        => 'worksFor',
              'type'      => 'text',
              'label'     => 'Company',
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

  function initialize() {

    register_schema( 'Person', 'person',
      array(
        'label'               => __( 'person', 'text_domain' ),
        'description'         => __( 'Person Post Type for People Schema', 'text_domain' ),
        'labels'              => array(
          'name'                => _x( 'People', 'Post Type General Name', 'text_domain' ),
          'singular_name'       => _x( 'Person', 'Post Type Singular Name', 'text_domain' ),
          'menu_name'           => __( 'Person', 'text_domain' ),
          'parent_item_colon'   => __( 'Parent:', 'text_domain' ),
          'all_items'           => __( 'All People', 'text_domain' ),
          'view_item'           => __( 'View People', 'text_domain' ),
          'add_new_item'        => __( 'Add New Person', 'text_domain' ),
          'add_new'             => __( 'New Person', 'text_domain' ),
          'edit_item'           => __( 'Edit Person', 'text_domain' ),
          'update_item'         => __( 'Update Person', 'text_domain' ),
          'search_items'        => __( 'Search people', 'text_domain' ),
          'not_found'           => __( 'No people found', 'text_domain' ),
          'not_found_in_trash'  => __( 'No people found in Trash', 'text_domain' ),
        ),
        'supports'            => array(),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'menu_icon'           => '',
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => false,
        'rewrite'             => false,
        'capability_type'     => 'page',
      ), $this->_get_person_properties() );

    ScaleUp_Schemas::get_post_type( 'Person' );

    $template_path = dirname( dirname( __FILE__ ) ) . '/templates';
    register_template( $template_path, '/profile.php' );
    register_template( $template_path, '/profile-edit.php' );

    /**
     * GET /profile/edit/
     *  - edit authenticated user's profile
     * POST /profile/edit/
     *  - update authenticated user's profile
     */
    register_view( 'profile-edit', '/edit', array( 'GET' => array( $this, 'edit_profile' ), 'POST' => array( $this, 'update_profile' ) ), $this, array( 'forms' => $this->get( 'forms' ) ) );

    register_view( 'profile', '', array( 'GET' => array( $this, 'view_profile' ) ), $this );

    /**
     * GET /profile/{username}/
     *  - view username with username that matches url query
     */
    register_view( 'profile-by-username', '/{username}', array( 'GET' => array( $this, 'view_profile' ) ), $this );

  }

  /**
   *
   * @return mixed
   */
  function _get_person_properties() {
    $schema = get_schema( 'Person', true );
    $properties = array();
    foreach ( $schema[ 'properties' ] as $property_name )
      $properties[ $property_name ] = array( 'meta_type' => 'user' );
    return $properties;
  }

  function edit_profile( $args, $view ) {

    if ( is_user_logged_in() ) {
      $user_id = get_current_user_id();
      $form = get_form( 'profile' );
      $schema = get_schema( 'Person' );
      $schema->read( $user_id );
      $form->load( $schema );
    } else {
      $login = get_view( 'login' );
    }

    get_template_part( '/profile-edit.php' );
  }

  function update_profile( $args, $view ) {

    if ( is_user_logged_in() ) {
      $user_id = get_current_user_id();
      $schema = get_schema( 'Person' );
      $schema->load( $args );
      $schema->update( $user_id );
      $form = get_form( 'profile' );
      $form->load( $schema );
      get_template_part( '/profile-edit.php' );
    } else {
      $login = get_view( 'login' );
    }
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
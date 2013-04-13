<?php
class ScaleUp_Profile_Addon extends ScaleUp_Addon {

  function get_defaults() {
    return wp_parse_args(
      array(
        'name'        => 'profile',
        'url'         => '/profile',
        'forms'       => array(
          'profile' => array(
            'fields'=> array(
              array(
                'name'        => 'nickName',
                'type'      => 'text',
                'validation'  => array( 'required', 'unique' ),
                'label'     => __( 'Nickname' ),
                'class'     => 'input-block-level',
              ),
              array(
                'name'        => 'givenName',
                'type'      => 'text',
                'validation'  => array( 'required' ),
                'label'     => __( 'First Name' ),
                'class'     => 'input-block-level',
              ),
              array(
                'name'        => 'familyName',
                'type'      => 'text',
                'validation'  => array( 'required' ),
                'label'     => __( 'Last Name' ),
                'class'     => 'input-block-level',
              ),
              array(
                'name'        => 'birthDate',
                'type'      => 'text',
                'label'     => 'Birth Date',
                'class'     => 'input-block-level',
              ),
              array(
                'name'        => 'email',
                'type'      => 'text',
                'validation'  => array( 'required', 'unique' ),
                'label'     => __( 'Email' ),
                'class'     => 'input-block-level',
              ),
              array(
                'name'        => 'worksFor',
                'type'      => 'text',
                'label'     => 'Company',
                'class'     => 'input-block-level',
              ),
              array(
                'name'        => 'address',
                'type'      => 'textarea',
                'required'  => false,
                'label'     => __( 'Address' ),
                'class'     => 'input-block-level',
              ),
              array(
                'name' => 'submit',
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
                'name'        => 'password',
                'type'      => 'password',
                'validation'  => array( 'required' ),
                'label'     => __( 'Password' ),
                'class'     => 'input-block-level',
              ),
              array(
                'name'        => 'confirm',
                'type'      => 'password',
                'validation'  => array( 'required' ),
                'label'     => __( 'Confirm password' ),
                'class'     => 'input-block-level',
              ),
              array(
                'name' => 'submit',
                'type' => 'button',
                'text' => __( 'Update' ),
                'value' => 'password',
                'class' => 'btn-large'
              ),
            ),
          ),
        ),
        'schemas'     => array(
          'Person' => array(
            'name'  => 'Person',
            'post_type' => 'person',
            'post_type_args' => array(
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
            ),
            //'properties'  => $this->_get_person_properties(),
          )
        ),
        'properties'  => array(
          'nickName'    => array(
            'schemas'  => array( 'Person' ),
            'meta_type'    => 'user',
            'meta_key'     => 'nickname',
            'label'        => __( 'Nickname' ),
            'description'  => 'Nickname of a person.',
            'data_types'   => array( 'Text' ),
          )
        ),
        'templates'   => array(
          'profile' => array(
            'name'    => 'profile',
            'path'    => dirname( __FILE__ ) . '/templates',
            'template'=> '/profile.php',
          ),
          'profile_edit' => array(
            'name'    => 'profile_edit',
            'path'    => dirname( __FILE__ ) . '/templates',
            'template'=> '/profile-edit.php',
          ),
        ),
        'views' => array(
          'profile_edit'  => array(
            'name'          => 'profile_edit',
            'url'           => '/edit'
          ),
          'profile'       => array(
            'name'          => 'profile',
            'url'           => ''
          ),
          'profile_by_username' => array(
            'name'                => 'profile_by_username',
            'url'                 => '/{username}',
          ),
        ),
      ), parent::get_defaults()
    );
  }

  /**
   * Return array of arguments for each Person property
   *
   * @return mixed
   */
  function _get_person_properties() {
    $schema = get_schema( 'Person', true );
    $properties = array();
    foreach ( $schema[ 'properties' ] as $property_name ) {
      $args = array( 'meta_type' => 'user' );
      // WordPress has metadata for First name and Last name, so let's store it there.
      switch ( $property_name ) :
        case 'givenName' :
          $args[ 'meta_key' ] = 'first_name';
          $properties[ $property_name ] = $args;
          break;
        case 'familyName' :
          $args[ 'meta_key'] = 'last_name';
          $properties[ $property_name ] = $args;
          break;
        default:
          $properties[ $property_name ] = $args;
      endswitch;
    }
    return $properties;
  }

  /**
   * GET /profile/edit callback function
   *
   * Show edit form to logged in user.
   * Unauthorized user is redirected to login.
   * @todo: finish redirection
   *
   * @param $args
   */
  function get_profile_edit( $args ) {

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

  /**
   * POST /profile/edit callback function.
   *
   * Stores the values that were submitted by the users.
   * Unauthorized user is redirected to login
   *
   * @param $args
   */
  function post_profile_edit( $args ) {

    if ( is_user_logged_in() ) {
      $user_id = get_current_user_id();
      $form = get_form( $args[ 'submit' ] );
      if ( $form ) {
        $form->load( $args );
        if ( $form->validates() ) {
          $schema = get_schema( 'Person' );
          $schema->load( $args );
          $schema->update( $user_id );
        }
        get_template_part( '/profile-edit.php' );
      }

    } else {
      $login = get_view( 'login' );
    }
  }

  /**
   * GET /profile/ & GET /profile/{username} callback function
   *
   * Shows user profile
   *
   * @param $args
   */
  function get_profile( $args ) {
    $this->get_profile( $args );
  }

  function get_profile_by_username( $args ) {
    if ( isset( $args[ 'username' ] ) && !empty( $args[ 'username' ] ) ) {
      if ( $user = get_user_by( 'login', $args[ 'username' ] ) ) {
        // user found
        $this->set( 'user', $user );
      } else {
        $this->set( 'message', array( 'error', __( '%s does not exist.', $args[ 'username' ] ) ) );
      }
    }

    if ( is_user_logged_in() ) {

      global $current_user;
      get_currentuserinfo();
      $this->set( 'user', $current_user );

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
ScaleUp::register( 'addon', array( 'name' => 'profile', '__CLASS__' => 'ScaleUp_Profile_Addon' ) );
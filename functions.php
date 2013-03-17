<?php
/**
 * Functions in this file are meant to be used in a theme's functions.php
 */

if ( !function_exists( 'add_form' ) ) {
  /**
   * Adds a form to the site that can be displayed in a template using the_form( $form_name ) function.
   * @see template-tags.php for more information on how to use this form in your theme.
   *
   * Returns a form object that be populated with form fields or processed.
   *
   * @param array $args
   *
   * @return ScaleUp_Form|bool $form
   */
  function add_form( $args = array() ) {

    static $count;

    if ( !isset( $args[ 'name' ] ) ) {
      $count++;
    }

    $default = array(
      'name'          => "form_{$count}", # name to be used when referencing this form ( lower case, no spaces, or special characters, use _ as separator between words )
      'form_fields'   => array(), # array of fields to be included in this form. See add_form_field() for configuration options
      'notifications' => array(), # array of notifications to be sent out after form is verified. See add_form_notification() for configuration options
      'action'        => $_SERVER[ 'REQUEST_URI' ], # url to submit results to
      'method'        => "post", # request method to be made to the url ( options: post or get )
      'enctype'       => "application/x-www-form-urlencoded",
    );

    $form = ScaleUp::add_form( wp_parse_args( $args ) );

    return $form;
  }
} else {
  ScaleUp::add_alert( array(
    'type'  => 'warning',
    'msg'   => sprintf( "add_form function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
    'debug' => true
  ) );
}

if ( !function_exists( 'add_form_field' ) ) {
  /**
   * Add a form field to a form
   *
   * $args
   * -----
   * - type:
   *    - text:       regular input field that allows text to be entered
   *    - hidden:     hidden input field
   *    - textarea:   text area field that allows multiline text entry
   *    - checkbox:   shows a field with many checkboxes
   *    - select:     shows a select field as a dropdown
   *    - raido:      show a group of radio buttons
   *    - html:       allows arbitrary html to be included as a field ( this is not an input field but rather a way to out information to the form )
   * - label:   field's label
   * - options: array of values to show in fields with multiple options like checkbox, radio & select
   * - help:    help information to show after the field
   *
   * @param string|ScaleUp_Form $form
   * @param array $args
   * @return ScaleUp_Form_Field|bool
   */
  function add_form_field( $form, $args = array() ) {

    static $count;

    if ( !isset( $args[ 'name' ] ) ) {
      $count++;
    }

    $args = array(
      'name'        => "field_{$count}", # name attribute of the form field ( highly recommend overwriting this )
      'type'        => "text", # type form field attribute
      'value'       => null, # default form field
      'placeholder' => null, # placeholder text for the form field
      'label'       => null, # label of the form field
      'disabled'    => false, # should this form field be disabled?
    );

    $field = ScaleUp::add_form_field( $form, $args );

    return $field;
  }
} else {
  ScaleUp::add_alert( array(
    'type'  => 'warning',
    'msg'   => sprintf( "add_form_field function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
    'debug' => true
  ) );
}

if ( !function_exists( 'add_form_notification' ) ) {

  /**
   * Add a notification to be issued after form is verified
   *
   * @todo: implement form notifications
   *
   * @param string|ScaleUp_Form $form
   * @param array $args
   * @return ScaleUp_Notification|bool
   */
  function add_form_notification( $form, $args ) {
    $notification = ScaleUp::add_form_notification( $form, $args );

    return $notification;
  }

} else {
  ScaleUp::add_alert( array(
    'type'  => 'warning',
    'msg'   => sprintf( "add_form_notification function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
    'debug' => true
  ) );
}

if ( !function_exists( 'get_form' ) ) {
  /**
   * Return form by $name
   *
   * @param $name
   * @return ScaleUp_Form|bool
   */
  function get_form( $name ) {

    /*** @var $form ScaleUp_Form */
    $form = ScaleUp::get_form( $name );

    return $form;
  }
}

if ( !function_exists( 'register_schema' ) ) {
  /**
   * A schema is a soft structure that a developer can impose on a content item. A schema can have properties,
   * taxonomies and relationships. Each schema has a name that uniquely identifies it within a site.
   *
   * @todo: add documentation about format of args arrays
   *
   * @param string $schema_name
   * @param array $args
   * @return ScaleUp_Schema|bool
   */
  function register_schema( $schema_name, $args = array() ) {

    $name = ScaleUp::slugify( $schema_name );

    $order = array_keys( $args );

    $properties    = array();
    $taxonomies    = array();
    $relationships = array();

    foreach ( $args as $prop_name => $prop_args ) {
      if ( is_array( $prop_args ) ) {
        if ( isset( $prop_args[ 'type' ] ) ) {
          $feature_type = $prop_args[ 'type' ];
          unset( $prop_args[ 'type' ] );
        } else {
          $feature_type = 'property';
        }
        switch ( $feature_type ) :
          case 'taxonomy' :
            $taxonomies[ $prop_name ] = $prop_args;
            break;
          case 'relationships' :
            $relationships[ $prop_name ] = $prop_args;
            break;
          case 'property' :
          default:
            $properties[ $prop_name ] = $prop_args;
        endswitch;
        unset( $args[ $prop_name ] );
      }
    }

    $default = array(
      'name'          => $name,
      'properties'    => $properties,
      'taxonomies'    => $taxonomies,
      'relationships' => $relationships,
      'order'         => $order,
    );

    /**
     * $default is before $args because 'name', 'properties', 'taxonomies' & 'relationships' are reserved arg names in
     * this context. They take priority over user's input ( so, user you better check yourself, before you wreck something. )
     */

    return ScaleUp::register_schema( wp_parse_args( $default, $args ) );
  }
} else {
  ScaleUp::add_alert( array(
    'type'  => 'warning',
    'msg'   => sprintf( "register_schema function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
    'debug' => true
  ) );
}

if ( !function_exists( 'create_item' ) ) {
  /**
   * Create an item with a specific schema and populated with values from the $args array
   * By default, an item has a schema in addition to schema specified via first parameter of this function.
   *
   * If you want to create an item that doesn't have post schema then you can use ScaleUp::create_item function.
   * @see /core/class-scaleup.php
   *
   * @param $schema
   * @param array $args
   * @return ScaleUp_Item
   */
  function create_item( $schema, $args = array() ) {

    $schema = ( array )$schema;

    $default = array(
      'schemas' => array_merge( array( 'post' ), $schema )
    );

    return ScaleUp::create_item( wp_parse_args( $args, $default ) );
  }
} else {
  ScaleUp::add_alert( array(
      'type'  => 'warning',
      'msg'   => sprintf( "create_item function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
      'debug' => true,
    )
  );
}

if ( !function_exists( 'get_item' ) ) {
  /**
   * Get item by specific $id.
   *
   * @param $id
   * @return ScaleUp_Item|false
   */
  function get_item( $id ) {

    return ScaleUp::get_item( $id );
  }
} else {
  ScaleUp::add_alert( array(
      'type'  => 'warning',
      'msg'   => sprintf( "get_item function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
      'debug' => true,
    )
  );
}

if ( !function_exists( 'update_item' ) ) {
  /**
   * Update item with values from $args
   *
   * @param $id
   * @param $args
   * @return bool
   */
  function update_item( $id, $args ) {

    return ScaleUp::update_item( $id, $args );
  }
} else {
  ScaleUp::add_alert( array(
      'type'  => 'warning',
      'msg'   => sprintf( "update_item function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
      'debug' => true,
    )
  );
}

if ( !function_exists( 'delete_item' ) ) {
  /**
   * Delete item with specific id
   *
   * @param $id
   * @return bool
   */
  function delete_item( $id ) {

    return ScaleUp::delete_item( $id );
  }
} else {
  ScaleUp::add_alert( array(
      'type'  => 'warning',
      'msg'   => sprintf( "delete_item function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
      'debug' => true,
    )
  );
}

if ( !function_exists( 'new_item' ) ) {
  /**
   * Return an empty instance of an item without modifying the database.
   * To create an item in the database use create_item function instead.
   *
   * @param $schema
   * @param array $args
   * @return bool|ScaleUp_Feature
   */
  function new_item( $schema, $args = array() ) {

    $schema = ( array )$schema;

    $default = array(
      'schemas' => array_merge( array( 'post' ), $schema )
    );

    return ScaleUp::new_item( wp_parse_args( $args, $default ) );
  }
} else {
  ScaleUp::add_alert( array(
      'type'  => 'warning',
      'msg'   => sprintf( "new_item function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
      'debug' => true,
    )
  );
}

if ( !function_exists( 'add_template' ) ) {
  /**
   * Add a template to the site to make it available for use with get_template_part.
   *
   * @param $args
   * @return mixed
   */
  function add_template( $args ) {

    $default = array(
      'template'  => '',              # template to be used with get_template_part
      'path'      => '',              # path to the directory that contains the template
      'name'      => null,            # name of the template
    );

    $template = ScaleUp::add_template( wp_parse_args( $args, $default ) );

    return $template;
  }
} else {
  ScaleUp::add_alert( array(
    'type'  => 'warning',
    'msg'   => sprintf( "add_template function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
    'debug' => true
  ) );
}

if ( !function_exists( 'register_asset' ) ) {
  /**
   * Register an asset to make it available to the site.
   * This function combines wp_register_style & wp_register_script into one ScaleUp style register function.
   *
   * @param $args
   * @return array|bool
   */
  function register_asset( $args ) {

    $default = array(
      'name'      => '',        # handle for this asset
      'type'      => '',        # type of asset to register 'script' or 'style'
      'src'       => '',        # relative to plugins directory
      'deps'      => array(),   # dependencies that this asset request to be enqueue before
      'vers'      => '',
      'in_footer' => true,
      'media'     => 'screen',
    );

    $asset_args = ScaleUp::register_asset( wp_parse_args( $args, $default ) );

    return $asset_args;
  }
} else {
  ScaleUp::add_alert( array(
    'type'  => 'warning',
    'msg'   => sprintf( "register_asset function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
    'debug' => true
  ) );
}

if ( !function_exists( 'add_alert' ) ) {
  /**
   * Add an alert for the users, developers or other features
   *
   * Alert $type
   * -----------
   *  - success:  when everything went well
   *  - info:     when there is something that user might care about
   *  - warning:  when there is something that user must pay attention to
   *  - error:    when things went badly and you gotta report the bad news
   *
   * $args array
   * -----------
   *  - log:      use to specify that you'd like this alert to be written to the logs
   *  - debug:    mark this alert helpful to the developers to debug the app
   *
   * @param $type string severity or type of alert
   * @param $msg string of message that you'd like to share
   * @param array $args
   *
   * @return ScaleUp_Alert
   */
  function add_alert( $type, $msg, $args = array() ) {

    $default = array(
      'type'  => $type,
      'msg'   => $msg,
      'log'   => false,
      'debug' => false,
      'wrong' => 'not set' // I'm using not set because null is a value that might be passed to this arg
    );

    return ScaleUp::add_alert( wp_parse_args( $args, $default ) );
  }
} else {
  ScaleUp::add_alert( array(
    'type'  => 'warning',
    'msg'   => sprintf( "add_alert function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
    'debug' => true
  ) );
}
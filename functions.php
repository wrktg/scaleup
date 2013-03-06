<?php
/**
 * Functions in this file are meant to be used in a theme's functions.php
 */

if ( !function_exists( 'add_form' ) ) {
  /**
   * Adds a form to the site that can be displayed in templates using the_form( $form_name )
   * @see template-tags.php for more information on how to use this form in your theme
   *
   * @param $name
   * @param array $fields array @see add_form_field for array format
   *
   * @return bool $form ScaleUp_Form
   */
  function add_form( $name, $fields = array() ) {

    $name = ScaleUp::slugify( $name );

    $args = array(
      'name'        => $name,
      'form_fields' => $fields,
    );

    $site = ScaleUp::get_site();

    $form = $site->add( 'form', $args );

    return $form;
  }
} else {
  ScaleUp::add_alert( 'warning', sprintf( "add_form function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ), array( 'debug' => true ) );
}

if ( !function_exists( 'add_form_field' ) ) {
  /**
   * Add a form field to a form
   *
   * $args
   * -----
   * - type:
   *    - text:       regular input field that allows text to be entered
   *    - textarea:   text area field that allows multiline text entry
   *    - checkbox:   shows a field with many checkboxes
   *    - select:     shows a select field as a dropdown
   *    - raido:      show a group of radio buttons
   *    - html:       allows arbitrary html to be included as a field ( this is not an input field but rather a way to out information to the form )
   * - label:   field's label
   * - options: array of values to show in fields with multiple options like checkbox, radio & select
   * - help:    help information to show after the field
   *
   * @param $form
   * @param $field_name
   * @param array $args
   *
   * @return ScaleUp_Form_Field|bool
   */
  function add_form_field( $form, $field_name, $args = array() ) {

    $field = false;

    if ( !is_object( $form ) && is_string( $form ) ) {
      $form_name = ScaleUp::slugify( $form );
      $site      = ScaleUp::get_site();
      $form      = $site->get_feature( 'form', $form_name );
      if ( false === $form ) {
        ScaleUp::add_alert(
          array(
            'type'  => 'warning',
            'msg'   => "Form called $form_name could not be found on this site.",
            'debug' => true,
            'wrong' => $form_name
          ) );
      }
    }

    if ( is_object( $form ) ) {
      $args[ 'name' ] = $field_name;
      $field          = $form->add( 'form_field', $args );
      if ( false === $field ) {
        ScaleUp::add_alert(
          array(
            'type'  => 'warning',
            'msg'   => "Form field could not be added to form called $field_name.",
            'debug' => true,
            'wrong' => $args
          ) );
      }
    } else {
      ScaleUp::add_alert(
        array(
          'type'  => "warning",
          'msg'   => "Field called $field_name could not be added to the form.",
          'debug' => true,
          'wrong' => $form
        ) );
    }

    return $field;
  }
} else {
  ScaleUp::add_alert(
    array(
      'type'  => 'warning',
      'msg'   => sprintf( "add_form_field function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
      'debug' => true
    ) );
}

if ( !function_exists( 'register_schema' ) ) {
  /**
   * A schema is a soft structure that a developer can impose on a content item. A schema can have properties,
   * taxonomies and relationships. Each schema has a name that uniquely identifies it within a site.
   *
   * @todo: add documentation about format of args arrays
   *
   * @param $name
   * @param array $args
   * @return mixed
   */
  function register_schema( $name, $args = array() ) {

    $name = ScaleUp::slugify( $name );

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
          case 'property':
            $properties[ $prop_name ] = $prop_args;
            break;
          case 'taxonomy':
            $taxonomies[ $prop_name ] = $prop_args;
            break;
          case 'relationships':
            $relationships[ $prop_name ] = $prop_args;
            break;
          default:
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

    $schema = ( array ) $schema;

    $default = array(
      'schemas' => array_merge( array( 'post' ), $schema )
    );

    return ScaleUp::create_item( wp_parse_args( $args, $default ) );
  }
} else {
  ScaleUp::add_alert(
    array(
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
  ScaleUp::add_alert(
    array(
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
  ScaleUp::add_alert(
    array(
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
  ScaleUp::add_alert(
    array(
      'type'  => 'warning',
      'msg'   => sprintf( "delete_item function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
      'debug' => true,
    )
  );
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
  ScaleUp::add_alert(
    array(
      'type'  => 'warning',
      sprintf( "add_alert function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ),
      'debug' => true
    ) );
}
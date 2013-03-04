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

    $form = $site->add( 'form', $args = array() );

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

    if ( !is_object( $form ) && is_string( $form )) {
      $form_name = ScaleUp::slugify( $form );
      $site = ScaleUp::get_site();
      $form = $site->get_feature( 'form', $form_name );
      if ( false === $form ) {
        ScaleUp::add_alert( 'warning', "Form called $form_name could not be found on this site.", array( 'debug' => true, 'wrong' => $form_name ) );
      }
    }

    if ( is_object( $form ) ) {
      $args[ 'name' ] = $field_name;
      $field = $form->add( 'form_field', $args );
      if ( false === $field ) {
        ScaleUp::add_alert( 'warning', "Form field could not be added to form called $form_name.", array( 'debug' => true, 'wrong' => $args ) );
      }
    } else {
      ScaleUp::add_alert( "warning", "Field called $field_name could not be added to the form.", array( 'debug' => true, 'wrong' => $form ) );
    }

    return $field;
  }
} else {
  ScaleUp::add_alert( 'warning', sprintf( "add_form_field function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ), array( 'debug' => true ) );
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
      'type'     => $type,
      'msg'      => $msg,
      'log'      => false,
      'debug'    => false,
      'wrong'    => 'not set' // I'm using not set because null is a value that might be passed to this arg
    );

    return ScaleUp::add_alert( wp_parse_args( $args, $default ) );

  }
} else {
  ScaleUp::add_alert( 'warning', sprintf( "add_alert function could not be defined in /%s/functions.php because it was defined somewhere else first.", dirname( __FILE__ ) ), array( 'debug' => true ) );
}
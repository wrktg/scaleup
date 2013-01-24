<?php
class ScaleUp_Form {

  protected $_field_count;

  protected $_field_position = 0;

  /**
   * $context is an instance of ScaleUp_View or implements ScaleUp_Context_Interface
   * @todo: discuss with Mike Schinkel weather I should use Interface in this context
   *
   * @param $args
   * @param null $context ScaleUp_View
   */
  function __construct( $args, $context = null ) {

    if ( !is_null( $context ) && is_object( $context ) && method_exists( $context, 'get_url' ) )
      $action = $context->get_url();
    else
      $action = '';

    $default = array(
      'encoding'  => 'application/x-www-form-urlencoded',
      'action'    => $action,
      'fields'    => array(),
    );

    $args = wp_parse_args( $args, $default );

    $this->_args = $args;

    foreach ( $args as $key => $value ) {
      $this->set_attr( $key, $value );
      unset( $value );
    }

    add_action( 'scaleup_initialize', array( $this, 'initialize' ) );
  }

  function initialize() {
    register_template( SCALEUP_DIR . '/templates', '/forms/form.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/checkbox.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/help.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/label.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/password.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/text.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/textarea.php' );
    do_action( 'scaleup_forms_initialize' );
  }

  /**
   * Load arguments into the form.
   * Call this function when processing a POST request.
   *
   * @param $args
   */
  function load( $args ) {
    /**
     * @todo: write load function
     */
  }

  /**
   * Set current form into global scope
   * @return bool;
   */
  function the_form() {
    global $form, $in_form;
    $form = $this;
    $in_form = true;
    return true;
  }

  /**
   * Return a form attribute
   *
   * @param $name
   * @return mixed|null
   */
  function get_attr( $name ) {

    if ( property_exists( $this, $name ) ) {
      return $this->$name;
    }

    return null;
  }

  /**
   * Set a from attribute
   *
   * @param $name
   * @param $value
   */
  function set_attr( $name, $value ) {
    $this->$name = $value;
  }

}
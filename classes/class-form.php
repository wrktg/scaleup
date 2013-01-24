<?php
class ScaleUp_Form {

  static $_initialized = false;

  protected $_position = 0;

  /**
   * $context is an instance of ScaleUp_View or implements ScaleUp_Context_Interface
   * @todo: discuss with Mike Schinkel whether I should use Interface in this context
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
      'method'        => 'post',
      'enctype'       => '',
      'action'        => $action,
      'title'         => '',
      'before_title'  => '<h2>',
      'after_title'   => '</h2>',
      'description'   => '',
      'fields'        => array(),
    );

    $args = wp_parse_args( $args, $default );

    $this->_args = $args;

    foreach ( $args as $key => $value ) {
      $this->set( $key, $value );
      unset( $value );
    }

    if ( !self::$_initialized )
      self::initialize();

  }

  static function initialize() {
    register_template( SCALEUP_DIR . '/templates', '/forms/button.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/form.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/checkbox.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/help.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/label.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/password.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/text.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/textarea.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/confirmation.php' );
    do_action( 'initialize_scaleup_forms' );
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
   * Validate this form.
   * It must be loaded with $this->load before running this funciton
   *
   * @return bool
   */
  function validates() {
    /**
     * Do the actual heavy lifting of validating these forms.
     */
    return true;
  }

  /**
   * Set current form into global scope
   * @return bool;
   */
  function the_form() {

    /**
     * form setup
     */
    $this->_inject_nonce();
    reset( $this->_fields );

    global $scaleup_form, $in_scaleup_form;
    $scaleup_form = $this;
    $in_scaleup_form = true;

    return true;
  }

  function _inject_nonce() {
    $nonce_field = array(
      'id'    =>  '_nonce',
      'type'  => 'hidden',
      'value' => $this->get( 'action' ),
    );
    array_push( $this->_fields, $nonce_field );
  }

  /**
   * Advance field position by one and return true if next field is available, otherwise return false
   *
   * @return bool
   */
  function has_fields() {

    // don't count the first one because its nonce
    if ( 1 < count( $this->_fields ) )
      if ( 0 == $this->_position ) {
        $this->_position++;
        return true;
      } else {
        $this->_position++;
        return false !== next( $this->_fields );
      }
    return false;
  }

  /**
   * Setup field
   *
   * @return bool
   */
  function the_field() {

    global $scaleup_form_field, $in_scaleup_form_field;
    $args = current( $this->_fields );
    $scaleup_form_field = new ScaleUp_Form_Field( $args );
    $in_scaleup_form_field = true;

    return true;
  }

  /**
   * Return a form attribute
   *
   * @param $name
   * @return mixed|null
   */
  function get( $name ) {

    $method_name = "get_$name";
    if ( method_exists( $this, $method_name ) )
      return $this->$method_name( $name );

    $property_name = "_$name";
    if ( property_exists( $this, $property_name ) ) {
      return $this->$property_name;
    }

    return null;
  }

  /**
   * Set a from attribute
   *
   * @param $name
   * @param $value
   */
  function set( $name, $value ) {

    $method_name = "set_$name";
    if ( method_exists( $this, $method_name ) )
      $this->$method_name( $name, $value );

    $property_name = "_$name";
    $this->$property_name = $value;
  }

}
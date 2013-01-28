<?php
class ScaleUp_Form {

  /**
   * Flag that prevents form initialization from happening more than once
   * @var bool
   */
  static $_initialized = false;

  /**
   * Field position marker
   * @var int
   */
  protected $_position = 0;

  /**
   * Stores form's field arguments
   *
   * @var array
   */
  protected $_fields = array();

  /**
   * Stores validation errors
   *
   * @var null | WP_Error
   */
  var $_error = null;

  /**
   * $context is an instance of ScaleUp_View or implements ScaleUp_Context_Interface
   *
   * @param $name
   * @param $args
   * @param null $context ScaleUp_View
   */
  function __construct( $name, $args, $context = null ) {

    if ( !is_null( $context ) && is_object( $context ) && method_exists( $context, 'get_url' ) )
      $action = $context->get_url();
    else
      $action = '';

    $default = array(
      'name'          => $name,
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

    /**
     * Inject nonce field into fields
     */
    $this->_fields[] = array(
      'name'        => '_nonce',
      'type'        => 'hidden',
      'action'      => "$action/$name",
      'validation'  => array( 'required', 'nonce' ),
    );

    if ( !self::$_initialized )
      self::initialize();

  }

  static function initialize() {
    register_template( SCALEUP_DIR . '/templates', '/forms/button.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/form.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/form-error.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/checkbox.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/help.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/label.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/password.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/text.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/textarea.php' );
    register_template( SCALEUP_DIR . '/templates', '/forms/hidden.php' );
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

    reset( $this->_fields );
    while ( $field_args = current( $this->_fields ) ) {
      $field = new ScaleUp_Form_Field( $field_args, $this );  // create an instance of the form field
      $name = $field->get( 'name' );                          // get the name for current field
      if ( isset( $args[ $name ] ) )                          // if field value was submitted
        $field->set( 'value', $args[ $name ] );               // set the submitted field's value
      $this->_fields[ key( $this->_fields ) ] = $field;       // replace the field's argument array with instantiated object
      next( $this->_fields );                                 // advance to the next field
    }

  }

  /**
   * Validate this form. Before calling this method, use load to populate the fields
   *
   * @return bool
   */
  function validates() {

    $valid = true;
    foreach ( $this->_fields as $field ) {
      if ( is_object( $field ) && method_exists( $field, 'validates' ) ) {
        $field->set( 'valid', true );   // set default validation value to valid is true
        if ( !$field->validates() ) {
          $valid = false;
          apply_filters( 'form_errors', $this );
        }
      } else {
        $valid = false;
        /**
         * Fields at this point should not be args. If this happens, then something fishy is happening.
         * @todo: add error to the view informing the user that an error occured
         */
      }
    }

    return $valid;
  }

  /**
   * Set current form into global scope
   * @return bool;
   */
  function the_form() {

    reset( $this->_fields );

    global $scaleup_form, $in_scaleup_form;
    $scaleup_form = $this;
    $in_scaleup_form = true;

    return true;
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
    if ( is_object( $args ) ) {
      $scaleup_form_field = $args;
    } else {
      $scaleup_form_field = new ScaleUp_Form_Field( $args, $this );
      // replace arguments with instantiated object
      $this->_fields[ key( $this->_fields ) ] = $scaleup_form_field;
    }
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
    if ( method_exists( $this, $method_name ) ) {
      $this->$method_name( $value );
      return;
    }

    $property_name = "_$name";
    $this->$property_name = $value;
  }

  /**
   * Set error for this field.
   * $wp_error_params is an array of 3 values as array( $code, $message, $data )
   *
   * @param $wp_error_params array
   */
  function set_error( $wp_error_params ) {

    list( $code, $message, $data ) = $wp_error_params;

    if ( isset( $this->_error ) && is_wp_error( $this->_error ) ) {
      $this->_error->add( $code, $message, $data );
    } else {
      $this->_error = new WP_Error( $code, $message, $data );
    }

  }

}
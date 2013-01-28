<?php
class ScaleUp_Form_Field {

  /**
   * Flag that prevents form initialization from happening more than once
   * @var bool
   */
  static $_initialized = false;

  /**
   * Stores validation errors
   *
   * @var null | WP_Error
   */
  var $_error = null;

  function __construct( $args, $form ) {

    $default = array(
      'form'          => $form,
      'type'          => 'text',
      'value'         => '',
      'help'          => null,
      'label'         => null,
      'before_field'  => '',
      'after_field'   => '',
      'placeholder'   => '',
      'class'         => '',
    );

    $args = wp_parse_args( $args, $default );

    /**
     * In case user provides only id but not name or the other way around
     */
    if ( !isset( $args[ 'name' ] ) && isset( $args[ 'id' ] ) )
      $args[ 'name' ] = $args[ 'id' ];

    if ( !isset( $args[ 'id' ] ) && isset( $args[ 'name' ] ) )
      $args[ 'id' ] = $args[ 'name' ];

    foreach ( $args as $property => $value )
      $this->set( $property, $value );

    if ( !self::$_initialized ) {
      add_filter( 'form_field_validates', array( $this, 'validate_nonce' ) );
      add_filter( 'form_field_validates', array( $this, 'validate_required' ) );
      add_filter( 'form_field_validates', array( $this, 'validate_email' ) );

      /**
       * @todo: Implement validate_unique after
       */
      // add_filter( 'form_field_validates', array( $this, 'validate_unique' ) );
      self::$_initialized = true;
    }

  }

  function form_errors( $form ) {
    if ( $this->_form == $form ) {
      $form->set( 'error', array( 'validate-nonce', __( 'Nonce validation failed. What are you trying to do?' ), array() ) );
    }
  }

  /**
   * Return true if nonce is valid
   *
   * @param $field
   * @return bool
   */
  function validate_nonce( $field ) {
    $valid = $field->get( 'valid' );
    $validation = $field->get( 'validation' );
    if ( is_array( $validation ) && in_array( 'nonce', $validation ) ) {
      $passed = wp_verify_nonce( $field->get( 'value' ), $field->get( 'action' ) );
      if ( false == $passed ) {
        $field->set( 'valid', false );
        $field->set( 'error', array( 'validate-nonce', __( 'Nonce validation failed. What are you trying to do?' ), array() ) );
        add_filter( 'form_errors', array( $this, 'form_errors' ) );
      }
    }
    return $field;
  }

  /**
   * Return true if value is required and is not empty
   *
   * @param $field
   * @return bool
   */
  function validate_required( $field ) {
    $valid = $field->get( 'valid' );
    $validation = $field->get( 'validation' );
    if ( is_array( $validation ) && in_array( 'required', $validation ) ) {
      $value = $field->get( 'value' );
      if ( empty( $value ) ) {
        $field->set( 'valid', false );
        $field->set( 'error', array( 'validate-required', __( 'This field is required. Please, populate it with appropriate information.' ), array() ) );
      }
    }
    return $field;
  }

  /**
   * Return true if value is a valid email
   *
   * @param $field
   * @return bool
   */
  function validate_email( $field ) {
    $valid = $field->get( 'valid' );
    $validation = $field->get( 'validation' );
    if ( is_array( $validation ) && in_array( 'email', $validation ) ) {
      $value = $field->get( 'value' );
      if ( 1 != preg_match( '/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/', $value ) ) {
        $field->set( 'valid', false );
        $field->set( 'error', array( 'validate-email', __( "Must be a valid email." ), array() ) );
      }
    }
    return $field;
  }

  function get_nonce_value() {
    return wp_nonce_field( wp_create_nonce( $this->get( 'action' ) ) );
  }

  function validates() {
    $validation = $this->get( 'validation' );
    if ( $validation ) {
      $field = apply_filters( 'form_field_validates', $this );
      return $field->get( 'valid' );
    }
    return true;
  }

  /**
   * Return field value
   *
   * @return mixed|string|void
   */
  function get_value() {
    if ( '_nonce' == $this->_name )
      return wp_create_nonce( $this->get( 'action' ) );
    return apply_filters( 'form_field_get_value', $this->_value );
  }

  /**
   * Return a field attribute
   *
   * @param $name
   * @return mixed|null
   */
  function get( $name ) {

    $method_name = "get_$name";
    if ( method_exists( $this, $method_name ) )
      return $this->$method_name();

    $property_name = "_$name";
    if ( property_exists( $this, $property_name ) ) {
      return $this->$property_name;
    }

    return null;
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

  /**
   * Set a field attribute
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

}
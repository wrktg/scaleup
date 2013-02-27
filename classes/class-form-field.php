<?php
class ScaleUp_Form_Field extends ScaleUp_Feature {

  var $_type = 'text';

  var $_valid = true;

  function init() {

    // set the id, incase one was not specified
    if ( !$this->has( 'id' ) ) {
      $name = $this->get( 'name' );
      $this->set( 'id', "field_$name" );
    }

    // set default template incase one was not specified
    if ( !$this->has( 'template' ) ) {
      switch ( $this->get( 'type' ) ) :
        case 'text':
        case 'submit':
        case 'email':
        case 'password':
        case 'file':
        case 'hidden':
          $template = 'input';
          break;
        case 'checkbox':
          $template = 'checkbox';
          break;
        case 'button':
          $template = 'button';
          break;
        case 'textarea':
          $template = 'textarea';
          break;
        case 'radio':
          $template = 'radio';
          break;
        case 'select':
        case 'dropdown':
          $template = 'select';
          break;
        case 'html':
          $template = 'html_field';
          break;
        default:
          $template = null;
      endswitch;
      $this->set( 'template', $template );
    }

    if ( $this->has( 'validation' ) ) {
      $validations = (array)$this->get( 'validation' );
      foreach ( $validations as $validation ) {
        if ( is_callable( $validation ) ) {
          $this->add_filter( 'validation', $validation );
        } else {
          $method_name = "validate_$validation";
          if ( is_string( $validation ) && is_callable( array( $this, $method_name ) ) ) {
            $this->add_filter( 'validation', array( $this, $method_name ) );
          }
        }
      }
    }

    $this->add_action( 'register', array( $this, 'set_error_flag' ) );
    $this->add_action( 'register', array( $this, 'add_error_class' ) );
  }

  /**
   * Return a property value.
   * If value is a callable, then execute the callable and return the result
   *
   * @param $name
   * @return mixed|null
   */
  function get( $name ) {

    $property_name = "_$name";
    if ( property_exists( $this, $property_name ) ) {
      if ( is_array( $this->$property_name ) && is_callable( $this->$property_name ) ) {
        return call_user_func( $this->$property_name );
      } else {
        return $this->$property_name;
      }
    }

    return null;
  }

  /**
   * Run validation filters on this form field and return true if validation passed, otherwise return false.
   *
   * @return bool
   */
  function validates() {
    $this->apply_filters( 'validation' );

    return $this->get( 'valid' );
  }

  /**
   * Apply required validation to field
   *
   * @param $field ScaleUp_Form_Field
   * @return ScaleUp_Form_Field
   */
  function validate_required( $field ) {

    $value = $field->get( 'value' );
    if ( empty( $value ) ) {
      $field->set( 'valid', false );
      $field->register( 'alert', array(
        'type' => 'error',
        'msg'  => __( 'This field can not be empty.' )
      ) );
    }

    return $field;
  }

  /**
   * Apply email validation to field
   *
   * @param $field ScaleUp_Form_Field
   * @return ScaleUp_Form_Field
   */
  function validate_email( $field ) {

    $value = $field->get( 'value' );
    if ( 1 != preg_match( '/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/', $value ) ) {
      $field->set( 'valid', false );
      $field->register( 'alert', array(
        'type' => 'error',
        'msg'  => __( "Must be a valid email address." )
      ) );
    }

    return $field;
  }

  /**
   * Return true if nonce is valid
   *
   * @param $field ScaleUp_Form_Field
   * @return ScaleUp_Form_Field
   */
  function validate_nonce( $field ) {

    $passed = wp_verify_nonce( $field->get( 'value' ), $field->get( 'action' ) );
    if ( false == $passed ) {
      $field->set( 'valid', false );
      $form = $field->get( 'context' );
      $form->set( 'valid', false );
      $form->register( 'alert', array(
        'type' => 'error',
        'msg'  => __( 'Nonce could not be verified. What are you trying to do?' )
      ) );
    }

    return $field;
  }

  /**
   * Checks if an error alert is being registered to a feature and sets feature's errors flag to true
   *
   * This is callback function for $this->register hook.
   * errors flag when displaying forms
   *
   * @param $feature ScaleUp_Form_Field
   * @param $args array
   */
  function set_error_flag( $feature, $args = array() ) {
    if ( isset( $args[ '_feature_type' ] ) && 'alert' == $args[ '_feature_type' ] ) {
      if ( isset( $args[ 'type' ] ) && 'error' == $args[ 'type' ] ) {
        $feature->set( 'errors', true );
      }
    }
  }

  /**
   * Add error class to form field if errors flag is set to true
   *
   * @param $feature ScaleUp_Form_Field
   * @param $args array
   */
  function add_error_class( $feature, $args = array() ) {
    if ( $feature->has( 'errors' ) && true == $feature->get( 'errors' ) ) {
      $classes = array();
      $class   = $feature->get( 'class' );
      if ( is_string( $class ) ) {
        $classes = explode( ' ', $class );
      } elseif ( is_array( $class ) ) {
        $classes = $class;
      }
      if ( !in_array( 'error', $classes ) ) {
        $classes[ ] = 'error';
      }
      $feature->set( 'class', implode( ' ', $classes ) );
    }
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'form_field',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'form_field', array(
  '__CLASS__'   => 'ScaleUp_Form_Field',
  '_plural'     => 'form_fields',
  '_supports'   => array( 'templates', 'alerts' ),
  '_duck_types' => array( 'contextual' ),
  '_bundled'    => array(
    'templates' => array(
      'html_field'   => array(
        'template' => '/scaleup-form-fields/html.php',
        'path'     => SCALEUP_DIR . '/templates'
      ),
      'button'       => array(
        'template' => '/scaleup-form-fields/button.php',
        'path'     => SCALEUP_DIR . '/templates'
      ),
      'checkbox'     => array(
        'template' => '/scaleup-form-fields/checkbox.php',
        'path'     => SCALEUP_DIR . '/templates'
      ),
      'confirmation' => array(
        'template' => '/scaleup-form-fields/confirmation.php',
        'path'     => SCALEUP_DIR . '/templates'
      ),
      'help'         => array(
        'template' => '/scaleup-form-fields/help.php',
        'path'     => SCALEUP_DIR . '/templates'
      ),
      'input'        => array(
        'template' => '/scaleup-form-fields/input.php',
        'path'     => SCALEUP_DIR . '/templates'
      ),
      'label'        => array(
        'template' => '/scaleup-form-fields/label.php',
        'path'     => SCALEUP_DIR . '/templates'
      ),
      'textarea'     => array(
        'template' => '/scaleup-form-fields/textarea.php',
        'path'     => SCALEUP_DIR . '/templates'
      ),
      'select'       => array(
        'template' => '/scaleup-form-fields/select.php',
        'path'     => SCALEUP_DIR . '/templates'
      ),
      'radio'        => array(
        'template' => '/scaleup-form-fields/radio.php',
        'path'     => SCALEUP_DIR . '/templates'
      ),
    ),
  ),
) );
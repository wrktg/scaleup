<?php
class ScaleUp_Form_Field extends ScaleUp_Feature {

  var $_type = 'text';

  var $_error = false;

  function init() {

    // set the id, incase one was not specified
    if ( !$this->has( 'id' ) ) {
      $name = $this->get( 'name' );
      $this->set( 'id', "field_$name" );
    }

    // set default template incase one was not specified
    if ( !$this->has( 'template' ) ) {
      $template = $this->get_default_template();
      $this->set( 'template', $template );
    }

    if ( $this->has( 'validation' ) ) {
      $validations = (array)$this->get( 'validation' );
      foreach ( $validations as $validation ) {
        $this->add_validation( $validation );
      }
    }

    if ( $this->has( 'context' ) ) {
      $form = $this->get( 'context' );
      $form->add_filter( 'populate',  array( $this, 'populate' ) );
      $form->add_filter( 'normalize', array( $this, 'normalize' ) );
      $form->add_filter( 'validate',  array( $this, 'validate' ) );
    }

    $this->add_action( 'register', array( $this, 'add_error_class' ), 20 );
  }

  function activation() {

    /**
     * Change form action if activating a field file
     */
    if ( 'file' == $this->get( 'type' ) && $this->has( 'context' ) ) {
      $form = $this->get( 'context' );
      $form->set( 'enctype', 'multipart/form-data' );
      $form->add_action( 'store',   array( $this, 'handle_upload' ) );
    }

  }

  /**
   * Return a property value.
   * If value is a callable, then execute the callable and return the result
   * Otherwise return the value of the property
   * If property value is null
   * Return value from get_default_$name method ( if one exists )
   *
   * @param $name
   * @return mixed|null
   */
  function get( $name ) {

    $value = null;

    $property_name = "_$name";
    if ( property_exists( $this, $property_name ) ) {
      if ( is_array( $this->$property_name ) && is_callable( $this->$property_name ) ) {
        $value = call_user_func( $this->$property_name );
      } else {
        $value = $this->$property_name;
      }
    }

    /**
     * Try to get value from default getter method
     */
    if ( is_null( $value ) ) {
      $default_method = "get_default_$name";
      if ( method_exists( $this, $default_method ) ) {
        $value = $this->$default_method();
      }
    }

    return $value;
  }

  /**
   * Deal with intricacies of different field formats
   *
   * @param array $args
   * @return array
   */
  function normalize( $args = array() ) {
    $name   = $this->get( 'name' );
    if ( isset( $args[ $name ] ) ) {
      $args[ $name ] = apply_filters( 'scaleup_normalize_value', $args[ $name ], $this->get( 'format' ) );
    }

    return $args;
  }

  /**
   * Take value from $args and load it into this form field
   *
   * @param array $args
   * @return array
   */
  function populate( $args = array() ) {
    $field_name = $this->get( 'name' );
    if ( isset( $args[ $field_name ][ 'value' ] ) ) {
      $this->set( 'value', $args[ $field_name ][ 'value' ] );
    }
    return $args;
  }

  /**
   * Run validation filters on this form field and return true if validation passed, otherwise return false.
   *
   * @param $pass bool
   * @return bool
   */
  function validate( $pass ) {
    $pass = $this->apply_filters( 'validate', $pass );
    return $pass;
  }

  /**
   * Callback for form store action
   *
   * @param $args
   */
  function handle_upload( $args ) {

    $name = $this->get( 'name' );
    if ( isset( $_FILES[ $name ] ) ) {
      if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
      }
      $upload = $_FILES[ $name ];
      if ( 0 == $upload[ 'error' ] ) {
        $result = wp_handle_upload( $upload, array( 'test_form' => false ) );
        if ( isset( $result[ 'error' ] ) ) {
          ScaleUp::add_alert(
            array(
              'type'  => 'warning',
              'msg'   => $result[ 'error' ],
              'debug' => true,
            ));
        } else {
          $args[ $name ] = $result;
          $this->set( 'file', $result );
        }
      }
    }

    return $args;
  }

  /**
   * Add validation to this field
   *
   * @param $validation string|callable
   */
  function add_validation( $validation ) {
    if ( is_callable( $validation ) ) {
      $this->add_filter( 'validate', $validation );
    } else {
      $method_name = "validate_$validation";
      if ( is_string( $validation ) && is_callable( array( $this, $method_name ) ) ) {
        $this->add_filter( 'validate', array( $this, $method_name ) );
      }
    }
  }

  /**
   * Remove validation from this field
   *
   * @param $validation string|callable
   */
  function remove_validation( $validation ) {
    if ( is_callable( $validation ) ) {
      $this->remove_filter( 'validate', $validation );
    } else {
      $method_name = "validate_$validation";
      if ( is_string( $validation ) && is_callable( array( $this, $method_name ) ) ) {
        $this->remove_filter( 'validate', array( $this, $method_name ) );
      }
    }
  }

  /**
   * Apply required validation to field
   *
   * @param $pass bool
   * @return bool
   */
  function validate_required( $pass ) {

    $value = $this->get( 'value' );
    if ( '' === trim( $value ) || is_null( $value ) ) {
      $this->register( 'alert', array(
        'type' => 'error',
        'msg'  => __( 'This field can not be empty.' )
      ) );
      $pass = false;
    }

    return $pass;
  }

  /**
   * Apply email validation to field
   *
   * @param $pass bool
   * @return ScaleUp_Form_Field
   */
  function validate_email( $pass ) {

    $value = $this->get( 'value' );
    if ( !empty( $value ) && 1 != preg_match( '/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/', $value ) ) {
      $this->register( 'alert', array(
        'type' => 'error',
        'msg'  => __( "Must be a valid email address." )
      ) );
      $pass = false;
    }

    return $pass;
  }

  /**
   * Return true if nonce is valid
   *
   * @param $pass bool
   * @return bool
   */
  function validate_nonce( $pass ) {

    if ( false === wp_verify_nonce( $this->get( 'value' ), $this->get( 'action' ) ) ) {
      $error_args = array(
        'type' => 'error',
        'msg'  => __( 'Nonce could not be verified. What are you trying to do?' )
      );
      $this->register( 'alert', $error_args );
      /**
       * Nonce fields are usually hidden, so let's add this error alert to the form
       */
      $form = $this->get( 'context' );
      $form->register( 'alert', $error_args );
      $pass = false;
    }

    return $pass;
  }

  /**
   * Insert newly created
   *
   * @param $args
   * @return mixed
   */
  function update_args( $args ) {

    return $args;
  }

  /**
   * Add error class to form field if errors flag is set to true
   *
   * @param $feature ScaleUp_Form_Field
   * @param $args array
   */
  function add_error_class( $feature, $args = array() ) {
    /**
     * If an error alert is being registered then set the feature's error flag to true
     */
    if ( isset( $args[ 'type' ] ) && 'error' == $args[ 'type' ] ) {
      $feature->set( 'error', true );
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

  /**
   * Return default template for specific type
   *
   * @return null|string
   */
  function get_default_template() {
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
    return $template;
  }

  function get_default_value() {
    if ( $this->has( 'default' ) ) {
      $value = $this->get( 'default' );
    } else {
      switch( $this->get( 'type' ) ) {
        case 'checkbox':
          $value = array();
          break;
        default:
          $value = null;
      }
      switch( $this->get( 'name' ) ):
        case 'nonce':
          $value = wp_create_nonce( $this->get( 'action' ) );
          break;
      endswitch;
    }

    return $value;
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'form_field',
        'format'        => 'string',
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
      'select2'       => array(
        'template' => '/scaleup-form-fields/select2.php',
        'path'     => SCALEUP_DIR . '/templates',
        'assets'   => array(
          'select2_css' => array(
            'type' => 'style',
            'src'  => '/scaleup/templates/libraries/select2/select2.css',
          ),
          'select2_js' => array(
            'type' => 'script',
            'src'  => '/scaleup/templates/libraries/select2/select2.js',
            'deps' => array( 'jquery' )
          ),
        ),
      ),
      'radio'        => array(
        'template' => '/scaleup-form-fields/radio.php',
        'path'     => SCALEUP_DIR . '/templates'
      ),
    ),
  ),
));
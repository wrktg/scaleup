<?php
class ScaleUp_Form extends ScaleUp_Feature {

  /**
   * Points to the current form field that is used when displaying the form
   * @var ScaleUp_Form_Field
   */
  var $_current_field = null;

  var $_error = false;

  function init() {

    if ( !$this->has( 'action' ) ) {
      $this->set( 'action', $_SERVER[ 'REQUEST_URI' ] );
    }

    $this->register( 'form_field', array(
      'name'        => 'form_name',
      'type'        => 'hidden',
      'value'      => $this->get( 'name' ),
      'validation'  => array( 'required' ),
    ));

    $nonce_action = $this->get( 'action' ) . $this->get( 'name' );
    $nonce = wp_create_nonce( $nonce_action );

    $this->register( 'form_field', array(
      'name'        => 'nonce',
      'type'        => 'hidden',
      'value'       => $nonce,
      'action'      => $nonce_action,
      'validation'  => array( 'required', 'nonce' ),
    ));

  }

  /**
   * Checks if form has fields that have not been displayed.
   * If more fields are available, advance to next field and return true.
   * Otherwise, return false.
   *
   * @return bool
   */
  function has_fields() {

    $next_field = $this->next_field();
    if ( is_object( $next_field ) ) {
      $this->set( 'current_field', $next_field );
      $has_fields = true;
    } else {
      $has_fields = false;
    }

    return $has_fields;
  }

  /**
   * Return next field
   *
   * @return null|ScaleUp_Feature
   */
  function next_field() {

    $next_field = null;

    $current_field = $this->get( 'current_field' );
    $field_names = $this->_get_field_names();

    if ( is_null( $current_field ) ) {
      $next = $field_names[ 0 ];
    } else {
      $pos = array_search( $current_field->get( 'name' ), $field_names );
      if ( false !== $pos ) {
        $next_pos = $pos + 1;
        if ( isset( $field_names[ $next_pos ] ) ) {
          $next = $field_names[ $next_pos ];
        }
      }
    }

    if ( isset( $next ) ) {
      $next_field = $this->get_feature( 'form_field', $next );
    }

    return $next_field;
  }

  /**
   * Return current form field
   *
   * @return ScaleUp_Form_Field|null
   */
  function get_current_field() {
    return $this->get( 'current_field' );
  }

  /**
   * Set field by $name to be the current field
   *
   * @param null $name
   */
  function setup_field( $name = null ) {

    if ( !is_null( $name ) ) {
      $field = $this->get_feature( 'field', $name );
      $this->set( 'current_field', $field );
    }

  }

  /**
   * Take the form through the 4 form stages and return if
   *   1. validate fields
   *   2. store data
   *   3. notify users
   *   4. confirm submission
   *
   * @param array $args
   * @return bool
   */
  function process( $args = array() ) {

    $steps = array( 'populate', 'validate', 'normalize','store', 'notify', 'confirm' );

    foreach ( $steps as $step ) {
      $this->add_action( $step, array( $this, $step ) );
    }
    reset( $steps );

    while( false === $this->get( 'error' ) && ( $step = current( $steps ) ) ) {
      $this->do_action( $step, $args );
      next( $steps );
    }

    return false !== $this->get( 'error' );
  }

  /**
   * Populate form field from $args array
   *
   * @param $form ScaleUp_Form
   * @param array $args
   *
   * @return bool
   */
  function populate( $form,  $args = array() ) {

    $field_names = $form->_get_field_names();
    foreach ( $field_names as $field_name ) {
      if ( isset( $args[ $field_name ] ) ) {
        /** @var $field ScaleUp_Form_Field */
        $field = $form->get_feature( 'form_field', $field_name );
        $field->set( 'value', $args[ $field_name ] );
      }
    }

    return true;
  }

  /**
   * Return weather or not form field validates
   *
   * @param $form ScaleUp_Form
   * @param array $args
   * @return bool
   */
  function validate( $form, $args = array()) {

    $validates = true;

    $field_names = $form->_get_field_names();
    foreach ( $field_names as $field_name ) {
      /** @var $field ScaleUp_Form_Field */
      $field = $form->get_feature( 'form_field', $field_name );
      if ( !$field->validate() ) {
        $validates = false;
      }
    }

    if ( !$validates ) {
      $form->register( 'alert', array(
        'msg'  => 'Your submission did not pass validation. Please, verify your entry and resubmit.',
        'type' => 'error'
      ) );
    }

    return $validates;
  }

  /**
   * Store the submission
   *
   * @param $form ScaleUp_Form
   * @param $args array
   * @return bool
   */
  function store( $form, $args = array() ) {

    return true;
  }

  /**
   * Notify those who care
   *
   * @param $form
   * @param array $args
   * @return bool
   */
  function notify( $form, $args = array() ) {

    /**
     * @todo: need to make this hookable
     */
    $notify = $form->get( 'notify' );
    if ( is_array( $notify ) ) {
      foreach ( $notify as $notice ) {
        if ( isset( $notice[ 'method' ] ) && 'email' == $notice[ 'method' ] ) {
          $this->notify_email( $form, $notice );
        }
      }
    }

    return true;
  }

  function notify_email( $form, $args ) {

    /**
     * @todo: this needs to be redone properly, right now too hardcody
     */
    if ( isset( $args[ 'to' ] ) ) {
      $to = $args[ 'to' ];
      if ( is_callable( $to ) ) {
        $to = call_user_func( $to, $form );
      }
      wp_mail( $to, $args[ 'subject' ], $args[ 'message' ] );
    }

  }

  /**
   * Return array of field names in this form
   *
   * @return array
   */
  function _get_field_names() {

    $features = $this->get( 'features' );
    /*** @var $form_fields ScaleUp_Base */
    $form_fields = $features->get( 'form_fields' );

    return $form_fields->get_properties();
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'form',
        'template'      => 'form',
        'method'        => 'post',
        'action'        => null,
        'enctype'       => null,
        'title'         => '',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>',
        'description'   => '',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'form', array(
  '__CLASS__'   => 'ScaleUp_Form',
  '_plural'     => 'forms',
  '_supports'   => array( 'form_fields', 'templates', 'alerts' ),
  '_duck_types' => array( 'global', 'contextual' ),
  '_bundled'    => array(
    'templates' => array(
      'form' => array(
        'path'     => SCALEUP_DIR . '/templates',
        'template' => '/scaleup-form.php',
      ),
    ),
  )
) );
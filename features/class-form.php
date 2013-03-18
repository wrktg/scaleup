<?php
/**
 * Class ScaleUp_Form
 *
 */
class ScaleUp_Form extends ScaleUp_Feature {

  /**
   * Points to the current form field that is used when displaying the form
   * @var ScaleUp_Form_Field
   */
  var $_current_field = null;

  var $_error = false;

  /**
   * This flag is set to false when form fails a step in $form->process
   * @var bool
   */
  var $_continue = true;

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

    $this->register( 'form_field', array(
      'name'        => 'nonce',
      'type'        => 'hidden',
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
   *
   * @param array $args
   * @return bool
   */
  function process( $args = array() ) {

    $this->add_filter( 'process', array( $this, 'normalize' ),  20 );
    $this->add_filter( 'process', array( $this, 'populate' ),   30 );
    $this->add_filter( 'process', array( $this, 'validate' ),   40 );
    $this->add_filter( 'process', array( $this, 'store' ),      50 );
    $this->add_filter( 'process', array( $this, 'notify' ),     60 );
    $this->add_filter( 'process', array( $this, 'confirm' ),    70 );

    return $this->apply_filters( 'process', $args );
  }

  /**
   * Deal with intricacies of different value formats and convert them to standard string or array and populate the
   * array with normalized values.
   *
   * @param array $args
   * @return mixed
   */
  function normalize( $args = array() ) {
    $args = $this->apply_filters( 'normalize', $args );
    return $args;
  }

  /**
   * Populate form field from $args array
   *
   * @param array $args
   * @return array
   */
  function populate( $args = array() ) {
    $args = $this->apply_filters( 'populate', $args );
    return $args;
  }

  /**
   * Return weather or not form field validates
   *
   * @param array $args
   * @return bool
   */
  function validate( $args = array() ) {

    $pass = $this->apply_filters( 'validate', true );
    if ( false === $pass ) {
      $this->register( 'alert', array(
        'msg'  => 'Your submission did not pass validation. Please, verify your entry and resubmit.',
        'type' => 'error'
      ) );
      $this->set( 'continue', false );
    }

    return $args;
  }

  /**
   * Store the submission
   *
   * @param $args array
   * @return bool
   */
  function store( $args = array() ) {

    if ( $this->get( 'continue' ) ) {
      $args = $this->apply_filters( 'store', $args );
    }

    return $args;
  }

  /**
   * Notify those who care
   *
   * @param array $args
   * @return bool
   */
  function notify( $args = array() ) {

    if ( $this->get( 'continue' ) ) {
      $notifications = $this->get_features( 'notifications' );
      foreach ( $notifications as $notification ) {
        $this->add_filter( 'notify', array( $notification, 'issue' ) );
      }
      $args = $this->apply_filters( 'notify', $args );
    }

    return $args;
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
        'enctype'       => "application/x-www-form-urlencoded",
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
  '_supports'   => array( 'form_fields', 'templates', 'alerts', 'notifications' ),
  '_duck_types' => array( 'global', 'contextual' ),
  '_bundled'    => array(
    'templates' => array(
      'form' => array(
        'path'     => SCALEUP_DIR . '/templates',
        'template' => '/scaleup-form.php',
      ),
    ),
  ),
  '_register'     => array( 'global', 'contextual' ),
  '_activate'     => array( 'global' ),
) );
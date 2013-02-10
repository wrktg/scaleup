<?php
class ScaleUp_Forms extends ScaleUp_Contextual {

  static $_this;

  /**
   * This static variable contains an object that stores arguments for registered forms.
   * Later on, the args are lazily replaced by an instantiated form.
   *
   * @var ScaleUp_Base
   */
  static $_wp_forms;

  /**
   * This instance variable contains an object that stores arguments for forms that are registered within context.
   * A context can be either an App or an Addon.
   *
   * @var ScaleUp_Base
   */
  var $_contextual_forms;

  function __construct( $args = array() ) {
    parent::__construct( $args );

    if ( !isset( self::$_this ) ) {
      self::$_this  = $this;
      self::$_wp_forms = new ScaleUp_Base();
      do_action( 'scaleup_forms_init' );
    }

    $this->_contextual_forms = new ScaleUp_Base();

    add_action( 'init', array( $this, 'init' ) );
    add_action( 'init', array( $this, 'form_submit' ), 20 );
  }

  function init() {
    ScaleUp::register_template( SCALEUP_DIR . '/templates', '/forms/button.php' );
    ScaleUp::register_template( SCALEUP_DIR . '/templates', '/forms/form.php' );
    ScaleUp::register_template( SCALEUP_DIR . '/templates', '/forms/form-error.php' );
    ScaleUp::register_template( SCALEUP_DIR . '/templates', '/forms/checkbox.php' );
    ScaleUp::register_template( SCALEUP_DIR . '/templates', '/forms/help.php' );
    ScaleUp::register_template( SCALEUP_DIR . '/templates', '/forms/label.php' );
    ScaleUp::register_template( SCALEUP_DIR . '/templates', '/forms/password.php' );
    ScaleUp::register_template( SCALEUP_DIR . '/templates', '/forms/text.php' );
    ScaleUp::register_template( SCALEUP_DIR . '/templates', '/forms/textarea.php' );
    ScaleUp::register_template( SCALEUP_DIR . '/templates', '/forms/hidden.php' );
    ScaleUp::register_template( SCALEUP_DIR . '/templates', '/forms/confirmation.php' );
  }

  /**
   * Register a form. $args array must contain name element.
   *
   * @param $args
   * @return array|WP_Error
   */
  function register_form( $args ) {

    if ( isset( $args[ 'context' ] ) && is_object( $args[ 'context' ] ) && method_exists( $args[ 'context' ], 'get' ) ) {
      $context = $args[ 'context' ];
    } else {
      $context = self::$_wp_forms;
    }
    // check that there isn't a form with similar name already
    if ( isset( $args[ 'name' ] ) && !self::$_wp_forms->has( $args[ 'name' ] ) ) {
      self::$_wp_forms->set( $args[ 'name' ], $args );
    } else {
      return new WP_Error( 'form-duplicate', sprintf( __( '%s already registered' ), $args[ 'name' ] ) );
    }

    return $args;
  }

  /**
   * Return a form object.
   * Specify $context to get form from context ( ie. App or Addon )
   *
   * @param $name
   * @param null $context
   * @return null|ScaleUp_Form
   */
  static function get_form( $name, $context = null ) {

    /**
     * When trying to get contextual forms
     */
    if ( is_object( $context ) && method_exists( $context, 'get_form' ) ) {
      return $context->get_form( $name );
    }

    if ( self::$_wp_forms->has( $name ) ) {
      // form is stored as an array when
      $form = self::$_wp_forms->get( $name );
      if ( is_array( $form ) ) {
        /**
         * form is an array, therefore its not been initialized.
         * Let's initialize it.
         */
        $form = new ScaleUp_Form( $form );
        self::$_wp_forms->set( $name, $form );
        return $form;
      }
      if ( is_object( $form ) ) {
        return $form;
      }
    }

    return null;
  }

  function form_submit() {
    if ( isset( $_POST[ 'scaleup_form' ] ) ) {
      $form = ScaleUp::get_form( $_POST[ 'scaleup_form' ] );
      if ( !is_null( $form ) ) {
        $form->load( $_POST );
        if ( $form->validates() ) {
          do_action( 'scaleup_form_submit', $form );
        }
      }
    }
  }

}
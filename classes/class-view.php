<?php
class ScaleUp_View extends ScaleUp_Routable {

  var $_name;

  var $_url;

  var $_context;

  var $_callbacks;

  var $_args;

  var $_forms;

  function __construct( $args ) {

    parent::__construct( $args );

    add_filter( 'register_view', array( $this, 'register_view' ) );

  }

  function get_defaults() {
    return array(
      'name'      =>  '',
      'url'       =>  '',
      'callbacks' => array(),
      'context'   => null,
      'args'      => array(),
      'forms'     => array(),
    );
  }

  function initialize() {
    foreach ( $this->_forms as $name => $args ) {
      $this->_forms[ $name ] = new ScaleUp_Form( $name, $args, $this );
    }
  }

  /**
   * Callback function for register_view filter to add this view to global views
   *
   * @param $views
   */
  function register_view ( $views ) {
    if ( !isset( $views[ $this->_name ] ) )
      $views[ $this->_name ] = $this;
    return $views;
  }

  /**
   * Return form with specific name
   *
   * @param $name
   * @return bool|ScaleUp_Form
   */
  function get_form( $name ) {

    if ( isset( $this->_forms[ $name ] ) )
      return $this->_forms[ $name ];

    // lazy load the forms
    if ( isset( $this->_args[ 'forms' ][ $name ] ) && !empty( $this->_args[ 'forms' ][ $name ] )) {
      $this->_forms[ $name ] = $form = new ScaleUp_Form( $name, $this->_args[ 'forms' ][ $name ], $this );
      return $form;
    }
    return false;
  }

  /**
   * Set form with specific name
   *
   * @param $name
   * @param $form
   */
  function set_form( $name, $form ) {
    $this->_forms[ $name ] = $form;
  }

}
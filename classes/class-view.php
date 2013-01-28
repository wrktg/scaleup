<?php
class ScaleUp_View {

  protected $_slug;

  protected $_url;

  protected $_callbacks;

  protected $_context;

  protected $_args;

  protected $_forms = array();

  function __construct( $slug, $url, $callbacks, $context = null, $args = null ) {

    $this->_slug      = $slug;
    $this->_url       = $url;
    $this->_context   = $context;
    $this->_callbacks = wp_parse_args( $callbacks, array( 'GET' => null, 'POST'=> null ) );
    $this->_args      = $args;

    add_filter( 'register_route', array( $this, 'register_route' ) );
    add_filter( 'register_view', array( $this, 'register_view' ) );
  }

  /**
   * Callback function for register_route filter to add this view to routes
   *
   * @param $routes
   * @return array
   */
  function register_route( $routes ) {
    $routes[] = $this;
    return $routes;
  }

  /**
   * Callback function for register_view filter to add this view to global views
   *
   * @param $views
   */
  function register_view ( $views ) {
    if ( !isset( $views[ $this->_slug ] ) )
      $views[ $this->_slug ] = $this;
    return $views;
  }

  /**
   * Return this view's url. Without $args, this function will return url template for this view.
   *
   * @param null $args
   * @return mixed
   */
  function get_url( $args = null ) {

    $url = '';
    if ( is_null( $this->_context ) )
      $url = $this->_url;
    elseif ( is_object( $this->_context ) && method_exists( $this->_context, 'get_url' ) )
      $url = $this->_context->get_url() . $this->_url;

    if ( is_null( $args ) )
      return $url;
    else
      return scaleup_string_template( $url, $args );

  }

  /**
   * @param $method
   * @return bool
   */
  function has_callback( $method ) {
    return isset( $this->_callbacks[ $method ] );
  }

  /**
   * @param $method
   * @return bool
   */
  function get_callback( $method ) {
    if ( isset( $this->_callbacks[ $method ] ) )
      return $this->_callbacks[ $method ];
    return false;
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

  /**
   * Return a field attribute
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
   * Set a field attribute
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
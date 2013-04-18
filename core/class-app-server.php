<?php
class ScaleUp_App_Server {

  private static $_this;

  function __construct() {
    self::$_this = $this;
    if ( !is_admin() ) {
      add_filter( 'do_parse_request', array( $this, 'do_parse_request' ) );
    }
    add_action( 'scaleup_init', array( $this, 'init' ) );
  }

  static function this() {
    return self::$_this;
  }

  function init() {
    do_action( 'scaleup_app_init' );
  }

  /**
   * Callback function for do_parse_request function
   *
   * @param $continue
   * @return bool
   */
  function do_parse_request( $continue ) {
    $continue = !$this->serve_request();

    return $continue;
  }

  /**
   *
   * @return bool
   */
  function serve_request() {

    $method = $_SERVER[ 'REQUEST_METHOD' ];
    $uri    = parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH );

    switch ( $method ):
      case 'GET':
        $args = $_GET;
        break;
      case 'POST':
        $args = $_POST;
        break;
      default:
        $args = array();
    endswitch;

    /**
     * @var $feature ScaleUp_Feature
     * @var $vars array
     */
    list( $feature, $vars ) = ScaleUp_Routable::match( $uri );

    if ( is_object( $feature ) ) {
      /**
       * Routing to a template part is the most basic ScaleUp functionality.
       * So, let's do this before doing anything more complicated.
       */
      if ( $feature->has( 'get_template_part' ) && !is_null( $feature->get( 'get_template_part' ) ) ) {
        get_template_part( $feature->get( 'get_template_part' ) );
        $result = true;
      } else {
        $result = $this->route( $method, $feature, wp_parse_args( $vars, $args ) );
      }
      if ( true === $result || is_object( $result ) ) {
        /**
         * @see http://core.trac.wordpress.org/ticket/16692
         * @todo: Remove exit when 3.6 comes out with ticket 16692
         */
        exit;
      }
    }

    return false;
  }

  /**
   * Attempt to execute callback for this request
   *
   * @param $method string
   * @param $feature ScaleUp_View
   * @param $vars array
   *
   * @return bool
   */
  function route( $method, $feature, $vars ) {

    $return = false;

    if ( method_exists( $feature, 'process' ) ) {
      $request = new ScaleUp_Request( $vars );
      $request->method = $method;
      $return = $feature->process( $request );
    } else {
      $name = $feature->get( 'name' );

      $method_name = strtolower( "{$method}_{$name}" );

      $return = $this->_execute_route( $feature, $method_name, $vars );

      /**
       * If instance doesn't have a callback then its probably in the context.
       * Let's try to get it from there.
       */
      if ( false === $return && $feature->is( 'contextual' ) ) {
        $return = $this->_execute_route( $feature->get( 'context' ), $method_name, $vars );
      }
    }

    if ( is_null( $return ) ) {
      /**
       * $return might equal null when called function didn't return anything.
       * For developer's convenience, we'll consider this scenario intentional and result of successful execution.
       * to force this function to continue, the feature must return false
       */
      $return = true;
    }

    return $return;
  }

  /**
   * Execute route for a specified feature with a given method_name
   *
   * @param ScaleUp_Feature $feature
   * @param string $method_name
   * @param array $args
   * @return bool
   */
  private function _execute_route( $feature, $method_name, $args ) {

    if ( method_exists( $feature, $method_name ) ) {
      // execute route if its an object method
      $return = $feature->$method_name( $args );
    } elseif ( property_exists( $feature, $method_name ) && is_callable( $feature->$method_name ) ) {
      // execute route if its an object property that has callback as value
      $return = $feature->$method_name( $args );
    } elseif ( $feature->has( $method_name ) && is_callable( $feature->get( $method_name ) ) ) {
      $callable = $feature->get( $method_name );
      $return = call_user_func( $callable, $args );
    } else {
      $return = false;
    }

    return $return;
  }

}
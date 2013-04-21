<?php
/**
 * Class ScaleUp_Request
 *
 * @todo: make this class extend from ScaleUp_Base after ScaleUp_Base is refactored to not use $this->_$property_name
 */
class ScaleUp_Request {

  /**
   * Request method: GET, POST or null.
   * @var string
   */
  var $method = null;

  /**
   * Original args that this request is generated with
   *
   * @var array
   */
  var $vars = array();

  /**
   * Headers array that contains headers that are set for current request
   * @var array
   */
  var $headers = array();

  /**
   * Query arguments that are passed to WP_Query object
   * @var array
   */
  var $query_vars = array();

  /**
   * Contains instance of WP_Query object for this view
   * @var WP_Query
   */
  var $query = null;

  /**
   * Contains data that is passed to the template
   * @var array
   */
  var $template_data = array();

  /**
   * Name of the template part that should be rendered when rendering the template
   * @var string|null
   */
  var $template_part = null;

  function __construct( $vars = array(), $args = array() ) {

    $this->vars = $vars;

    $this->query = new WP_Query();

    if ( isset( $args[ 'template_part' ] ) ) {
      $this->template_part = $args[ 'template_part' ];
    }

    if ( isset( $args[ 'method' ] ) ) {
      $this->method = $args[ 'method' ];
    }

  }

}
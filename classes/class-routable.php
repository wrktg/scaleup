<?php
class ScaleUp_Routable extends ScaleUp_Context {

  function __construct( $name, $url, $context = null, $args = null ) {

    $default = array(
      'name'    => $name,
      'url'     => $url,
    );

    wp_parse_args( $args, $default );
    parent::__construct( $context, $args );

  }

  /**
   * Return url of the current instance
   *
   * @return string
   */
  function get_url() {
    $context = $this->get_context();
    if ( is_object( $context ) && method_exists( $context, 'get_url' ) )
      return $context->get_url() . $this->get_url();
    return $this->get_url();
  }

}
<?php
/**
 * Creates linkable objects that are aware of what they're part of.
 */
class ScaleUp_Context extends ScaleUp_Base {

  function __construct( $context, $args = null ) {

    $default = array(
      'context'   => $context,
    );

    $args = wp_parse_args( $args, $default );
    parent::__construct( $args );

  }

  function get_context() {
    return $this->get( 'context' );
  }

}
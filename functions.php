<?php

if ( !function_exists( 'register_form' ) ) {
  /**
   * Register to make it available on this site.
   *
   * @param $name string
   * @param $fields array
   * @param $args array
   * @return array|\WP_Error
   */
  function register_form( $name, $fields, $args = array() ) {
    return ScaleUp::register_form( $name, $fields, $args );
  }
}

<?php
class ScaleUp_Asset extends ScaleUp_Feature {

  function init( $feature, $args ) {
    if ( $this->has( 'context' ) ) {
      $context = $this->get( 'context' );
      $context->add_action( 'activation', array( $this, 'register' ) );
      $context->add_action( 'render', array( $this, 'enqueue' ) );
    }
  }

  /**
   * Register this asset with WordPress to be enqueued when necessary
   *
   * @param $context
   * @param array|object $args
   * @return array|void|WP_Error
   */
  function register( $context, $args ) {
    $src = plugins_url( $this->get( 'src' ) );
    switch( $this->get( 'type' ) ):
      case 'script':
        wp_register_script( $this->get( 'name' ), $src, $this->get( 'deps' ), $this->get( 'vers' ), $this->get( 'in_footer' ) );
        break;
      case 'style':
        wp_register_style( $this->get( 'name' ), $src, $this->get( 'deps' ), $this->get( 'vers' ), $this->get( 'media' ) );
        break;
    endswitch;
  }

  /**
   * Enqueue this feature when this template is rendered
   *
   * @param $feature
   * @param $args
   */
  function enqueue( $feature, $args ) {

    switch( $this->get( 'type' ) ):
      case 'script':
        wp_enqueue_script( $this->get( 'name' ) );
        break;
      case 'style':
        wp_enqueue_style( $this->get( 'name' ) );
        break;
    endswitch;
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        'type'      => 'script',    // 'script' or 'style'
        'src'       => '',          // relative to plugins directory
        'deps'      => array(),
        'vers'      => '',
        'in_footer' => true,
        'media'     => 'screen',
        '_feature_type' => 'asset',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'asset',
  array(
    '__CLASS__'     => 'ScaleUp_Asset',
    '_plural'       => 'assets',
    '_data_types'   => array( 'contextual' )
  ) );
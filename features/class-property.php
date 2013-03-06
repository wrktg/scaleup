<?php
class ScaleUp_Property extends ScaleUp_Feature {

  function activation() {
    $context = $this->get( 'context' );
    $context->add_action( 'create', array( $this, 'create' ) );
    $context->add_action( 'read', array( $this, 'read' ) );
    $context->add_action( 'update', array( $this, 'update' ) );
  }

  function read( $feature, $args ) {

  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'template',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'property', array(
  '__CLASS__' => 'ScaleUp_Property',
  '_plural'   => 'properties',
  '_duck_types'   => array( 'global', 'contextual' ),
) );
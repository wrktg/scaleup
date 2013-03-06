<?php
class ScaleUp_Relationship extends ScaleUp_Feature {

  function activation() {
    $context = $this->get( 'context' );
    $context->add_action( 'create', array( $this, 'create' ) );
    $context->add_action( 'read', array( $this, 'read' ) );
    $context->add_action( 'update', array( $this, 'update' ) );
    $context->add_action( 'delete', array( $this, 'delete' ) );
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'relationship',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'relationship', array(
  '__CLASS__' => 'ScaleUp_Relationship',
  '_plural'   => 'relationships',
  '_duck_types'   => array( 'global', 'contextual' ),
) );
<?php
class ScaleUp_Taxonomy extends ScaleUp_Feature {

  function activation() {
    $context = $this->get( 'context' );
    $context->add_action( 'create', array( $this, 'create' ) );
    $context->add_action( 'read', array( $this, 'read' ) );
    $context->add_action( 'update', array( $this, 'update' ) );
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'taxonomy',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'taxonomy', array(
  '__CLASS__'   => 'ScaleUp_Taxonomy',
  '_plural'     => 'taxonomies',
  '_duck_types' => array( 'global', 'contextual' ),
) );
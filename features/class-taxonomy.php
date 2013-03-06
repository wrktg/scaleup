<?php
class ScaleUp_Taxonomy extends ScaleUp_Feature {

  function activation() {
    $context = $this->get( 'context' );
    $context->add_action( 'create', array( $this, 'setup' ) );
    $context->add_action( 'create', array( $this, 'create' ) );
    $context->add_action( 'read',   array( $this, 'setup' ) );
    $context->add_action( 'read',   array( $this, 'read' ) );
    $context->add_action( 'update', array( $this, 'setup' ) );
    $context->add_action( 'update', array( $this, 'update' ) );
  }

  function setup( $schema, $args = array() ) {
    if ( !$this->has( 'id' ) || is_null( $this->has( 'id' ) ) ) {
      $id = (int) $args[ 'id' ];
      $this->set( 'id', $id );
    }
  }

  function create( $schema, $args = array() ) {

  }

  function read( $schema, $args = array() ) {

  }

  function update( $schema, $args = array() ) {

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
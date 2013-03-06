<?php
class ScaleUp_Schema extends ScaleUp_Feature {

  function activation() {
    /** @var $context ScaleUp_Item */
    $context = $this->get( 'context' );
    $context->add_action( 'delete', array( $this, 'delete' ) );
  }

  function read( $id ) {
    $this->do_action( 'read', $id );
  }

  function update( $id, $args ) {
    $this->do_action( 'update', $args );
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'schema',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'schema', array(
  '__CLASS__'     => 'ScaleUp_Schema',
  '_plural'       => 'schemas',
  '_supports'     => array( 'properties', 'taxonomies', 'relationships' ),
  '_duck_types'   => array( 'global', 'contextual' ),
) );
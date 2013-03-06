<?php
class ScaleUp_Item extends ScaleUp_Feature {

  function create( $args ) {

  }

  function read() {

  }

  function update( $args ) {

  }

  function delete() {

  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'item',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'item', array(
  '__CLASS__'     => 'ScaleUp_Item',
  '_plural'       => 'items',
  '_supports'     => array( 'schemas' ),
  '_duck_types'   => array( 'global' ),
) );
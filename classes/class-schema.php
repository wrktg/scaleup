<?php
class ScaleUp_Schema extends ScaleUp_Feature {
  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'schema',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'schema',
  array(
    '__CLASS__'     => 'ScaleUp_Schema',
    '_feature_type' => 'schema',
    '_plural'       => 'schemas',
    '_supports'     => array( 'properties' ),
  ) );
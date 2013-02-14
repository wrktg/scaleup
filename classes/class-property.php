<?php
class ScaleUp_Property extends ScaleUp_Feature {
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
) );
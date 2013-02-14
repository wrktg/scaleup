<?php
class ScaleUp_Form extends ScaleUp_Feature {
  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'form',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'form', array(
  '__CLASS__'     => 'ScaleUp_Form',
  '_feature_type' => 'form',
  '_plural'       => 'forms',
  '_supports'     => array( 'form_fields' ),
  'duck_types'    => array( 'contextual' ),
) );
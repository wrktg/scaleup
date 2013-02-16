<?php
class ScaleUp_Form_Field extends ScaleUp_Feature {
  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'form_field',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'form_field', array(
  '__CLASS__'     => 'ScaleUp_Form_Field',
  '_plural'       => 'form_fields',
  '_duck_types'    => array( 'contextual' ),
) );
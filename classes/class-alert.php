<?php
class ScaleUp_Alert {
  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'alert',
        'type'          => 'info',    // can also be: error, success or warning
        'loggable'      => false,
        'msg'           => '',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'alert', array(
  '__CLASS__'     => 'ScaleUp_Alert',
  '_plural'       => 'alerts',
  '_duck_types'   => array( 'contextual' ),
) );
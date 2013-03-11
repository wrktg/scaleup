<?php
class ScaleUp_Notification extends ScaleUp_Feature {

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'notification',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'notification', array(
  '__CLASS__'     => 'ScaleUp_Notification',
  '_plural'       => 'notifications',
) );
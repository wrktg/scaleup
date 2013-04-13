<?php
class ScaleUp_View extends ScaleUp_Feature {

  function get_defaults() {
    return wp_parse_args(
      array(
        'url'           => '',
        '_feature_type' => 'view',
      ), parent::get_defaults() );
  }

}

ScaleUp::register_feature_type( 'view', array(
  '__CLASS__'    => 'ScaleUp_View',
  '_plural'      => 'views',
  '_supports'    => array( 'forms', 'templates' ),
  '_duck_types'  => array( 'contextual', 'routable' ),
  'exclude_docs' => true,
) );
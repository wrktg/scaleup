<?php
class ScaleUp_App extends ScaleUp_Feature {

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'app',
      ), parent::get_defaults() );
  }

}

ScaleUp::register_feature_type( 'app', array(
  '__CLASS__'     => 'ScaleUp_App',
  '_plural'       => 'apps',
  '_supports'     => array( 'addons', 'views', 'forms', 'templates', 'alerts' ),
  '_duck_types'   => array( 'global', 'routable' ),
  'exclude_route' => true,  // do not include this url when routing
  'exclude_docs'  => true,
));
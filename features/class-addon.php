<?php
class ScaleUp_Addon extends ScaleUp_Feature {

}

ScaleUp::register_feature_type( 'addon', array(
  '__CLASS__'     => 'ScaleUp_Addon',
  '_plural'       => 'addons',
  '_supports'     => array( 'views', 'forms', 'templates', 'schemas' ),
  '_duck_types'   => array( 'global', 'contextual', 'routable' ),
  'exclude_route' => true,  // do not include this url when routing
  'exclude_docs'  => true,
  '_register'     => array( 'global', 'contextual' ),
  '_activate'     => array( 'contextual' ),
) );
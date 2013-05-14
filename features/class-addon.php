<?php
class ScaleUp_Addon extends ScaleUp_Feature {

  /**
   * Add a view to this app from $args and return its instance.
   *
   * The slug is relative to app's url and must begin with "/".
   * If you are adding a view that's intended to route to the root of the app, then set $slug to ''
   *
   * @param string $name for this view
   * @param bool|string $slug of the view ( relative to app ) or false if not routable
   * @param array $args
   * @return ScaleUp_View|bool
   */
  function add_view( $name, $slug = false, $args = array() ) {
    return $this->add( 'view', wp_parse_args( array(
        'name'          => $name,
        'url'           => $slug,
        'templates_dir' => ScaleUp_Template::find_templates_dir( $this ),
        'exclude_route' => false === $slug,
      ), $args
    ));
  }

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
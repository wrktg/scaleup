<?php
class ScaleUp_Addon extends ScaleUp_Feature {

  function activation() {

    $context = $this->get( 'context' );

    if ( method_exists( $this, 'activate_feature' ) ) {
      $context->add_action( 'activate_feature', array( $this, 'activate_feature' ) );
    }

  }

  /**
   * Add a view to the app that addon is active on and return its instance.
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
    $context = $this->get( 'context' );
    return $context->add( 'view', wp_parse_args( array(
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
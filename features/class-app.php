<?php
class ScaleUp_App extends ScaleUp_Feature {

  /**
   * Add a view to this app from $args and return its instance.
   *
   * The slug is relative to app's url and must begin with "/".
   * If you are adding a view that's intended to route to the root of the app, then set $slug to ''
   *
   * @param string $name for this view
   * @param string $slug of the view ( relative to app )
   * @param array $args
   * @return ScaleUp_View|bool
   */
  function add_view( $name, $slug = '', $args = array() ) {
    return $this->add( 'view', wp_parse_args( array(
        'name'      => $name,
        'url'       => $slug,
        'template'  => null,
    ), $args
    ));
  }

  /**
   * Add an addon by specific name to this app.
   *
   * @param string $name of the addon
   * @param array $args configuration to pass to addon
   * @return ScaleUp_Addon|bool
   */
  function add_addon( $name, $args = array() ) {
    return $this->add( 'addon', wp_parse_args( array(
      'name'  => $name,
    ), $args ));
  }

  /**
   * Add an error to this app that can be used for debugging or be presented to the user.
   *
   * @param string  $msg
   * @param string  $type options: info, error, success
   * @param array   $args
   * @return ScaleUp_Alert|bool
   */
  function add_alert( $msg, $type='info', $args = array() ) {
    return $this->add( 'alert', wp_parse_args( array(
      'type'      => $type,
      'msg'       => $msg,
      'debug'     => false,
      'loggable'  => false,
    ), $args ));
  }

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
  '_supports'     => array( 'addons', 'views', 'forms', 'alerts' ),
  '_duck_types'   => array( 'global', 'routable' ),
  'exclude_route' => true,  // do not include this url when routing
  'exclude_docs'  => true,
));
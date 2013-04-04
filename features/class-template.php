<?php
class ScaleUp_Template extends ScaleUp_Feature {

  private static $_activated = array();

  function activation() {
    $template = $this->get( 'template' );
    $tag      = "get_template_part_{$template}";
    $callback = array( $this, 'get_template_part' );
    if ( !in_array( $tag, self::$_activated ) ) {
      add_action( $tag, $callback );
      self::$_activated[] = $tag;
    }
  }

  /**
   * Callback for get_template_part function
   *
   * @param string $template
   */
  function get_template_part( $template = null ) {

    if ( is_null( $template ) ) {
      $template = $this->get( 'template' );
    }

    $paths = array(
      get_stylesheet_directory()  . $template,          // child theme
      get_template_directory()    . $template,          // parent theme
      $this->get( 'path' )        . $template,          // original
    );

    foreach ( $paths as $path ) {
      if ( $found = file_exists( $path ) ) {
        $this->do_action( 'render' );
        include $path;
        $this->do_action( 'after' );
        break;
      }
    }

    if ( !$found ) {
      ScaleUp::add_alert( array(
        'type'  => 'warning',
        'msg'   => "Template $template not found.",
        'wrong' => $template,
        'debug' => true,
      ));
    }

  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'template',
        'template'      => null,
        'path'          => null,
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'template',
  array(
    '__CLASS__'   => 'ScaleUp_Template',
    '_plural'     => 'templates',
    '_duck_types' => array( 'global' ),
    '_supports'   => array( 'assets' ),
    '_bundled'    => array(
      'assets' => array(
        'bootstrap_base'   => array(
          'type' => 'style',
          'src'  => '/scaleup/templates/libraries/bootstrap/bootstrap-base.css',
        ),
      )
    )
  ) );
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
   * @param $template
   */
  function get_template_part( $template ) {

    // check if template exists in child theme directory
    if ( is_child_theme() && file_exists( get_stylesheet_directory() . $template ) ) {
      $this->do_action( 'render' );
      include( get_stylesheet_directory() . $template );
    } elseif ( file_exists( get_template_directory() . $template ) ) {
      $this->do_action( 'render' );
      include( get_template_directory() . $template );
    } else {
      $this->do_action( 'render' );
      include( $this->get( 'path' ) . $template );
    }

  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'template',
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